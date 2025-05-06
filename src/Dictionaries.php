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

return [
    'destinationReplacements' => [
        ', Bahnhof' => '',
        ' Bahnhof' => '',
        ', Hauptbahnhof' => ' Hbf',
        'Hannover/' => 'Hannover ',
    ],

    'transportLabels' => [
        'unknown'          => 'Unbekannt',
        'air'              => 'Flugzeug',
        'bus'              => 'Bus',
        'trolleyBus'       => 'O-Bus',
        'tram'             => 'Tram',
        'coach'            => 'Reisebus',
        'rail'             => 'Regionalbahn',
        'suburbanRailway'  => 'S-Bahn',
        'intercityRail'    => 'Fernbahn',
        'urbanRail'        => 'Stadtbahn',
        'metro'            => 'U-Bahn',
        'water'            => 'Fähre',
        'cableway'         => 'Seilbahn',
        'funicular'        => 'Standseilbahn',
        'taxi'             => 'Taxi',
    ],

    'transportIcons' => [
        'unknown'          => '',
        'air'              => '',
        'bus'              => 'bus.svg',
        'trolleyBus'       => 'bus.svg',
        'tram'             => 'trm.svg',
        'coach'            => '',
        'rail'             => 'reg.svg',
        'suburbanRailway'  => 'sur.svg',
        'intercityRail'    => 'ice.svg',
        'urbanRail'        => 'sub.svg',
        'metro'            => 'sub.svg',
        'water'            => 'fry.svg',
        'cableway'         => '',
        'funicular'        => '',
        'taxi'             => '',
    ],

    'transportColors' => [
        'unknown'          => '',
        'air'              => '',
        'bus'              => '#95276E',
        'trolleyBus'       => '#95276E',
        'tram'             => '#BE1414',
        'coach'            => '',
        'rail'             => '#585757',
        'suburbanRailway'  => '#439844',
        'intercityRail'    => '#E63556',
        'urbanRail'        => '#115D91',
        'metro'            => '#115D91',
        'water'            => '#528DBA',
        'cableway'         => '',
        'funicular'        => '',
        'taxi'             => '',
    ],
];
