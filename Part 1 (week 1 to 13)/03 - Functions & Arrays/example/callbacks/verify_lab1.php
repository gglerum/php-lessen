<?php

// Controleert de uitvoer in lab/filter_step.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// myArrayFilter() is de versie uit het lab. Elk scenario wordt ook vergeleken met de echte array_filter().

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

function myArrayFilter($array, $callback)
{
    $newArray = [];
    foreach ($array as $arrayItem) {
        if ($callback($arrayItem)) {
            $newArray[] = $arrayItem;
        }
    }

    return $newArray;
}

function is5OrBigger($number)
{
    return is_numeric($number) && $number >= 5;
}

$max = 5;
$cases = [
    '1 is_numeric' => [[1, "twee", 3], 'is_numeric', '1, 3'],
    '2 eigen functie' => [[3, "twee", 7], 'is5OrBigger', '7'],
    '3 anonieme functie' => [["ed", "glenn", "jan"], function ($name) {
        return strlen($name) <= 3;
    }, 'ed, jan'],
    '4 arrow functie' => [["ed", "glenn", "jan"], fn($name) => strlen($name) <= 3, 'ed, jan'],
    '5 use' => [[3, 8, 5, 9], function ($number) use ($max) {
        return $number > $max;
    }, '8, 9'],
    '6 arrow en scope' => [[3, 8, 5, 9], fn($number) => $number > $max, '8, 9'],
];

foreach ($cases as $name => [$items, $callback, $expected]) {
    check($name, implode(", ", myArrayFilter($items, $callback)), $expected);
    check($name . ' (array_filter)', implode(", ", array_filter($items, $callback)), $expected);
}
