<?php

declare(strict_types=1);

class Catalog
{
    private array $books = [
        ['title' => 'Carrie', 'author' => 'Stephen King'],
        ['title' => 'Da Vinci Code', 'author' => 'Dan Brown'],
        ['title' => 'Harry Potter', 'author' => 'J.K. Rowling'],
    ];

    // elke nieuwe manier van sorteren betekent dat we deze method moeten aanpassen
    public function getTitles(string $sortBy): array
    {
        $books = $this->books;

        if ($sortBy === 'title') {
            usort($books, fn(array $a, array $b) => $a['title'] <=> $b['title']);
        } elseif ($sortBy === 'author') {
            usort($books, fn(array $a, array $b) => $a['author'] <=> $b['author']);
        }

        return array_column($books, 'title');
    }
}

$catalog = new Catalog();
echo implode(', ', $catalog->getTitles('title')) . "\n";
echo implode(', ', $catalog->getTitles('author')) . "\n";
