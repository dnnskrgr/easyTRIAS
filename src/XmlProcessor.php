<?php

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