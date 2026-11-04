<?php

declare(strict_types=1);

// leden krijgen 10% korting, dit percentage is vastgelegd in het ledencontract
// we rekenen niet met een afgeronde prijs omdat de afronding pas op de factuur gebeurt
function calculateDiscount(float $price, bool $isMember): float
{
    if (!$isMember) {
        return 0.0;
    }

    return $price * 0.10;
}

function hasWordBeenGuessed(string $displayWord, string $word): bool
{
    return $displayWord === $word;
}

$price = 80.0;

// de naam van de functie zegt al wat de comment eerst zei
if (hasWordBeenGuessed('banana', 'banana')) {
    echo "Gewonnen!\n";
}

echo calculateDiscount($price, true) . "\n";
