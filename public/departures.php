<?php
/*
 * This file is part of easyTRIAS (https://github.com/dnnskrgr/easyTRIAS).
 *
 * Copyright 2025 Dennis Krüger
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 */

$config = require_once __DIR__ . '/../src/Config.php';
$dictionaries = require_once __DIR__ . '/../src/Dictionaries.php';

require_once __DIR__ . '/../src/InputValidator.php';
require_once __DIR__ . '/../src/CacheHandler.php';
require_once __DIR__ . '/../src/TriasRequest.php';
require_once __DIR__ . '/../src/XmlProcessor.php';
require_once __DIR__ . '/../src/Helpers.php';
require_once __DIR__ . '/../src/OutputRenderer.php';

// Start runtime measurement
$scriptStart = microtime(true);

$input = validateInput($_GET);

if (!$input['valid']) {
    header('Content-Type: text/plain', true, 400);
    echo "Error:\n";
    echo implode("\n", $input['errors']);
    exit;
}

if (!$input['forceRefresh']) {
    $cacheResult = readFromCache($input['stopPointRef'], $config['cacheDir'], $config['cacheDurationMinutes']);
}

if ($cacheResult ?? null) {
    $response = $cacheResult['response'];
    $useCache = true;
    $cacheTimestamp = $cacheResult['timestamp'];
} else {
    $requestTimestamp = (new DateTime())->format('c');
    $response = sendTriasRequest($input['stopPointRef'], $config['triasToken'], $config['triasUrl']);

    writeToCache($input['stopPointRef'], $config['cacheDir'], $response);
    $useCache = false;
    $cacheTimestamp = '';
}

if ($input['format'] === 'raw') {
    renderRawXml($response);
}

$xml = parseXml($response);

if (!$xml) {
    header('Content-Type: text/plain', true, 502);
    echo "Error:\n-Failed to parse XML response from TRIAS." . "\n\nData Source: " . ($useCache == true ? 'Cache' : 'Live');
    exit;
}

if (hasTriasError($xml)) {
    header('Content-Type: text/plain', true, 502);
    echo getTriasErrorMessage($xml) . "\n\nData Source: " . ($useCache == true ? 'Cache' : 'Live');
    exit;
}

$stopEvents = $xml->xpath('//trias:StopEventResult');
$departures = [];

if ($stopEvents) {
    foreach ($stopEvents as $event) {
        // === Basic information about the stop ===
        $stopPointName  = $event->xpath('.//trias:StopPointName/trias:Text')[0] ?? 'N/A';
        $lineRaw        = $event->xpath('.//trias:PublishedLineName/trias:Text')[0] ?? 'N/A';
        $destinationRaw = $event->xpath('.//trias:DestinationText/trias:Text')[0] ?? 'N/A';
        $routeDesc      = $event->xpath('.//trias:RouteDescription/trias:Text')[0] ?? 'N/A';

        // === Time information (planned time vs. real time) ===
        $timetableTime  = $event->xpath('.//trias:TimetabledTime')[0] ?? 'N/A';
        $estimatedTime  = $event->xpath('.//trias:EstimatedTime')[0] ?? null;

        // Calculate local time (with time zone)
        $effectiveTime  = $estimatedTime ?? $timetableTime;
        $departureDate = new DateTime($effectiveTime);
        $departureDate->setTimezone(new DateTimeZone('Europe/Berlin'));

        // Calculate delay in seconds (only if real-time information is available)
        $delayInSec     = $estimatedTime
            ? (new DateTime($estimatedTime))->getTimestamp() - (new DateTime($timetableTime))->getTimestamp()
            : 0;

        // Minutes until departure (from current time)
        $minutesUntil   = calculateMinutesDifference($departureDate->format('c'));

        // Skip if departure is in the past or walking is no longer sufficient
        if ($minutesUntil <= $input['walkingMinutes']) {
            continue;
        }

        // === Determine transport type (e.g. bus, S-Bahn, long-distance train ...) ===
        $transport   = $event->xpath('.//trias:Mode/trias:PtMode')[0] ?? 'N/A';         // Main Transport Type
        $railSubmode = $event->xpath('.//trias:Mode/trias:RailSubmode')[0] ?? null;     // Sub Transport Type

        // Clarification of train types (S-Bahn / long-distance train instead of just "rail"; everything else is regional train)
        if ($transport == 'rail' && $railSubmode == 'suburbanRailway') {
            $transport = 'suburbanRailway';
        } elseif ($transport == 'rail' && $railSubmode == 'highSpeedRail') {
            $transport = 'intercityRail';
        }
            
        // === Prepare data ===

        // Sanitize line number (e.g. “ICE 123 Hannover” → “ICE 123”)
        $lineNumber = preg_replace('/\s+[A-Za-zÄÖÜäöüß].*$/u', '', (string)$lineRaw);

        // Sanitize destination if necessary (e.g. “Hannover/XYZ” → “Hannover XYZ”)
        $destination         = applyReplacements((string)$destinationRaw, $dictionaries['destinationReplacements']);

        // Translate transport types, load icon and color information
        $transportTranslated = translateTransport($transport, $dictionaries['transportLabels']);
        $iconFile            = translateTransport($transport, $dictionaries['transportIcons']);
        $transportIcon       = $config['iconUrl'] . $iconFile;
        $transportColor      = translateTransport($transport, $dictionaries['transportColors']);

        // === Add record to array ===
        $departures[] = [
            'stopPointName'         => (string)$stopPointName,
            'timetabledTime'        => (string)$timetableTime,
            'estimatedTime'         => $estimatedTime ? (string)$estimatedTime : 'N/A',
            'localTime'             => $departureDate->format('H:i'),
            'delay'                 => (int)round($delayInSec / 60),
            'minutesUntilDeparture' => $minutesUntil,
            'lineNumber'            => (string)$lineNumber,
            'destination'           => (string)$destination,
            'routeDescription'      => (string)$routeDesc,
            'transportTranslated'   => $transportTranslated,
            'transportIcon'         => $transportIcon,
            'transportColor'        => $transportColor
        ];
    }

    // === Sort records in the array by next departure (real time > scheduled time) ===
    usort($departures, function ($a, $b) {
        $aTime = $a['estimatedTime'] !== 'N/A' ? $a['estimatedTime'] : $a['timetabledTime'];
        $bTime = $b['estimatedTime'] !== 'N/A' ? $b['estimatedTime'] : $b['timetabledTime'];
        return strtotime($aTime) <=> strtotime($bTime);
    });
}

// End runtime measurement
$executionTime = microtime(true) - $scriptStart;

switch ($input['format']) {
    case 'json':
        renderJson($departures);
    case 'xml':
        renderXml($departures);
    case 'csv':
        renderCsv($departures, $input['stopPointRef']);
    case 'html':
        renderHtml($departures, $input['stopPointRef'], $useCache, $cacheTimestamp, $requestTimestamp ?? '', $executionTime);
}