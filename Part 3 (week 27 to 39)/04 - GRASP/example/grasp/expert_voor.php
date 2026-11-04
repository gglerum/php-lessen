<?php

declare(strict_types=1);

// een uitlening weet wanneer hij terug moet zijn en hoeveel de boete per dag is
class Loan
{
    private string $title;
    private DateTimeImmutable $dueDate;
    private float $feePerDay;

    public function __construct(string $title, DateTimeImmutable $dueDate, float $feePerDay)
    {
        $this->title = $title;
        $this->dueDate = $dueDate;
        $this->feePerDay = $feePerDay;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getFeePerDay(): float
    {
        return $this->feePerDay;
    }
}

class LoanController
{
    // de controller haalt alle gegevens op en rekent de boete zelf uit
    public function showLateFee(Loan $loan, DateTimeImmutable $today): void
    {
        $daysLate = (int)$loan->getDueDate()->diff($today)->format('%r%a');
        $fee = max(0, $daysLate) * $loan->getFeePerDay();

        echo $loan->getTitle() . ': ' . $fee . " boete\n";
    }
}

$loan = new Loan('Misery', new DateTimeImmutable('2026-01-10'), 0.25);
(new LoanController())->showLateFee($loan, new DateTimeImmutable('2026-01-20'));

// als de berekening verandert (bijvoorbeeld een maximumboete), moeten we de controller aanpassen
// terwijl alle gegevens die de berekening nodig heeft in Loan zitten
