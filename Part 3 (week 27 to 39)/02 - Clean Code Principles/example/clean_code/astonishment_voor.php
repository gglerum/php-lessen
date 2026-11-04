<?php

declare(strict_types=1);

$cart = [
    ['name' => 'Boek', 'price' => 12.5],
    ['name' => 'Pen', 'price' => 1.5],
];

// de naam belooft een getal, maar de functie leegt ook de winkelmand
function getTotal(array &$cart): float
{
    $total = 0.0;

    foreach ($cart as $item) {
        $total += $item['price'];
    }

    $cart = [];

    return $total;
}

// de naam zegt "calculate", maar de functie toont ook iets op het scherm
function calculateAverage(array $prices): float
{
    $average = array_sum($prices) / count($prices);
    echo "Het gemiddelde is $average\n";

    return $average;
}

echo "Totaal: " . getTotal($cart) . "\n";
echo "Aantal items in de winkelmand: " . count($cart) . "\n";
calculateAverage([10.0, 20.0]);
