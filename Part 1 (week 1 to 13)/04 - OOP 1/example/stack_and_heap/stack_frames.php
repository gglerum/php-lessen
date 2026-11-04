<?php

declare(strict_types=1);

// elke echo laat zien wanneer een stack frame wordt gemaakt (push) en weggegooid (pop)
function calculateTotal(int $price): int
{
    echo "  push: calculateTotal(price = $price)\n";
    $tax = addTax($price);
    $total = $price + $tax;
    echo "  pop:  calculateTotal geeft $total terug\n";

    return $total;
}

function addTax(int $price): int
{
    echo "    push: addTax(price = $price)\n";
    $tax = intdiv($price * 21, 100);
    echo "    pop:  addTax geeft $tax terug\n";

    return $tax;
}

echo "push: main\n";
$price = 100;
$total = calculateTotal($price);
echo "pop:  main is klaar, total = $total\n\n";

// debug_print_backtrace() toont de stack op het moment dat je hem aanroept
// de bovenste regel is het frame dat op dit moment draait
function showStack(): void
{
    debug_print_backtrace();
}

function second(): void
{
    showStack();
}

function first(): void
{
    second();
}

first();
