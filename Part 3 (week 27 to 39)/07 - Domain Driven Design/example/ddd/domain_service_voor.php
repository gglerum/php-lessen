<?php

declare(strict_types=1);

class Member
{
    public string $name;
    public array $books = [];

    public function __construct(string $name, array $books = [])
    {
        $this->name = $name;
        $this->books = $books;
    }
}

// een boek doorgeven van het ene lid aan het andere, direct in de controller
class TransferController
{
    public function handle(Member $from, Member $to, string $book): void
    {
        // de controller kent de interne opbouw van Member en bepaalt zelf de regels
        if (!in_array($book, $from->books)) {
            echo "{$from->name} heeft $book niet\n";
            return;
        }

        $from->books = array_values(array_diff($from->books, [$book]));
        $to->books[] = $book;

        echo "$book is doorgegeven van {$from->name} aan {$to->name}\n";
    }
}

$anna = new Member('Anna', ['Misery']);
$bram = new Member('Bram');

$controller = new TransferController();
$controller->handle($anna, $bram, 'Misery');
$controller->handle($anna, $bram, 'Misery');

// de regel voor doorgeven staat in de controller en niet in het domein
