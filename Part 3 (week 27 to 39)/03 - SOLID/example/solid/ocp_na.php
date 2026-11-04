<?php

declare(strict_types=1);

// elk type vertelt zelf hoeveel de boete per dag is
abstract class Item
{
    abstract public function getLateFeePerDay(): float;
}

class Book extends Item
{
    public function getLateFeePerDay(): float
    {
        return 0.25;
    }
}

class Magazine extends Item
{
    public function getLateFeePerDay(): float
    {
        return 0.10;
    }
}

class Boardgame extends Item
{
    public function getLateFeePerDay(): float
    {
        return 0.50;
    }
}

// deze functie is gesloten voor wijziging: een nieuw type erbij betekent een nieuwe class,
// maar deze functie hoeft niet te veranderen
function calculateLateFee(Item $item, int $daysLate): float
{
    return $daysLate * $item->getLateFeePerDay();
}

echo calculateLateFee(new Book(), 10) . "\n";
echo calculateLateFee(new Magazine(), 10) . "\n";
echo calculateLateFee(new Boardgame(), 10) . "\n";
