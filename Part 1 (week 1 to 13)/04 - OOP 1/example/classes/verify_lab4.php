<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/bughunt.html, voor de versie met de fout en de gecorrigeerde versie.
// Run met: php verify_lab4.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// Elke versie heeft hier een eigen classnaam, zodat ze in een bestand passen. De naam wordt uit de melding gehaald.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

$warnings = [];
set_error_handler(function (int $level, string $message) use (&$warnings): bool {
    $warnings[] = 'Warning: ' . $message;
    return true;
});

// scenario 1
class BankAccount1Bug
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $balance = $balance + $amount;
    }
}
$account = new BankAccount1Bug();
$account->deposit(100);
check('1 met fout', "Saldo: " . $account->balance, 'Saldo: 0');
check('1 waarschuwing', implode('|', $warnings), 'Warning: Undefined variable $balance');

class BankAccount1Fixed
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }
}
$account = new BankAccount1Fixed();
$account->deposit(100);
check('1 gecorrigeerd', "Saldo: " . $account->balance, 'Saldo: 100');

// scenario 2
class BankAccount2Bug
{
    public int $balance;

    public function __construct(int $balance)
    {
        $balance = $balance;
    }
}
$account = new BankAccount2Bug(100);
try {
    $message = "Saldo: " . $account->balance;
} catch (Error $error) {
    $message = str_replace('2Bug', '', $error->getMessage());
}
check('2 met fout', $message, 'Typed property BankAccount::$balance must not be accessed before initialization');

class BankAccount2Fixed
{
    public int $balance;

    public function __construct(int $balance)
    {
        $this->balance = $balance;
    }
}
$account = new BankAccount2Fixed(100);
check('2 gecorrigeerd', "Saldo: " . $account->balance, 'Saldo: 100');

// scenario 3
try {
    $account = BankAccount();
    $message = 'geen Error';
} catch (Error $error) {
    $message = $error->getMessage();
}
check('3 met fout', $message, 'Call to undefined function BankAccount()');

class BankAccount3
{
    public int $balance = 0;
}
$account = new BankAccount3();
check('3 gecorrigeerd', "Saldo: " . $account->balance, 'Saldo: 0');
