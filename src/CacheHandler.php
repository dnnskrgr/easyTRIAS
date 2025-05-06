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

function getCachePath(string $stopPointRef, string $cacheDir): string {
    $fileName = 'departures_' . str_replace(':', '-', $stopPointRef) . '.txt';
    return rtrim($cacheDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;
}

function readFromCache(string $stopPointRef, string $cacheDir, int $cacheDurationMinutes): ?array {
    $cachePath = getCachePath($stopPointRef, $cacheDir);

    if ($cacheDurationMinutes <= 0 || !file_exists($cachePath)) {
        return null;
    }

    $lines = file($cachePath, FILE_IGNORE_NEW_LINES);
    if (!$lines || count($lines) < 2) {
        return null;
    }

    $timestamp = new DateTime($lines[0]);
    $now = new DateTime();
    $diffMinutes = ($now->getTimestamp() - $timestamp->getTimestamp()) / 60;

    if ($diffMinutes >= $cacheDurationMinutes) {
        return null;
    }

    $response = implode("\n", array_slice($lines, 1));

    return [
        'response' => $response,
        'timestamp' => $timestamp->format('c'),
    ];
}

function writeToCache(string $stopPointRef, string $cacheDir, string $response): void {
    $cachePath = getCachePath($stopPointRef, $cacheDir);

    if (!is_dir(dirname($cachePath))) {
        mkdir(dirname($cachePath), 0777, true);
    }

    $timestamp = (new DateTime())->format('c');
    file_put_contents($cachePath, $timestamp . "\n" . $response);
}
