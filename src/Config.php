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

$env = parse_ini_file(__DIR__ . '/../.env.ini', false, INI_SCANNER_TYPED);

return [
    'triasUrl' => $env['TRIAS_API_URL'],
    'triasToken' => $env['TRIAS_API_TOKEN'],
    'cacheDurationMinutes' => $env['CACHE_DURATION_MINUTES'] ?? 10,
    'cacheDir' => __DIR__ . '/../' . ($env['CACHE_DIRECTORY'] ?? 'cache'),
    'iconUrl' => $env['ICON_URL'],
];
