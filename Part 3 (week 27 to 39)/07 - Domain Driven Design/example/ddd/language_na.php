<?php

declare(strict_types=1);

// de code gebruikt dezelfde woorden als de bibliothecaris: een Member leent een Book

class Book
{
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

class Member
{
    private string $name;
    private array $borrowedBooks = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function borrow(Book $book): void
    {
        $this->borrowedBooks[] = $book;
    }

    public function countBorrowedBooks(): int
    {
        return count($this->borrowedBooks);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

$member = new Member('Anna');
$member->borrow(new Book('Misery'));

echo $member->getName() . ' heeft ' . $member->countBorrowedBooks() . " boek(en) geleend\n";

// een nieuwe collega of de bibliothecaris zelf kan de code lezen zonder woorden te vertalen
