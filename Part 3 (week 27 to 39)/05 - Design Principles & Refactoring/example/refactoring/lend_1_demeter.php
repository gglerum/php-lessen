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

    // stap 1: Member vertelt zelf wat zijn maximum is, wie Member gebruikt hoeft Organisation niet te kennen
    public function getMaxLoans(): int
    {
        return $this->organisation->getMaxLoans();
    }

    public function getLoanCount(): int
    {
        return $this->loanCount;
    }

    public function setLoanCount(int $loanCount): void
    {
        $this->loanCount = $loanCount;
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
    $max = $member->getMaxLoans();

    if ($member->getLoanCount() >= $max) {
        echo $member->getName() . " mag niet meer dan $max boeken lenen\n";
        return;
    }

    $member->setLoanCount($member->getLoanCount() + 1);
    echo $book->getTitle() . ' is uitgeleend aan ' . $member->getName() . "\n";
}

$anna = new Member('Anna', new Organisation('Bibliotheek Utrecht', 2));

lendBook($anna, new Book('Misery'));
lendBook($anna, new Book('It'));
lendBook($anna, new Book('Carrie'));
