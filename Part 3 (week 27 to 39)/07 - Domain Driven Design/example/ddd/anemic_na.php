<?php

declare(strict_types=1);

// een rijk model: Book kent zijn eigen regels en bewaakt zijn eigen toestand
class Book
{
    private string $title;
    private ?string $borrowedBy = null;

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
        return $this->borrowedBy === null;
    }

    // de regel zit in de entity, dus elke aanroeper gebruikt dezelfde regel
    public function lendTo(string $member): void
    {
        if (!$this->isAvailable()) {
            throw new DomainException($this->title . ' is niet beschikbaar');
        }

        $this->borrowedBy = $member;
    }

    public function giveBack(): void
    {
        $this->borrowedBy = null;
    }
}

$book = new Book('Misery');

foreach (['Anna', 'Bram'] as $member) {
    try {
        $book->lendTo($member);
        echo $book->getTitle() . " is uitgeleend aan $member\n";
    } catch (DomainException $exception) {
        echo $exception->getMessage() . "\n";
    }
}

// er is geen setAvailable() meer: een boek komt alleen weer beschikbaar via giveBack()
$book->giveBack();
$book->lendTo('Chris');
echo $book->getTitle() . " is uitgeleend aan Chris\n";
