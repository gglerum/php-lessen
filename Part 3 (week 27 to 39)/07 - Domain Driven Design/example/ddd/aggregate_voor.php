<?php

declare(strict_types=1);

class Loan
{
    private string $title;
    private string $member;

    public function __construct(string $title, string $member)
    {
        $this->title = $title;
        $this->member = $member;
    }

    public function getMember(): string
    {
        return $this->member;
    }
}

class Library
{
    // de lijst is public, dus iedereen kan er rechtstreeks iets aan toevoegen
    public array $loans = [];
}

$library = new Library();

// de regel "maximaal 2 boeken per lid" moet elke aanroeper zelf onthouden
$library->loans[] = new Loan('Misery', 'Anna');
$library->loans[] = new Loan('It', 'Anna');
$library->loans[] = new Loan('Carrie', 'Anna');

echo 'Anna heeft ' . count(array_filter($library->loans, fn(Loan $loan) => $loan->getMember() === 'Anna')) . " boeken geleend\n";

// niets voorkomt dat de regel wordt overtreden
