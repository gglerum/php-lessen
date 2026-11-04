<?php

declare(strict_types=1);

class Loan
{
    private string $status = 'active';

    // elke method moet de status controleren en weet wat er in elke status mag
    public function renew(): string
    {
        if ($this->status === 'active') {
            return 'Verlengd met 14 dagen';
        } elseif ($this->status === 'overdue') {
            return 'Verlengen kan niet: eerst de boete betalen';
        } elseif ($this->status === 'returned') {
            return 'Verlengen kan niet: het boek is al ingeleverd';
        }

        throw new LogicException('Onbekende status');
    }

    public function returnBook(): string
    {
        if ($this->status === 'returned') {
            return 'Het boek is al ingeleverd';
        }

        $message = $this->status === 'overdue' ? 'Ingeleverd, boete verschuldigd' : 'Ingeleverd';
        $this->status = 'returned';

        return $message;
    }

    public function markOverdue(): void
    {
        if ($this->status === 'active') {
            $this->status = 'overdue';
        }
    }
}

$loan = new Loan();
echo $loan->renew() . "\n";
$loan->markOverdue();
echo $loan->renew() . "\n";
echo $loan->returnBook() . "\n";
echo $loan->renew() . "\n";

// een vierde status betekent dat we elke method moeten aanpassen, en de regels per status zijn verspreid over de class
