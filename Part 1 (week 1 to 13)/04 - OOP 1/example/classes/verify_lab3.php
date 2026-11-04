<?php

declare(strict_types=1);

// Controleert de vooraf bepaalde antwoorden van lab/predict_output.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab3.php (vanuit deze map). Alle regels moeten "ok" zeggen.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// dezelfde class als in access_modifiers.php en in de pagina
class BankAccount
{
    public readonly string $owner;
    private int $balance;

    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner;
        $this->balance = $balance;
    }

    public function deposit(int $amount): void
    {
        if ($amount <= 0) {
            echo "Je kunt alleen een positief bedrag storten\n";
            return;
        }

        $this->balance = $this->balance + $amount;
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
$first = new BankAccount("John", 100);
$second = new BankAccount("Jane");
$first->deposit(50);
check('1', (string) $second->getBalance(), '0');

// scenario 2
ob_start();
$account = new BankAccount("John", 100);
$account->withdraw(500);
echo $account->getBalance();
check('2', str_replace("\n", '|', ob_get_clean()), 'Onvoldoende saldo|100');

// scenario 3
ob_start();
$account = new BankAccount("John", 100);
$account->deposit(-20);
echo $account->getBalance();
check('3', str_replace("\n", '|', ob_get_clean()), 'Je kunt alleen een positief bedrag storten|100');

// scenario 4, 5 en 6: een Error of een ander resultaat
$cases = [
    '4' => [fn() => $account->balance, 'Error'],
    '5' => [function () use ($account) { $account->owner = "Jane"; }, 'Error'],
    '6' => [fn() => new BankAccount(), 'ArgumentCountError'],
];
foreach ($cases as $name => [$run, $expected]) {
    try {
        $run();
        $actual = 'geen Error';
    } catch (Error $error) {
        $actual = $error::class;
    }
    check((string) $name, $actual, $expected);
}
