<?php

declare(strict_types=1);

class BankAccount
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance;
    }

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

// let op: dezelfde functie als in pass_by_value.php, maar nu met een object
function addBonus(BankAccount $account): void
{
    $account->deposit(50);
    echo "In de functie: " . $account->getBalance() . "\n";
}

$account = new BankAccount(100);
addBonus($account);

// het saldo is wel veranderd, de functie werkte op hetzelfde object
echo "Buiten de functie: " . $account->getBalance() . "\n";

// de functie krijgt een kopie van de verwijzing naar het object, niet van het object zelf
// met new maken we in de functie een tweede object, het originele object blijft zoals het was
function replaceAccount(BankAccount $account): void
{
    $account = new BankAccount(9999);
    echo "In de functie: " . $account->getBalance() . "\n";
}

replaceAccount($account);
echo "Buiten de functie: " . $account->getBalance() . "\n";
