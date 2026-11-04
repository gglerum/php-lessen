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

    public function canBorrow(): bool
    {
        return $this->loanCount < $this->getMaxLoans();
    }

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

function lendBook(Member $member): bool
{
    if (!$member->canBorrow()) {
        return false;
    }

    $member->borrow();

    return true;
}

$anna = new Member('Anna', new Organisation('Bibliotheek Utrecht', 2));

// stap 4: het herhaalde blok is vervangen door een loop over de boeken
$books = [new Book('Misery'), new Book('It'), new Book('Carrie')];

foreach ($books as $book) {
    echo lendBook($anna)
        ? $book->getTitle() . ' is uitgeleend aan ' . $anna->getName() . "\n"
        : $anna->getName() . ' mag niet meer dan ' . $anna->getMaxLoans() . " boeken lenen\n";
}
