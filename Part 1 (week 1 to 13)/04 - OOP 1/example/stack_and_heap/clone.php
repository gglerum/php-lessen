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

// met clone maken we een nieuw object met dezelfde waarden
// de functie werkt nu op de kopie, het origineel blijft ongewijzigd
function showBalanceAfterWithdraw(BankAccount $account, int $amount): void
{
    $copy = clone $account;
    $copy->withdraw($amount);
    echo "Saldo na opname: " . $copy->getBalance() . "\n";
}

$account = new BankAccount(100);

showBalanceAfterWithdraw($account, 80);
echo "Saldo van de rekening: " . $account->getBalance() . "\n";

// ook bij een toewijzing: nu zijn het twee losse objecten
$backup = clone $account;
$account->withdraw(30);

echo "account: " . $account->getBalance() . "\n";
echo "backup: " . $backup->getBalance() . "\n";
echo "Zijn het hetzelfde object? " . ($account === $backup ? "ja" : "nee") . "\n";
