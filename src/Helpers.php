<?php

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
