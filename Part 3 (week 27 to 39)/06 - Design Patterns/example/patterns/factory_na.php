<?php

declare(strict_types=1);

abstract class Item
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    abstract public function describe(): string;
}

class Book extends Item
{
    public function describe(): string
    {
        return 'Boek: ' . $this->title;
    }
}

class Magazine extends Item
{
    public function describe(): string
    {
        return 'Tijdschrift: ' . $this->title;
    }
}

class Boardgame extends Item
{
    public function describe(): string
    {
        return 'Bordspel: ' . $this->title;
    }
}

// op één plek staat welke class bij welk type hoort
class ItemFactory
{
    public static function create(string $type, string $title): Item
    {
        return match ($type) {
            'book' => new Book($title),
            'magazine' => new Magazine($title),
            'boardgame' => new Boardgame($title),
            default => throw new InvalidArgumentException("Onbekend type: $type"),
        };
    }
}

// het formulier en de import gebruiken allebei de factory
echo ItemFactory::create('book', 'Misery')->describe() . "\n";

$row = ['magazine', 'National Geographic'];
echo ItemFactory::create($row[0], $row[1])->describe() . "\n";

// een nieuw type item betekent nu één wijziging in ItemFactory
