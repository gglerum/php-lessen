<?php

declare(strict_types=1);

class BankAccount
{
    private string $owner;

    public function __construct(string $owner)
    {
        $this->owner = $owner;
        echo "  [heap] rekening van {$this->owner} is aangemaakt\n";
    }

    // __destruct wordt aangeroepen als het object uit het geheugen verdwijnt
    public function __destruct()
    {
        echo "  [heap] rekening van {$this->owner} is opgeruimd\n";
    }
}

function openTemporaryAccount(): void
{
    echo "  begin van de functie\n";
    $account = new BankAccount("Tijdelijk");
    echo "  einde van de functie\n";
    // het frame verdwijnt, daarmee $account, en daarna is er niets meer dat naar het object wijst
}

function openAndReturnAccount(): BankAccount
{
    $account = new BankAccount("Teruggegeven");

    // de variabele $account verdwijnt, maar het object leeft door omdat we het teruggeven
    return $account;
}

echo "1. Object alleen in een functie:\n";
openTemporaryAccount();
echo "  terug in main\n\n";

echo "2. Object dat uit de functie wordt teruggegeven:\n";
$kept = openAndReturnAccount();
echo "  terug in main, het object bestaat nog\n\n";

echo "3. Verwijzing weghalen:\n";
$kept = null;
echo "  na \$kept = null\n\n";

echo "4. Object dat tot het einde van het script blijft:\n";
$last = new BankAccount("Laatste");
echo "  einde van het script\n";
