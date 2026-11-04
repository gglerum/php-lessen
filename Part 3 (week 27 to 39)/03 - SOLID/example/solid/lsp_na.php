<?php

declare(strict_types=1);

// wat een Book en een ReferenceBook gemeen hebben, staat in Item
abstract class Item
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

// alleen een Book kan worden uitgeleend
class Book extends Item
{
    public function borrow(): string
    {
        return $this->title . ' is uitgeleend';
    }
}

// een ReferenceBook erft niet van Book, dus er is ook geen borrow() die we moeten breken
class ReferenceBook extends Item
{
}

// de functie vraagt nu om een Book, en een ReferenceBook meegeven geeft een TypeError
// in plaats van een verrassing tijdens het uitlenen
function borrowBook(Book $book): void
{
    echo $book->borrow() . "
";
}

borrowBook(new Book('Misery'));
borrowBook(new Book('It'));

$catalog = [new Book('Misery'), new ReferenceBook('Woordenboek')];

foreach ($catalog as $item) {
    echo $item->getTitle() . "\n";
}
