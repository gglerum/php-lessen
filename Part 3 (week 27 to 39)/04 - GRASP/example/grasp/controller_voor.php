<?php

declare(strict_types=1);

class LoanController
{
    private array $loansPerMember = [];

    // de controller leest het verzoek, bepaalt de regels, rekent en kiest de tekst
    public function handleLendRequest(array $request): void
    {
        $member = $request['member'] ?? '';
        $title = $request['title'] ?? '';

        if ($member === '' || $title === '') {
            echo "Vul een lid en een titel in\n";
            return;
        }

        // een bedrijfsregel: een lid mag maximaal 2 boeken tegelijk lenen
        $current = $this->loansPerMember[$member] ?? 0;
        if ($current >= 2) {
            echo "$member mag niet meer dan 2 boeken lenen\n";
            return;
        }

        $this->loansPerMember[$member] = $current + 1;
        echo "$title is uitgeleend aan $member\n";
    }
}

$controller = new LoanController();
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'Misery']);
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'It']);
$controller->handleLendRequest(['member' => 'Anna', 'title' => 'Carrie']);
$controller->handleLendRequest(['member' => '', 'title' => 'Carrie']);

// de regel "maximaal 2 boeken" zit nu in de controller, en een console script
// of een API die ook uitleent zou de regel moeten kopiëren
