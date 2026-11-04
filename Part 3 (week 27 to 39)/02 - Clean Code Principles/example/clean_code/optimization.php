<?php

declare(strict_types=1);

const NUMBER_OF_LOOPS = 5_000_000;

// de gewone manier om even getallen te tellen
$start = microtime(true);
$evenNumbers = 0;
for ($i = 0; $i < NUMBER_OF_LOOPS; $i++) {
    if ($i % 2 === 0) {
        $evenNumbers++;
    }
}
$readableTime = microtime(true) - $start;

// de "slimme" manier met een bitwise operator, waarvan we hopen dat hij sneller is
$start = microtime(true);
$evenNumbersTrick = 0;
for ($i = 0; $i < NUMBER_OF_LOOPS; $i++) {
    if (($i & 1) === 0) {
        $evenNumbersTrick++;
    }
}
$trickTime = microtime(true) - $start;

echo "Met % 2:  $evenNumbers even getallen in " . round($readableTime, 3) . " seconden\n";
echo "Met & 1:  $evenNumbersTrick even getallen in " . round($trickTime, 3) . " seconden\n";

// de truc is relatief sneller, maar bij vijf miljoen keer lopen gaat het om een fractie van een seconde,
// terwijl een enkele database query al veel meer tijd kost
// de trucs maken de code wel lastiger te lezen, dus alleen optimaliseren als je hebt gemeten dat het nodig is
