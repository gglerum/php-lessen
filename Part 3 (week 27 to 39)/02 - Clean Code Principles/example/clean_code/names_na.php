<?php

declare(strict_types=1);

// de namen vertellen nu wat er gebeurt, zodat we de functie niet meer hoeven te lezen
const FREE_LOAN_DAYS = 14;
const LATE_FEE_PER_DAY = 0.25;

function calculateLateFee(int $daysBorrowed): float
{
    if ($daysBorrowed <= FREE_LOAN_DAYS) {
        return 0.0;
    }

    return ($daysBorrowed - FREE_LOAN_DAYS) * LATE_FEE_PER_DAY;
}

// een boolean lees je als een vraag, net als isGameOver() en hasWordBeenGuessed() in Galgje
function isOverdue(int $daysBorrowed): bool
{
    return $daysBorrowed > FREE_LOAN_DAYS;
}

$lateFee = calculateLateFee(20);
$hasLateFee = isOverdue(10);

echo "Te betalen: $lateFee\n";
