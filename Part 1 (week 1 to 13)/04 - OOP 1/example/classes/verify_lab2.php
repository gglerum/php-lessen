<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/private.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab2.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// De regels van het lab worden aan elkaar geplakt, omdat echo zelf geen regeleinde toevoegt.
// De class met een private saldo heet hier BankAccountPrivate, zodat beide versies in een bestand passen.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

class BankAccount
{
    public int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance;
    }

    public function withdraw(int $amount): void
    {
        if ($amount > $this->balance) {
            echo "Onvoldoende saldo\n";
            return;
        }

        $this->balance = $this->balance - $amount;
    }
}

class BankAccountPrivate
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance;
    }

    public function withdraw(int $amount): void
    {
        if ($amount > $this->balance) {
            echo "Onvoldoende saldo\n";
            return;
        }

        $this->balance = $this->balance - $amount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}

// scenario 1
ob_start();
$account = new BankAccount(100);
$account->withdraw(500);
$account->balance = -500;
echo $account->balance;
check('1', str_replace("\n", '|', ob_get_clean()), 'Onvoldoende saldo|-500');

// scenario 2
ob_start();
$account = new BankAccountPrivate(100);
$account->withdraw(500);
$account->withdraw(30);
echo $account->getBalance();
check('2', str_replace("\n", '|', ob_get_clean()), 'Onvoldoende saldo|70');

// scenario 3
$account = new BankAccountPrivate(100);
try {
    $account->balance = -500;
    $message = 'geen Error';
} catch (Error $error) {
    $message = $error->getMessage();
}
check('3', $message, 'Cannot access private property BankAccountPrivate::$balance');
