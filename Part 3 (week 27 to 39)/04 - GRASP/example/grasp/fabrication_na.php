<?php

declare(strict_types=1);

// Book weet alleen nog wat een boek is
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

// een repository bestaat niet in een echte bibliotheek: we hebben hem verzonnen (Pure Fabrication)
// om het opslaan los te houden van Book
class BookRepository
{
    private array $storage = [];

    public function save(Book $book): void
    {
        $this->storage[] = $book;
    }

    public function findAll(): array
    {
        return $this->storage;
    }
}

$repository = new BookRepository();
$repository->save(new Book('Misery'));
$repository->save(new Book('It'));

foreach ($repository->findAll() as $book) {
    echo $book->getTitle() . "\n";
}
