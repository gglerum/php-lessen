<?php

declare(strict_types=1);

class Organisation
{
    private string $name;
    private int $maxLoans;

    public function __construct(string $name, int $maxLoans)
    {
        $this->name = $name;
        $this->maxLoans = $maxLoans;
    }

    public function getMaxLoans(): int
    {
        return $this->maxLoans;
    }
}

class Member
{
    private string $name;
    private Organisation $organisation;
    private int $loanCount = 0;

    public function __construct(string $name, Organisation $organisation)
    {
        $this->name = $name;
        $this->organisation = $organisation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMaxLoans(): int
    {
        return $this->organisation->getMaxLoans();
    }

    // stap 2: de beslissing zit in Member, die heeft de gegevens (Information Expert)
    public function canBorrow(): bool
    {
        return $this->loanCount < $this->getMaxLoans();
    }

    // en Member houdt zelf het aantal bij, er is geen setLoanCount() meer nodig
    public function borrow(): void
    {
        $this->loanCount++;
    }
}

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

function lendBook(Member $member, Book $book): void
{
    if (!$member->canBorrow()) {
        echo $member->getName() . ' mag niet meer dan ' . $member->getMaxLoans() . " boeken lenen\n";
        return;
    }

    $member->borrow();
    echo $book->getTitle() . ' is uitgeleend aan ' . $member->getName() . "\n";
}

$anna = new Member('Anna', new Organisation('Bibliotheek Utrecht', 2));

lendBook($anna, new Book('Misery'));
lendBook($anna, new Book('It'));
lendBook($anna, new Book('Carrie'));
