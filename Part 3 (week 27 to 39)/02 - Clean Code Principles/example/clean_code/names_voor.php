<?php

declare(strict_types=1);

// wat betekenen $b, $d en $r? en wat doet calc()? Je moet de hele functie lezen om het te weten
function calc(int $d): float
{
    $r = 0.0;

    if ($d > 14) {
        $r = ($d - 14) * 0.25;
    }

    return $r;
}

$x = calc(20);

// is $f een bedrag, een aantal of een ja/nee?
$f = calc(10) > 0;

echo "Te betalen: $x\n";
