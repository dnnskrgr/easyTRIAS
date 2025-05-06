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

function parseXml(string $xmlString): ?SimpleXMLElement {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlString);
    if ($xml === false) {
        return null;
    }

    $namespaces = $xml->getNamespaces(true);
    $defaultNs = $namespaces[''] ?? 'http://www.vdv.de/trias';
    $xml->registerXPathNamespace('trias', $defaultNs);

    return $xml;
}

function hasTriasError(SimpleXMLElement $xml): bool {
    $errors = $xml->xpath('//trias:ErrorMessage');
    return !empty($errors);
}

function getTriasErrorMessage(SimpleXMLElement $xml): string {
    $codeNode = $xml->xpath('//trias:ErrorMessage/trias:Code');
    $textNode = $xml->xpath('//trias:ErrorMessage/trias:Text/trias:Text');

    $code = isset($codeNode[0]) ? (string)$codeNode[0] : 'N/A';
    $text = isset($textNode[0]) ? (string)$textNode[0] : 'unknown error';

    return "Error:\n- TRIAS reports $text (Code: $code)";
}