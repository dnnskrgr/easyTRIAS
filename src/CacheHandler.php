<?php

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
