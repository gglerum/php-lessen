<?php

declare(strict_types=1);

class Loan
{
    private string $title;
    private string $member;
    private int $days;
    private bool $sendReminder;
    private int $maxRenewals;
    private string $notes;

    public function __construct(
        string $title,
        string $member,
        int $days,
        bool $sendReminder,
        int $maxRenewals,
        string $notes
    ) {
        $this->title = $title;
        $this->member = $member;
        $this->days = $days;
        $this->sendReminder = $sendReminder;
        $this->maxRenewals = $maxRenewals;
        $this->notes = $notes;
    }

    public function describe(): string
    {
        return "{$this->title} voor {$this->member}, {$this->days} dagen, "
            . ($this->sendReminder ? 'met' : 'zonder') . " herinnering, {$this->maxRenewals} verlengingen"
            . ($this->notes !== '' ? ", opmerking: {$this->notes}" : '');
    }
}

// wat betekent de 21, de true en de 2? dat moet je in de constructor opzoeken
$loan = new Loan('Misery', 'Anna', 21, true, 2, '');
echo $loan->describe() . "\n";

// en ook als we alleen de standaardwaarden willen moeten we alle zes de waarden opgeven
$simple = new Loan('It', 'Bram', 14, false, 1, '');
echo $simple->describe() . "\n";
