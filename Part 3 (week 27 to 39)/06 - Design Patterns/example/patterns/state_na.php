<?php

declare(strict_types=1);

// elke status is een eigen class die weet wat er in die status mag
interface LoanState
{
    public function renew(): string;

    public function returnBook(Loan $loan): string;

    public function markOverdue(Loan $loan): void;
}

class ActiveState implements LoanState
{
    public function renew(): string
    {
        return 'Verlengd met 14 dagen';
    }

    public function returnBook(Loan $loan): string
    {
        $loan->changeState(new ReturnedState());

        return 'Ingeleverd';
    }

    public function markOverdue(Loan $loan): void
    {
        $loan->changeState(new OverdueState());
    }
}

class OverdueState implements LoanState
{
    public function renew(): string
    {
        return 'Verlengen kan niet: eerst de boete betalen';
    }

    public function returnBook(Loan $loan): string
    {
        $loan->changeState(new ReturnedState());

        return 'Ingeleverd, boete verschuldigd';
    }

    public function markOverdue(Loan $loan): void
    {
        // de uitlening is al te laat, er verandert niets
    }
}

class ReturnedState implements LoanState
{
    public function renew(): string
    {
        return 'Verlengen kan niet: het boek is al ingeleverd';
    }

    public function returnBook(Loan $loan): string
    {
        return 'Het boek is al ingeleverd';
    }

    public function markOverdue(Loan $loan): void
    {
        // een ingeleverd boek kan niet meer te laat zijn
    }
}

class Loan
{
    private LoanState $state;

    public function __construct()
    {
        $this->state = new ActiveState();
    }

    public function changeState(LoanState $state): void
    {
        $this->state = $state;
    }

    // Loan geeft het werk door aan de huidige status, hier staat geen if meer op de status
    public function renew(): string
    {
        return $this->state->renew();
    }

    public function returnBook(): string
    {
        return $this->state->returnBook($this);
    }

    public function markOverdue(): void
    {
        $this->state->markOverdue($this);
    }
}

$loan = new Loan();
echo $loan->renew() . "\n";
$loan->markOverdue();
echo $loan->renew() . "\n";
echo $loan->returnBook() . "\n";
echo $loan->renew() . "\n";
