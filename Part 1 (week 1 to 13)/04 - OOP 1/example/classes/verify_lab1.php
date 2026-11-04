<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/objects.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// scenario 1
function deposit(int $balance, int $amount): int
{
    return $balance + $amount;
}
$balance = 100;
$balanceOfSecondAccount = 20;
$balance = deposit($balance, 50);
$balanceOfSecondAccount = deposit($balanceOfSecondAccount, 10);
check('1', $balance . " en " . $balanceOfSecondAccount, '150 en 30');

// scenario 2 en 3
class BankAccount
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }
}
$account = new BankAccount();
$account->deposit(150);
check('2', (string) $account->balance, '150');

$account = new BankAccount();
$secondAccount = new BankAccount();
$account->deposit(150);
$secondAccount->deposit(20);
check('3', $account->balance . " en " . $secondAccount->balance, '150 en 20');

// scenario 4
class BankAccountWithConstructor
{
    public string $owner;
    public int $balance;

    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner;
        $this->balance = $balance;
    }

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }
}
$account = new BankAccountWithConstructor("John", 100);
$account->deposit(50);
check('4', $account->owner . ": " . $account->balance, 'John: 150');
