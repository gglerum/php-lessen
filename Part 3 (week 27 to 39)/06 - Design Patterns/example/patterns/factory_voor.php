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

// op de eerste plek bepalen we welke class bij het type hoort
function createItemFromForm(string $type, string $title): Item
{
    if ($type === 'book') {
        return new Book($title);
    } elseif ($type === 'magazine') {
        return new Magazine($title);
    } elseif ($type === 'boardgame') {
        return new Boardgame($title);
    }

    throw new InvalidArgumentException("Onbekend type: $type");
}

// en op de tweede plek doen we hetzelfde nog eens, bij een import
function createItemFromImport(array $row): Item
{
    switch ($row[0]) {
        case 'book':
            return new Book($row[1]);
        case 'magazine':
            return new Magazine($row[1]);
        case 'boardgame':
            return new Boardgame($row[1]);
    }

    throw new InvalidArgumentException('Onbekend type: ' . $row[0]);
}

echo createItemFromForm('book', 'Misery')->describe() . "\n";
echo createItemFromImport(['magazine', 'National Geographic'])->describe() . "\n";

// een nieuw type item betekent dat we beide plekken moeten aanpassen, en vergeten we er één, dan gaat het mis
