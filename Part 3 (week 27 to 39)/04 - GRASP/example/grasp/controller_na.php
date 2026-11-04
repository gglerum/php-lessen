<?php

declare(strict_types=1);

class Library
{
    private const MAX_LOANS_PER_MEMBER = 2;

    private array $loansPerMember = [];

    // de bedrijfsregel zit in het domein, zodat elke manier van uitlenen dezelfde regel gebruikt
    public function lend(string $title, string $member): void
    {
        $current = $this->loansPerMember[$member] ?? 0;

        if ($current >= self::MAX_LOANS_PER_MEMBER) {
            throw new DomainException("$member mag niet meer dan " . self::MAX_LOANS_PER_MEMBER . ' boeken lenen');
        }

        $this->loansPerMember[$member] = $current + 1;
    }
}

class LoanController
{
    private Library $library;

    public function __construct(Library $library)
    {
        $this->library = $library;
    }

    // de controller leest het verzoek, geeft het door en kiest wat de gebruiker te zien krijgt
    public function handleLendRequest(array $request): void
    {
        $member = $request['member'] ?? '';
        $title = $request['title'] ?? '';

        if ($member === '' || $title === '') {
            echo "Vul een lid en een titel in\n";
            return;
        }

        try {
            $this->library->lend($title, $member);
            echo "$title is uitgeleend aan $member\n";
        } catch (DomainException $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}

$controller = new LoanController(new Library());
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'Misery']);
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'It']);
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'Carrie']);
$controller->handleLendRequest(['member' => '', 'title' => 'Carrie']);
