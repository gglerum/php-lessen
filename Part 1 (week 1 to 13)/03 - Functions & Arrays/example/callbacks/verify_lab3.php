<?php

// Controleert de uitvoer in lab/bughunt.html, voor de versie met de fout en de gecorrigeerde versie.
// Run met: php verify_lab3.php (vanuit deze map). Alle regels moeten "ok" zeggen.

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

$warnings = [];
set_error_handler(function (int $level, string $message) use (&$warnings): bool {
    $warnings[] = $message;
    return true;
});

// scenario 1
$numbers = [3, 8, 9];
$max = 5;
$filtered = myArrayFilter($numbers, function ($number) {
    return $number > $max;
});
check('1 met fout', "Groter dan 5: " . implode(", ", $filtered), 'Groter dan 5: 3, 8, 9');
check('1 waarschuwing', implode('|', array_unique($warnings)), 'Undefined variable $max');

$filtered = myArrayFilter($numbers, function ($number) use ($max) {
    return $number > $max;
});
check('1 gecorrigeerd', "Groter dan 5: " . implode(", ", $filtered), 'Groter dan 5: 8, 9');

// scenario 2
$names = ["ed", "glenn", "jan"];
$filtered = myArrayFilter($names, function ($name) {
    strlen($name) <= 3;
});
check('2 met fout', "Korte namen: " . implode(", ", $filtered), 'Korte namen: ');

$filtered = myArrayFilter($names, function ($name) {
    return strlen($name) <= 3;
});
check('2 gecorrigeerd', "Korte namen: " . implode(", ", $filtered), 'Korte namen: ed, jan');

// scenario 3
$items = [1, "twee", 3];
try {
    $filtered = myArrayFilter($items, is_numeric);
    $message = 'geen Error';
} catch (Error $error) {
    $message = $error->getMessage();
}
check('3 met fout', $message, 'Undefined constant "is_numeric"');

$filtered = myArrayFilter($items, 'is_numeric');
check('3 gecorrigeerd', "Getallen: " . implode(", ", $filtered), 'Getallen: 1, 3');
