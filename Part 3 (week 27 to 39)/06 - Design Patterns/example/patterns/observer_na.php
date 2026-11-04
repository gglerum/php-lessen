<?php

declare(strict_types=1);

// de afspraak: wie wil weten dat er iets is uitgeleend, implementeert deze interface
interface LoanObserver
{
    public function onLoan(string $title, string $member): void;
}

class MailObserver implements LoanObserver
{
    public function onLoan(string $title, string $member): void
    {
        echo "E-mail naar $member: $title is uitgeleend\n";
    }
}

class LogObserver implements LoanObserver
{
    public function onLoan(string $title, string $member): void
    {
        echo "[LOG] $title uitgeleend aan $member\n";
    }
}

class StatisticsObserver implements LoanObserver
{
    private int $loans = 0;

    public function onLoan(string $title, string $member): void
    {
        $this->loans++;
        echo "Aantal uitleningen: {$this->loans}\n";
    }
}

class Library
{
    private array $observers = [];

    public function addObserver(LoanObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    // Library meldt alleen dat er iets is uitgeleend, wie daarna luistert weet Library niet
    public function lend(string $title, string $member): void
    {
        foreach ($this->observers as $observer) {
            $observer->onLoan($title, $member);
        }
    }
}

$library = new Library();
$library->addObserver(new MailObserver());
$library->addObserver(new LogObserver());
$library->addObserver(new StatisticsObserver());

$library->lend('Misery', 'Anna');

// een vierde reactie toevoegen is een nieuwe class en een regel hierboven, Library blijft ongewijzigd
