<?php

declare(strict_types=1);

// de functie krijgt een kopie van de waarde in $balance
function addBonus(int $balance): int
{
    $balance = $balance + 50;
    echo "In de functie: $balance\n";

    return $balance;
}

$balance = 100;
addBonus($balance);

// de variabele buiten de functie is niet veranderd, de functie werkte op een kopie
echo "Buiten de functie: $balance\n";

// wil je het nieuwe saldo gebruiken, dan moet je de return waarde opslaan
$balance = addBonus($balance);
echo "Na het opslaan van de return waarde: $balance\n";
