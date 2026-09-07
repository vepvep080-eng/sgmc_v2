<?php
$root = dirname(__DIR__, 2);
$ar = require $root . '/lang/ar/app.php';
$fr = require $root . '/lang/fr/app.php';

function flattenKeys($array, $prefix = '') {
    $keys = [];
    foreach ($array as $key => $value) {
        $fullKey = $prefix === '' ? $key : $prefix . '.' . $key;
        if (is_array($value)) {
            $keys = array_merge($keys, flattenKeys($value, $fullKey));
        } else {
            $keys[] = $fullKey;
        }
    }
    return $keys;
}

$arKeys = flattenKeys($ar);
$frKeys = flattenKeys($fr);

$onlyAr = array_diff($arKeys, $frKeys);
$onlyFr = array_diff($frKeys, $arKeys);

file_put_contents($root . '/../keys_diff.txt', "Only in AR (deep):\n" . implode("\n", $onlyAr) . "\n\nOnly in FR (deep):\n" . implode("\n", $onlyFr));
echo "Done\n";
