<?php

declare(strict_types=1);

class Loan
{
    private string $title;
    private string $member;
    private DateTimeImmutable $dueDate;

    public function __construct(string $title, string $member, DateTimeImmutable $dueDate)
    {
        $this->title = $title;
        $this->member = $member;
        $this->dueDate = $dueDate;
    }

    public function describe(): string
    {
        return $this->title . ' voor ' . $this->member . ', terug op ' . $this->dueDate->format('d-m-Y');
    }
}

class Library
{
    private array $loans = [];

    public function addLoan(Loan $loan): void
    {
        $this->loans[] = $loan;
    }

    public function countLoans(): int
    {
        return count($this->loans);
    }
}

class LoanController
{
    public function lend(Library $library, string $title, string $member, DateTimeImmutable $today): void
    {
        // de controller maakt de uitlening aan en moet daarvoor weten hoe lang we uitlenen
        $loan = new Loan($title, $member, $today->modify('+14 days'));

        $library->addLoan($loan);
        echo $loan->describe() . "\n";
    }
}

$library = new Library();
(new LoanController())->lend($library, 'Misery', 'Anna', new DateTimeImmutable('2026-01-10'));
echo $library->countLoans() . " uitlening\n";

// de uitleenduur (14 dagen) staat nu in de controller, terwijl de bibliotheek die regel hoort te bepalen
