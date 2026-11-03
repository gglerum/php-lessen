<?php

declare(strict_types=1);

// met alleen functies staan de data (het saldo) en de functies die er iets mee doen los van elkaar
$balance = 100;

// de functie weet niet bij welke rekening hij hoort, dus we moeten het saldo steeds meegeven
function deposit(int $balance, int $amount): int
{
    return $balance + $amount;
}

function withdraw(int $balance, int $amount): int
{
    // we controleren hier of er genoeg saldo is
    if ($amount > $balance) {
        echo "Onvoldoende saldo\n";
        return $balance;
    }

    return $balance - $amount;
}

$balance = deposit($balance, 50);
$balance = withdraw($balance, 30);
echo "Saldo: $balance\n";

// probleem 1: niets beschermt het saldo. Iedereen kan het rechtstreeks aanpassen
// en dan wordt de controle in withdraw() gewoon overgeslagen
$balance = -500;
echo "Saldo: $balance\n";

// probleem 2: een tweede rekening betekent een tweede variabele met een nieuwe naam
$balance = 100;
$balanceOfSecondAccount = 20;

// we moeten zelf onthouden welk saldo bij welke rekening hoort
$balanceOfSecondAccount = deposit($balanceOfSecondAccount, 10);
$balance = withdraw($balance, 10);

echo "Saldo rekening 1: $balance\n";
echo "Saldo rekening 2: $balanceOfSecondAccount\n";

// probleem 3: voeg een rekeninghouder, een rekeningnummer en een limiet toe en elke functie
// heeft steeds meer parameters nodig. Het wordt lastig te zien welke functie bij welke data hoort.
