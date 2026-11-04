<?php

declare(strict_types=1);

// een anemic model: de entity heeft alleen getters en setters, zonder gedrag
class Book
{
    private string $title;
    private bool $available = true;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }
}

// alle regels staan in een service
class LendingService
{
    public function lend(Book $book, string $member): string
    {
        if (!$book->isAvailable()) {
            return $book->getTitle() . ' is niet beschikbaar';
        }

        $book->setAvailable(false);

        return $book->getTitle() . " is uitgeleend aan $member";
    }
}

$book = new Book('Misery');

echo (new LendingService())->lend($book, 'Anna') . "\n";
echo (new LendingService())->lend($book, 'Bram') . "\n";

// maar niets houdt andere code tegen om het boek ook zonder de service uit te lenen
$book->setAvailable(true);
echo (new LendingService())->lend($book, 'Chris') . "\n";
