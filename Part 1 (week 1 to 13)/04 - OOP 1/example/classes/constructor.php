<?php

declare(strict_types=1);

class BankAccount
{
    public string $owner;
    public int $balance;

    // de constructor wordt automatisch aangeroepen bij new
    // de argumenten die je bij new meegeeft komen hier binnen als parameters
    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner;
        $this->balance = $balance;
    }

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }

    public function getDescription(): string
    {
        return $this->owner . " heeft een saldo van " . $this->balance;
    }
}

// een object heeft nu altijd een eigenaar en een saldo vanaf het moment dat het bestaat
$account = new BankAccount("John", 100);
$secondAccount = new BankAccount("Jane");

$account->deposit(50);
$secondAccount->deposit(20);

echo $account->getDescription() . "\n";
echo $secondAccount->getDescription() . "\n";

// de types van de properties en parameters worden gecontroleerd (strict_types)
// de volgende regel geeft een TypeError, omdat "veel" geen int is
// $account->deposit("veel");

// zonder de verplichte parameters kun je geen object maken, dit geeft een ArgumentCountError
// $thirdAccount = new BankAccount();
