<?php

declare(strict_types=1);

// een class is een blauwdruk. Hier staat zowel de data (properties) als de functies (methods) in
class BankAccount
{
    // een property is een variabele die bij de class hoort
    public int $balance = 0;

    // een method is een functie die bij de class hoort
    public function deposit(int $amount): void
    {
        // $this verwijst naar het object waar de method op wordt aangeroepen
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
}

// met new maken we een object (een exemplaar) van de class
$account = new BankAccount();

// met -> gebruiken we een property of method van het object
$account->deposit(150);
$account->withdraw(30);
echo "Saldo: " . $account->balance . "\n";

// we kunnen zoveel objecten maken als we willen, ze hebben elk hun eigen saldo
$secondAccount = new BankAccount();
$secondAccount->deposit(20);

echo "Saldo rekening 1: " . $account->balance . "\n";
echo "Saldo rekening 2: " . $secondAccount->balance . "\n";

// let op: de property is nog public, dus ook nu kan iemand de controle in withdraw() omzeilen
$account->balance = -500;
echo "Saldo: " . $account->balance . "\n";
