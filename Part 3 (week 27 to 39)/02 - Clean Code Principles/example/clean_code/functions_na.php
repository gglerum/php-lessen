<?php

declare(strict_types=1);

// met early returns handelen we elk uitzonderingsgeval meteen af en blijft het gewone pad over
function borrowBook(array $book, int $age, bool $isMember): string
{
    if (!$book['available']) {
        return 'Niet beschikbaar';
    }

    if (!$isMember) {
        return 'Alleen voor leden';
    }

    if ($age < $book['ageRating']) {
        return 'Te jong voor dit boek';
    }

    return 'Uitgeleend';
}

// twee functies met een duidelijke naam in plaats van een bool die het gedrag omdraait
function showBookTitle(array $book): void
{
    echo $book['title'] . "\n";
}

function showBookDetails(array $book): void
{
    echo $book['title'] . ' (' . $book['pages'] . " pagina's)\n";
}

$book = ['title' => 'Misery', 'pages' => 320, 'ageRating' => 16, 'available' => true];

echo borrowBook($book, 15, true) . "\n";
showBookTitle($book);
