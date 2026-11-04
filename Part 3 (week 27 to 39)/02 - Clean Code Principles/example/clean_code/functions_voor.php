<?php

declare(strict_types=1);

// vier niveaus ifs: je moet onthouden onder welke voorwaarden je zit om te weten wanneer iets gebeurt
function borrowBook(array $book, int $age, bool $isMember): string
{
    if ($book['available']) {
        if ($isMember) {
            if ($age >= $book['ageRating']) {
                return 'Uitgeleend';
            } else {
                return 'Te jong voor dit boek';
            }
        } else {
            return 'Alleen voor leden';
        }
    } else {
        return 'Niet beschikbaar';
    }
}

// een bool als parameter draait het gedrag van de functie om, wat is `true` hier?
function showBook(array $book, bool $short): void
{
    if ($short) {
        echo $book['title'] . "\n";
    } else {
        echo $book['title'] . ' (' . $book['pages'] . " pagina's)\n";
    }
}

$book = ['title' => 'Misery', 'pages' => 320, 'ageRating' => 16, 'available' => true];

echo borrowBook($book, 15, true) . "\n";
showBook($book, true);
