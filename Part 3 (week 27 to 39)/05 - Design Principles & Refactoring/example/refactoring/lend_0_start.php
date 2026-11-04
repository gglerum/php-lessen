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

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
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

// dit is het beginpunt van de refactoring: het werkt, maar er zitten een aantal smells in
function lendBook(Member $member, Book $book): void
{
    // smell 1: een keten van aanroepen, de functie kent Member, Organisation en hoe ze samenhangen
    $max = $member->getOrganisation()->getMaxLoans();

    // smell 2: we vragen gegevens op en beslissen zelf, in plaats van het Member te vragen
    if ($member->getLoanCount() >= $max) {
        // smell 3: de beslissing en het tonen van de tekst zitten door elkaar
        echo $member->getName() . " mag niet meer dan $max boeken lenen\n";
        return;
    }

    $member->setLoanCount($member->getLoanCount() + 1);
    echo $book->getTitle() . ' is uitgeleend aan ' . $member->getName() . "\n";
}

$anna = new Member('Anna', new Organisation('Bibliotheek Utrecht', 2));

// smell 4: hetzelfde blok staat meerdere keren onder elkaar
lendBook($anna, new Book('Misery'));
lendBook($anna, new Book('It'));
lendBook($anna, new Book('Carrie'));
