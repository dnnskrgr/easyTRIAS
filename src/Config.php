<?php

$env = parse_ini_file(__DIR__ . '/../.env.ini', false, INI_SCANNER_TYPED);

return [
    'triasUrl' => $env['TRIAS_API_URL'],
    'triasToken' => $env['TRIAS_API_TOKEN'],
    'cacheDurationMinutes' => $env['CACHE_DURATION_MINUTES'] ?? 10,
    'cacheDir' => __DIR__ . '/../' . ($env['CACHE_DIRECTORY'] ?? 'cache'),
    'iconUrl' => $env['ICON_URL'],
];
