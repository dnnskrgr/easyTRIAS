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

function calculateMinutesDifference(string $targetTime): int {
    $current = new DateTime();
    $target = new DateTime($targetTime);
    $diff = $current->diff($target);

    $minutes = ($diff->h * 60) + $diff->i;

    return $target < $current ? -$minutes : $minutes;
}

function applyReplacements(string $text, array $replacementTable): string {
    foreach ($replacementTable as $search => $replace) {
        $text = str_replace($search, $replace, $text);
    }
    return $text;
}

function translateTransport(string $key, array $dictionary): string {
    return $dictionary[$key] ?? 'N/A';
}
