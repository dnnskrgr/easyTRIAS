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

function renderJson(array $departures): void {
    header('Content-Type: application/json');
    echo json_encode($departures, JSON_PRETTY_PRINT);
    exit;
}

function renderXml(array $departures): void {
    header('Content-Type: application/xml');
    $xml = new SimpleXMLElement('<Departures/>');
    foreach ($departures as $entry) {
        $item = $xml->addChild('Departure');
        foreach ($entry as $key => $value) {
            $item->addChild($key, htmlspecialchars($value));
        }
    }
    echo $xml->asXML();
    exit;
}

function renderCsv(array $departures, string $stopPointRef): void {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="easyTRIAS_' . str_replace(':', '-', $stopPointRef) . '_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // UTF-8 BOM for Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    if (!empty($departures)) {
        fputcsv($output, array_keys($departures[0]));
        foreach ($departures as $entry) {
            fputcsv($output, $entry);
        }
    }
    fclose($output);
    exit;
}

function renderHtml(array $departures, string $stopPointRef, bool $useCache, string $cacheTimestamp, string $requestTimestamp, float $executionTime): void {
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Timetable Results</h1>
    <p>Stop Point Ref: ' . htmlspecialchars($stopPointRef) . '</p>
    <p>Data Source: ' . ($useCache ? 'Cache (' . $cacheTimestamp . ')' : 'Live (' . $requestTimestamp . ')') . '</p>
    <p>Duration: ' . number_format($executionTime, 3) . ' Seconds</p>
    <table>
        <thead>
            <tr>
                <th>Stop Point Name</th>
                <th>Timetabled Time</th>
                <th>Estimated Time</th>
                <th>Local Time</th>
                <th>Delay in Minutes</th>
                <th>Minutes until Departure</th>
                <th>Line Number</th>
                <th>Destination</th>
                <th>Route Description</th>
                <th>Transport Translated</th>
                <th>Transport Icon</th>
                <th>Transport Color</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($departures as $departure) {
        echo '<tr>
            <td>' . htmlspecialchars($departure['stopPointName']) . '</td>
            <td>' . htmlspecialchars($departure['timetabledTime']) . '</td>
            <td>' . htmlspecialchars($departure['estimatedTime']) . '</td>
            <td>' . htmlspecialchars($departure['localTime']) . '</td>
            <td>' . htmlspecialchars($departure['delay']) . '</td>
            <td>' . htmlspecialchars($departure['minutesUntilDeparture']) . '</td>
            <td>' . htmlspecialchars($departure['lineNumber']) . '</td>
            <td>' . htmlspecialchars($departure['destination']) . '</td>
            <td>' . htmlspecialchars($departure['routeDescription']) . '</td>
            <td>' . htmlspecialchars($departure['transportTranslated']) . '</td>
            <td><center><img src="' . htmlspecialchars($departure['transportIcon']) . '" height=32 width=32/></center></td>
            <td style="color: #ffffff" bgcolor="' . htmlspecialchars($departure['transportColor']) . '"><center>' . htmlspecialchars($departure['transportColor']) . '</center></td>
        </tr>';
    }
    echo '</tbody>
    </table>
</body>
</html>';
    exit;
}

function renderRawXml(string $response): void {
    header('Content-Type: application/xml');
    echo $response;
    exit;
}

function renderUnsupportedFormat(string $format): void {
    header('Content-Type: text/plain', true, 400);
    echo "Error:\n-Output format '$format' is not supported.\nAllowed: json, xml, csv, html, raw.";
    exit;
}