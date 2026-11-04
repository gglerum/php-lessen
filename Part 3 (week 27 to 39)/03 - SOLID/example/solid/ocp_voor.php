<?php

declare(strict_types=1);

class Book
{
}

class Magazine
{
}

class Boardgame
{
}

// elk nieuw type item betekent dat we deze functie moeten aanpassen
function calculateLateFee(object $item, int $daysLate): float
{
    if ($item instanceof Book) {
        return $daysLate * 0.25;
    } elseif ($item instanceof Magazine) {
        return $daysLate * 0.10;
    } elseif ($item instanceof Boardgame) {
        return $daysLate * 0.50;
    }

    // vergeten we een type, dan gaat het hier pas tijdens het draaien mis
    throw new InvalidArgumentException('Onbekend type item');
}

echo calculateLateFee(new Book(), 10) . "\n";
echo calculateLateFee(new Magazine(), 10) . "\n";
echo calculateLateFee(new Boardgame(), 10) . "\n";
