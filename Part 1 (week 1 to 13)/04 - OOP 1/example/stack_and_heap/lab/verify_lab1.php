<?php

declare(strict_types=1);

// Controleert de vooraf bepaalde antwoorden van copy_or_original.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.

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

// scenario 1
function addBonusInt(int $balance): void
{
    $balance = $balance + 50;
}
$balance = 100;
addBonusInt($balance);
$results[1] = [$balance, 100];

// scenario 2
function addBonus(BankAccount $account): void
{
    $account->deposit(50);
}
$account = new BankAccount(100);
addBonus($account);
$results[2] = [$account->getBalance(), 150];

// scenario 3
function replaceAccount(BankAccount $account): void
{
    $account = new BankAccount(9999);
}
$account = new BankAccount(100);
replaceAccount($account);
$results[3] = [$account->getBalance(), 100];

// scenario 4
$account = new BankAccount(100);
$backup = $account;
$account->deposit(50);
$results[4] = [$backup->getBalance(), 150];
$sameObject4 = $account === $backup;

// scenario 5
$account = new BankAccount(100);
$backup = clone $account;
$account->deposit(50);
$results[5] = [$backup->getBalance(), 100];
$sameObject5 = $account === $backup;

foreach ($results as $number => [$actual, $expected]) {
    echo "Scenario $number: PHP geeft $actual, de pagina zegt $expected: " . ($actual === $expected ? "ok" : "FOUT") . "\n";
}
echo "Scenario 4 hetzelfde object: " . ($sameObject4 ? "ja (ok)" : "nee (FOUT)") . "\n";
echo "Scenario 5 hetzelfde object: " . ($sameObject5 ? "ja (FOUT)" : "nee (ok)") . "\n";
