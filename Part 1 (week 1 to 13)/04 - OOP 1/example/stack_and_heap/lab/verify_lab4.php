<?php

declare(strict_types=1);

// Controleert de uitvoer die bughunt.html (Lab 4) laat zien door dezelfde scripts echt uit te voeren.
// Run met: php verify_lab4.php (vanuit deze map).

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

function check(string $name, string $actual, string $expected): void
{
    echo $name . ": " . $actual . " (pagina: " . $expected . ") " . ($actual === $expected ? "ok" : "FOUT") . "\n";
}

// scenario 1, met de fout
function previewBalanceBug(BankAccount $account, int $bonus): string
{
    $account->deposit($bonus);
    return "Met bonus: " . $account->getBalance();
}
$account = new BankAccount(100);
$line = previewBalanceBug($account, 50);
check("1 met fout", $line . " | Saldo: " . $account->getBalance(), "Met bonus: 150 | Saldo: 150");

// scenario 1, gecorrigeerd
function previewBalanceFixed(BankAccount $account, int $bonus): string
{
    $copy = clone $account;
    $copy->deposit($bonus);
    return "Met bonus: " . $copy->getBalance();
}
$account = new BankAccount(100);
$line = previewBalanceFixed($account, 50);
check("1 gecorrigeerd", $line . " | Saldo: " . $account->getBalance(), "Met bonus: 150 | Saldo: 100");

// scenario 2
$template = new BankAccount(0);
$first = $template;
$second = $template;
$first->deposit(100);
check("2 met fout", "Tweede rekening: " . $second->getBalance(), "Tweede rekening: 100");

$template = new BankAccount(0);
$first = clone $template;
$second = clone $template;
$first->deposit(100);
check("2 gecorrigeerd", "Tweede rekening: " . $second->getBalance(), "Tweede rekening: 0");

// scenario 3, gecorrigeerd (de versie met fout kan niet veilig draaien)
$lines = [];
function countdown(int $number, array &$lines): void
{
    if ($number < 0) {
        return;
    }
    $lines[] = (string) $number;
    countdown($number - 1, $lines);
}
countdown(3, $lines);
check("3 gecorrigeerd", implode(" | ", $lines), "3 | 2 | 1 | 0");
