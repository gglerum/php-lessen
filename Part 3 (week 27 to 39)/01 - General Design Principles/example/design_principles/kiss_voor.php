<?php

declare(strict_types=1);

// deze functie toont het woord met streepjes voor de letters die nog niet geraden zijn
// hij werkt, maar is veel ingewikkelder dan nodig
function displayWord(string $word, array $guessedLetters): string
{
    $result = [];
    $position = 0;

    while ($position < strlen($word)) {
        $letter = $word[$position];

        // we zoeken zelf of de letter al geraden is
        $isGuessed = false;
        foreach ($guessedLetters as $guessedLetter) {
            if ($guessedLetter === $letter) {
                $isGuessed = true;
            }
        }

        if ($isGuessed === true) {
            $result[$position] = $letter;
        } else {
            $result[$position] = '_';
        }

        $position++;
    }

    // daarna plakken we de losse tekens weer aan elkaar
    $output = '';
    foreach ($result as $character) {
        $output = $output . $character;
    }

    return $output;
}

echo displayWord('banana', ['a']) . "\n";
