<?php

// Controleert de vooraf bepaalde antwoorden van lab/predict_output.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab2.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// Een PHP waarschuwing wordt opgevangen en achter de uitvoer gezet.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

$warnings = [];
set_error_handler(function (int $level, string $message) use (&$warnings): bool {
    $warnings[] = $message;
    return true;
});

// scenario 1
$items = [1, "twee", 3, "vier"];
$filtered = array_filter($items, 'is_numeric');
check('1', implode(", ", $filtered), '1, 3');

// scenario 2
$names = ["ed", "", "jan"];
$filtered = array_filter($names, 'strlen');
check('2', implode(", ", $filtered), 'ed, jan');

// scenario 3
$max = 5;
$numbers = [3, 8, 9];
$filtered = array_filter($numbers, function ($number) {
    return $number > $max;
});
check('3 uitvoer', implode(", ", $filtered), '3, 8, 9');
check('3 waarschuwing', $warnings[0] ?? 'geen', 'Undefined variable $max');

// scenario 4
$filtered = array_filter($numbers, fn($number) => $number > $max);
check('4', implode(", ", $filtered), '8, 9');

// scenario 5
$names = ["ed", "glenn", "jan"];
$filtered = array_filter($names, function ($name) {
    strlen($name) <= 3;
});
check('5', implode(", ", $filtered), '');

// scenario 6
$items = [1, "twee", 3, "vier", 5];
$filtered = array_filter($items, function ($item, $key) {
    return is_numeric($item) && $key % 2 == 0;
}, ARRAY_FILTER_USE_BOTH);
check('6', implode(", ", $filtered), '1, 3, 5');
