<?php

// Controleert de uitvoer in lab/bughunt.html, voor de versie met de fout en de gecorrigeerde versie.
// Run met: php verify_lab4.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// De regels van het lab worden aan elkaar geplakt; een waarschuwing komt als "Warning: ..." in de uitvoer.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

$warnings = [];
set_error_handler(function (int $level, string $message) use (&$warnings): bool {
    $warnings[] = 'Warning: ' . $message;
    return true;
});

// scenario 1
function addShipping(int $total): int
{
    return $total + 5;
}
$total = 45;
addShipping($total);
check('1 met fout', 'Te betalen: ' . $total, 'Te betalen: 45');
$total = 45;
$total = addShipping($total);
check('1 gecorrigeerd', 'Te betalen: ' . $total, 'Te betalen: 50');

// scenario 2
function calculateTax($price)
{
    echo "BTW: " . $price * 21 / 100;
}
ob_start();
$tax = calculateTax(200);
echo "Totaal: " . (200 + $tax);
check('2 met fout', ob_get_clean(), 'BTW: 42Totaal: 200');

function calculateTaxFixed(int $price): int
{
    return $price * 21 / 100;
}
$tax = calculateTaxFixed(200);
check('2 gecorrigeerd', 'Totaal: ' . (200 + $tax), 'Totaal: 242');

// scenario 3
function calculateDiscount(): int
{
    return $price / 10;
}
$price = 80;
$discount = calculateDiscount();
check('3 met fout', 'Korting: ' . $discount, 'Korting: 0');
check('3 waarschuwing', implode('|', $warnings), 'Warning: Undefined variable $price');

function calculateDiscountFixed(int $price): int
{
    return $price / 10;
}
$discount = calculateDiscountFixed($price);
check('3 gecorrigeerd', 'Korting: ' . $discount, 'Korting: 8');
