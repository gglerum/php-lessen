<?php

declare(strict_types=1);

// de Loan hoort bij de Library en wordt alleen door de Library aangemaakt
class Loan
{
    private string $title;
    private string $member;

    public function __construct(string $title, string $member)
    {
        $this->title = $title;
        $this->member = $member;
    }

    public function isFor(string $member): bool
    {
        return $this->member === $member;
    }
}

// Library is de root van de aggregate: het enige toegangspunt, dat de regels bewaakt
class Library
{
    private const MAX_LOANS_PER_MEMBER = 2;

    private array $loans = [];

    public function lend(string $title, string $member): void
    {
        if ($this->countLoansOf($member) >= self::MAX_LOANS_PER_MEMBER) {
            throw new DomainException("$member mag niet meer dan " . self::MAX_LOANS_PER_MEMBER . ' boeken lenen');
        }

        $this->loans[] = new Loan($title, $member);
    }

    public function countLoansOf(string $member): int
    {
        return count(array_filter($this->loans, fn(Loan $loan) => $loan->isFor($member)));
    }
}

$library = new Library();

foreach (['Misery', 'It', 'Carrie'] as $title) {
    try {
        $library->lend($title, 'Anna');
    } catch (DomainException $exception) {
        echo $exception->getMessage() . "\n";
    }
}

echo 'Anna heeft ' . $library->countLoansOf('Anna') . " boeken geleend\n";

// de regel kan niet meer worden omzeild: de lijst is private en een Loan wordt alleen binnen Library aangemaakt
