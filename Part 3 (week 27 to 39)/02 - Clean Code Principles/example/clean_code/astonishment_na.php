<?php

declare(strict_types=1);

$cart = [
    ['name' => 'Boek', 'price' => 12.5],
    ['name' => 'Pen', 'price' => 1.5],
];

// de functie doet wat de naam zegt en laat de winkelmand met rust
function getTotal(array $cart): float
{
    $total = 0.0;

    foreach ($cart as $item) {
        $total += $item['price'];
    }

    return $total;
}

// het legen van de winkelmand is een eigen handeling met een eigen naam
function clearCart(): array
{
    return [];
}

// de functie rekent alleen, wat we tonen bepaalt degene die hem aanroept
function calculateAverage(array $prices): float
{
    return array_sum($prices) / count($prices);
}

echo "Totaal: " . getTotal($cart) . "\n";
echo "Aantal items in de winkelmand: " . count($cart) . "\n";
echo "Het gemiddelde is " . calculateAverage([10.0, 20.0]) . "\n";

$cart = clearCart();
