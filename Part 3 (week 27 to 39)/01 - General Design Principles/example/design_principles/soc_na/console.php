<?php

declare(strict_types=1);

require_once './Word.php';

// dit bestand regelt alleen de invoer en uitvoer in de terminal
$word = new Word('banana');
$attempts = 3;

while ($attempts > 0 && !$word->isGuessed()) {
    echo $word->getDisplayWord() . "\n";
    echo "Raad een letter: ";
    $letter = trim(fgets(STDIN));

    // de Word class zegt of de letter goed was, wij bepalen wat we de speler laten zien
    if ($word->guessLetter($letter)) {
        echo "Goed!\n";
    } else {
        $attempts--;
        echo "Fout, nog $attempts pogingen\n";
    }
}

echo $word->isGuessed() ? "Gewonnen!\n" : "Verloren\n";
