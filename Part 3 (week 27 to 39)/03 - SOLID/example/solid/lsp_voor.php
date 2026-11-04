<?php

declare(strict_types=1);

class Book
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function borrow(): string
    {
        return $this->title . ' is uitgeleend';
    }
}

// een naslagwerk is ook een boek, dus we laten het overerven van Book
class ReferenceBook extends Book
{
    // maar een naslagwerk mag niet mee naar huis, dus de afspraak van Book klopt niet meer
    public function borrow(): string
    {
        throw new LogicException($this->title . ' mag niet worden uitgeleend');
    }
}

// deze functie werkt voor elk Book, dus ook voor elke child van Book
function borrowAll(array $books): void
{
    foreach ($books as $book) {
        echo $book->borrow() . "\n";
    }
}

try {
    borrowAll([new Book('Misery'), new ReferenceBook('Woordenboek'), new Book('It')]);
} catch (LogicException $exception) {
    // het derde boek is nooit uitgeleend, omdat de child de afspraak van de parent breekt
    echo 'Fout: ' . $exception->getMessage() . "\n";
}
