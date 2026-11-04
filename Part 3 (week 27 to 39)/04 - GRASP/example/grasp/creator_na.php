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
    private const LOAN_DAYS = 14;

    private array $loans = [];

    // de bibliotheek houdt de uitleningen bij en kent de uitleenduur, dus de bibliotheek maakt ze aan
    public function lend(string $title, string $member, DateTimeImmutable $today): Loan
    {
        $loan = new Loan($title, $member, $today->modify('+' . self::LOAN_DAYS . ' days'));
        $this->loans[] = $loan;

        return $loan;
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
        echo $library->lend($title, $member, $today)->describe() . "\n";
    }
}

$library = new Library();
(new LoanController())->lend($library, 'Misery', 'Anna', new DateTimeImmutable('2026-01-10'));
echo $library->countLoans() . " uitlening\n";
