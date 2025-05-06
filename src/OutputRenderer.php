<?php
/*
 * This file is part of the easyTRIAS project (https://github.com/dnnskrgr/easytrias-php-proxy).
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
    <title>Departures</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        td.center { text-align: center; }
    </style>
</head>
<body>
    <h1>Departures</h1>
    <p>Stop Point Ref: ' . htmlspecialchars($stopPointRef) . '</p>
    <p>Data Source: ' . ($useCache ? 'Cache (' . $cacheTimestamp . ')' : 'Live (' . $requestTimestamp . ')') . '</p>
    <p>Duration: ' . number_format($executionTime, 3) . ' Seconds</p>';

    if (empty($departures)) {
        echo '<p><em>No departures found.</em></p>';
    } else {
        echo '<table>
            <thead>
                <tr>';
        foreach (array_keys($departures[0]) as $key) {
            echo '<th>' . htmlspecialchars($key) . '</th>';
        }
        echo '</tr>
            </thead>
            <tbody>';

        foreach ($departures as $departure) {
            echo '<tr>';
            foreach ($departure as $key => $value) {
                if ($key === 'transportIcon') {
                    echo '<td class="center"><img src="' . htmlspecialchars($value) . '" width="32" height="32" alt="icon" /></td>';
                } elseif ($key === 'transportColor') {
                    echo '<td class="center" style="color: #fff; background-color: ' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</td>';
                } else {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
            }
            echo '</tr>';
        }

        echo '</tbody>
        </table>';
    }

    echo '</body></html>';
    exit;
}

function renderRawXml(string $response): void {
    header('Content-Type: application/xml');
    echo $response;
    exit;
}