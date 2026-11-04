<?php

declare(strict_types=1);

// functie om de korting te berekenen
function calculateDiscount(float $price, bool $isMember): float
{
    // maak variabele discount aan en zet op 0
    $discount = 0.0;

    // als de klant lid is
    if ($isMember) {
        // 10 procent van de prijs
        $discount = $price * 0.10;
    }

    // geef discount terug
    return $discount;
}

$price = 80.0;
$displayWord = 'banana';
$word = 'banana';

// controleer of het woord geraden is
if ($displayWord === $word) {
    echo "Gewonnen!\n";
}

echo calculateDiscount($price, true) . "\n";
