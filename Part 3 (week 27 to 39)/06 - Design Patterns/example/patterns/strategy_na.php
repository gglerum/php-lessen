<?php

declare(strict_types=1);

// de afspraak: een manier van sorteren krijgt een lijst boeken en geeft ze gesorteerd terug
interface SortStrategy
{
    public function sort(array $books): array;
}

class SortByTitle implements SortStrategy
{
    public function sort(array $books): array
    {
        usort($books, fn(array $a, array $b) => $a['title'] <=> $b['title']);

        return $books;
    }
}

class SortByAuthor implements SortStrategy
{
    public function sort(array $books): array
    {
        usort($books, fn(array $a, array $b) => $a['author'] <=> $b['author']);

        return $books;
    }
}

class Catalog
{
    private array $books = [
        ['title' => 'Carrie', 'author' => 'Stephen King'],
        ['title' => 'Da Vinci Code', 'author' => 'Dan Brown'],
        ['title' => 'Harry Potter', 'author' => 'J.K. Rowling'],
    ];

    // Catalog weet niet hoe er gesorteerd wordt, een nieuwe manier is een nieuwe class
    public function getTitles(SortStrategy $strategy): array
    {
        return array_column($strategy->sort($this->books), 'title');
    }
}

$catalog = new Catalog();
echo implode(', ', $catalog->getTitles(new SortByTitle())) . "\n";
echo implode(', ', $catalog->getTitles(new SortByAuthor())) . "\n";

// een callback uit les 03 is een Strategy in het klein: voor een sorteerfunctie van één regel
// is een closure meestal genoeg, een class gaat pas lonen als de strategy meer doet of meer afhankelijkheden heeft
