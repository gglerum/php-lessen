<?php

declare(strict_types=1);

// een heel klein galgje waarin invoer, spellogica en uitvoer door elkaar staan
$word = 'banana';
$guessedLetters = [];
$attempts = 3;

// deze functie doet drie dingen tegelijk: input lezen, controleren en een melding tonen
function playRound(): bool
{
    global $word, $guessedLetters, $attempts;

    echo "Raad een letter: ";
    $letter = trim(fgets(STDIN));
    $guessedLetters[] = $letter;

    if (strpos($word, $letter) === false) {
        $attempts--;
        echo "Fout, nog $attempts pogingen\n";
        return false;
    }

    echo "Goed!\n";
    return true;
}

while ($attempts > 0) {
    playRound();
}

echo "Einde van het spel\n";

// probleem: als de invoer straks niet uit de terminal komt maar uit een webformulier,
// kunnen we niets van deze code hergebruiken. De controle staat vast aan fgets() en echo
