<?php

declare(strict_types=1);

$books = [
    ['title' => 'Harry Potter', 'author' => 'J.K. Rowling'],
    ['title' => 'It', 'author' => 'Stephen King'],
    ['title' => 'Da Vinci Code', 'author' => 'Dan Brown'],
    ['title' => 'Misery', 'author' => 'Stephen King'],
];

// de logica staat nu op één plek, de key waarop we zoeken geven we als parameter mee
function findBooks(array $books, string $field, string $value): array
{
    return array_filter($books, fn(array $book) => $book[$field] === $value);
}

echo count(findBooks($books, 'author', 'Stephen King')) . " boeken van Stephen King\n";
echo count(findBooks($books, 'title', 'It')) . " boek met de titel It\n";
