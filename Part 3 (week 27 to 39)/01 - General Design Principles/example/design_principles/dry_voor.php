<?php

declare(strict_types=1);

$books = [
    ['title' => 'Harry Potter', 'author' => 'J.K. Rowling'],
    ['title' => 'It', 'author' => 'Stephen King'],
    ['title' => 'Da Vinci Code', 'author' => 'Dan Brown'],
    ['title' => 'Misery', 'author' => 'Stephen King'],
];

// deze twee functies zijn bijna hetzelfde: alleen de key waarop we zoeken verschilt
function findBooksByAuthor(array $books, string $author): array
{
    $found = [];

    foreach ($books as $book) {
        if ($book['author'] === $author) {
            $found[] = $book;
        }
    }

    return $found;
}

function findBooksByTitle(array $books, string $title): array
{
    $found = [];

    foreach ($books as $book) {
        if ($book['title'] === $title) {
            $found[] = $book;
        }
    }

    return $found;
}

// stel dat we later ook op hoofdletters willen letten of een foutmelding willen tonen
// dan moeten we dat in beide functies aanpassen, en vergeten we er een
echo count(findBooksByAuthor($books, 'Stephen King')) . " boeken van Stephen King\n";
echo count(findBooksByTitle($books, 'It')) . " boek met de titel It\n";
