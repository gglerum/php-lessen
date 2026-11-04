<?php

declare(strict_types=1);

class Book
{
    // Book weet ook hoe en waar hij wordt bewaard
    private static array $storage = [];

    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function save(): void
    {
        self::$storage[] = $this;
    }

    public static function findAll(): array
    {
        return self::$storage;
    }
}

(new Book('Misery'))->save();
(new Book('It'))->save();

foreach (Book::findAll() as $book) {
    echo $book->getTitle() . "\n";
}

// willen we een database in plaats van een array, dan moeten we Book zelf aanpassen
// en een Book kun je niet meer gebruiken zonder dat hij ook een opslag meebrengt
