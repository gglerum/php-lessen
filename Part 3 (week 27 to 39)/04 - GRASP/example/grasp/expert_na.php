<?php

declare(strict_types=1);

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

    // Loan heeft de gegevens, dus Loan is de expert en berekent de boete
    public function calculateLateFee(DateTimeImmutable $today): float
    {
        $daysLate = (int)$this->dueDate->diff($today)->format('%r%a');

        return max(0, $daysLate) * $this->feePerDay;
    }
}

class LoanController
{
    // de controller vraagt het antwoord en toont het
    public function showLateFee(Loan $loan, DateTimeImmutable $today): void
    {
        echo $loan->getTitle() . ': ' . $loan->calculateLateFee($today) . " boete\n";
    }
}

$loan = new Loan('Misery', new DateTimeImmutable('2026-01-10'), 0.25);
(new LoanController())->showLateFee($loan, new DateTimeImmutable('2026-01-20'));
