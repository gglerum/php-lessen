<?php

declare(strict_types=1);

class BankAccount
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance;
    }

    public function withdraw(int $amount): void
    {
        $this->balance = $this->balance - $amount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

// deze functie wil alleen laten zien wat er gebeurt als we 80 opnemen
// maar ze gaat er niet vanuit dat het object daarmee echt verandert
function showBalanceAfterWithdraw(BankAccount $account, int $amount): void
{
    $account->withdraw($amount);
    echo "Saldo na opname: " . $account->getBalance() . "\n";
}

$account = new BankAccount(100);

showBalanceAfterWithdraw($account, 80);

// we verwachten nog steeds 100, maar de opname is echt uitgevoerd
echo "Saldo van de rekening: " . $account->getBalance() . "\n";

// hetzelfde gebeurt bij een toewijzing: er is nog steeds maar 1 object
$account = new BankAccount(100);
$backup = $account;

$account->withdraw(30);

echo "account: " . $account->getBalance() . "\n";
echo "backup: " . $backup->getBalance() . "\n";
echo "Zijn het hetzelfde object? " . ($account === $backup ? "ja" : "nee") . "\n";
