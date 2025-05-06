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

function validateInput(array $query): array {
    $errors = [];

    // read params
    $stopPointRef = $query['stopPointRef'] ?? null;
    $formatRaw = $query['format'] ?? 'html';
    $forceRefreshRaw = $query['forceRefresh'] ?? '0';
    $walkingMinutesRaw = $query['walkingMinutes'] ?? '0';

    // stopPointRef (required)
    $stopPointRef = strtolower(trim($stopPointRef));
    if (is_null($stopPointRef) || trim($stopPointRef) === '') {
        $errors[] = "- Invalid value for parameter 'stopPointRef' (input: '$stopPointRef'). Must not be empty.";
    } else {
        // check format: [2-3 letters]:[5 digits]:[1-7 digits]
        if (!preg_match('/^[a-z]{2,3}:\d{5}:\d{1,7}$/', $stopPointRef)) {
            $errors[] = "- Invalid format for parameter 'stopPointRef' (input: '$stopPointRef'). Expected format: [2-3 letters]:[5 digits]:[1-7 digits].";
        }
    }

    // format
    $validFormats = ['json', 'xml', 'csv', 'html', 'raw'];
    $format = strtolower(trim($formatRaw));
    if (!in_array($format, $validFormats)) {
        $errors[] = "- Invalid value for parameter 'format' (input: '$formatRaw'). Allowed values: " . implode(', ', $validFormats);
    }

    // forceRefresh
    $forceRefreshRaw = trim($forceRefreshRaw);
    if (!in_array($forceRefreshRaw, ['0', '1'], true)) {
        $errors[] = "- Invalid value for parameter 'forceRefresh' (input: '$forceRefreshRaw'). Allowed values: 0 or 1";
    } else {
        $forceRefresh = $forceRefreshRaw === '1';
    }

    // walkingMinutes
    $walkingMinutesRaw = trim($walkingMinutesRaw);
    if (!ctype_digit($walkingMinutesRaw)) {
        $errors[] = "- Invalid value for parameter 'walkingMinutes' (input: '$walkingMinutesRaw'). Must be a non-negative integer.";
    } else {
        $walkingMinutes = (int)$walkingMinutesRaw;
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'stopPointRef' => $stopPointRef,
        'format' => $format,
        'forceRefresh' => $forceRefresh ?? false,
        'walkingMinutes' => $walkingMinutes ?? 0
    ];
}
