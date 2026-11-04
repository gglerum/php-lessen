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

// de builder heeft standaardwaarden en elke method zegt wat hij instelt
class LoanBuilder
{
    private string $title;
    private string $member;
    private int $days = 14;
    private bool $sendReminder = false;
    private int $maxRenewals = 1;
    private string $notes = '';

    private function __construct(string $title, string $member)
    {
        $this->title = $title;
        $this->member = $member;
    }

    public static function for(string $title, string $member): self
    {
        return new self($title, $member);
    }

    public function days(int $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function withReminder(): self
    {
        $this->sendReminder = true;

        return $this;
    }

    public function maxRenewals(int $maxRenewals): self
    {
        $this->maxRenewals = $maxRenewals;

        return $this;
    }

    public function notes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function build(): Loan
    {
        return new Loan($this->title, $this->member, $this->days, $this->sendReminder, $this->maxRenewals, $this->notes);
    }
}

// je leest nu wat er wordt ingesteld, en wat je niet noemt krijgt de standaardwaarde
$loan = LoanBuilder::for('Misery', 'Anna')->days(21)->withReminder()->maxRenewals(2)->build();
echo $loan->describe() . "\n";

$simple = LoanBuilder::for('It', 'Bram')->build();
echo $simple->describe() . "\n";

// PHP kent ook "named arguments" (new Loan(title: 'Misery', days: 21, ...)), waarmee een
// constructor met veel parameters ook leesbaar kan. Voor een eenvoudige class is dat vaak genoeg
