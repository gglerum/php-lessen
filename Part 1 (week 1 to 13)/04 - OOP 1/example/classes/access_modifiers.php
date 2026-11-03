<?php

declare(strict_types=1);

class BankAccount
{
    // readonly: de waarde kan na de constructor niet meer veranderen
    public readonly string $owner;

    // private: alleen code binnen deze class mag dit saldo lezen en aanpassen
    private int $balance;

    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner;
        $this->balance = $balance;
    }

    // public: dit is de manier waarop de buitenwereld de class gebruikt
    public function deposit(int $amount): void
    {
        // omdat het saldo alleen hier aangepast kan worden, kunnen we hier ook controleren
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

    // het saldo is private, dus we bieden een method aan om het te lezen (een getter)
    public function getBalance(): int
    {
        return $this->balance;
    }
}

$account = new BankAccount("John", 100);

$account->deposit(50);
$account->withdraw(500);
$account->deposit(-20);

echo $account->owner . " heeft een saldo van " . $account->getBalance() . "\n";

// de volgende regels geven een Error. De controles in de methods kunnen niet meer omzeild worden
// $account->balance = -500;
// echo $account->balance;
// $account->owner = "Jane";
