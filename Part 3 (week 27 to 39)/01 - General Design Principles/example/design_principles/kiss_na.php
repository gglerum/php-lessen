<?php

declare(strict_types=1);

// dezelfde functie, maar nu met alleen wat nodig is
function displayWord(string $word, array $guessedLetters): string
{
    $display = '';

    for ($i = 0; $i < strlen($word); $i++) {
        // in_array() doet het zoeken voor ons
        $display .= in_array($word[$i], $guessedLetters) ? $word[$i] : '_';
    }

    return $display;
}

echo displayWord('banana', ['a']) . "\n";
