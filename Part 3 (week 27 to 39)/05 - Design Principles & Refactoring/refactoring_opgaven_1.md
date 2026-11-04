# Opgaven refactoring

Probeer de opgaven eerst zelf te maken. De uitleg in [refactoring.md](./refactoring.md) helpt je met de werkwijze, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Bij een refactoring verandert het gedrag niet: controleer na elke wijziging dat de uitvoer hetzelfde is als die van het beginpunt.

### Opgave 1: Law of Demeter

Een webshop bepaalt de verzendkosten aan de hand van het land van de klant.

```PHP
class Address
{
    private string $country;

    public function __construct(string $country)
    {
        $this->country = $country;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}

class Customer
{
    private string $name;
    private Address $address;

    public function __construct(string $name, Address $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}

class Order
{
    private Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}

function calculateShipping(Order $order): float
{
    if ($order->getCustomer()->getAddress()->getCountry() === 'NL') {
        return 4.95;
    }

    return 12.50;
}

echo calculateShipping(new Order(new Customer('Anna', new Address('NL')))) . "\n";
echo calculateShipping(new Order(new Customer('Bram', new Address('DE')))) . "\n";
```

1. Welke classes kent `calculateShipping()`? Schrijf het op in een comment.
2. Herschrijf de code zodat `calculateShipping()` alleen nog met `Order` praat. De uitvoer blijft `4.95` en `12.5`.

**Hint:** Laat `Order` zelf het land van de bezorging teruggeven, en `Customer` het land van zijn adres. Dan kun je de `getAddress()` en `getCustomer()` weghalen.

### Opgave 2: Tell, Don't Ask

Een bankrekening laat anderen het saldo opvragen en weer terugzetten.

```PHP
class Account
{
    private float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}

function withdraw(Account $account, float $amount): void
{
    if ($account->getBalance() >= $amount) {
        $account->setBalance($account->getBalance() - $amount);
        echo "Opgenomen: $amount\n";
    } else {
        echo "Onvoldoende saldo\n";
    }
}

$account = new Account(100.0);
withdraw($account, 30.0);
withdraw($account, 90.0);
```

1. Schrijf in een comment wat de functie `withdraw()` allemaal beslist dat `Account` zelf zou kunnen beslissen.
2. Laat `Account` de beslissing nemen met een method `withdraw(float $amount): bool`. Haal `setBalance()` weg. De functie toont alleen nog de tekst. De uitvoer blijft hetzelfde.

**Hint:** De method geeft terug of het is gelukt. Wat de gebruiker te zien krijgt, bepaalt degene die de method aanroept.

### Opgave 3: Refactoren in stappen

Een webshop rekent af voor een klant met een lidmaatschap.

```PHP
class Product
{
    private float $price;

    public function __construct(float $price)
    {
        $this->price = $price;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}

class Membership
{
    private int $discountPercentage;

    public function __construct(int $discountPercentage)
    {
        $this->discountPercentage = $discountPercentage;
    }

    public function getDiscountPercentage(): int
    {
        return $this->discountPercentage;
    }
}

class Customer
{
    private string $name;
    private Membership $membership;

    public function __construct(string $name, Membership $membership)
    {
        $this->name = $name;
        $this->membership = $membership;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMembership(): Membership
    {
        return $this->membership;
    }
}

function checkout(Customer $customer, array $products): void
{
    $total = 0.0;

    foreach ($products as $product) {
        $total += $product->getPrice();
    }

    $discount = $customer->getMembership()->getDiscountPercentage();
    $total = $total - ($total * $discount / 100);

    echo $customer->getName() . ' betaalt ' . $total . "\n";
}

$customer = new Customer('Anna', new Membership(10));
checkout($customer, [new Product(30.0), new Product(20.0)]);
```

De uitvoer is `Anna betaalt 45`.

1. Benoem welke drie smells hierin zitten, en bij elke smell het principe dat erbij hoort. Schrijf dit in een comment.
2. Los de smells één voor één op in drie aparte bestanden (`stap1.php`, `stap2.php`, `stap3.php`), waarbij elk bestand voortbouwt op het vorige.
3. Schrijf een `check.php` (zoals het voorbeeld uit de les) die het beginpunt en de drie stappen uitvoert en laat zien dat de uitvoer steeds gelijk is.

**Hint:** Kijk naar de keten bij het lidmaatschap, naar wie de korting zou moeten berekenen, en naar de `echo` midden in een functie die ook rekent.

### Opgave 4: Analyseer de FileController

Open [`bad/FileController.php`](../example/bad/app/Http/Controllers/FileController.php) en lees de code door. Kijk nog niet naar de map `better`.

1. Er staan zeven fouten in, elk gemarkeerd met `MISTAKE`. Schrijf bij elke fout op welk principe uit les 01 t/m 05 is overtreden, en hoe je het zou oplossen.
2. Zoek ook de Law of Demeter en Tell, Don't Ask in de code. Beide staan er, de eerste in een regel met `->organisation->`, de tweede in de controle van de uploadlimiet.
3. Vergelijk daarna je antwoorden met [`better`](../example/better/app) en met de uitleg in [First steps](./01%20-%20First%20steps/README.md). Welke oplossingen had je zelf ook gekozen en welke niet? Schrijf op waarom.

Je hoeft dit niet in Laravel uit te voeren, het gaat om lezen en schrijven.

**Hint:** Probeer eerst zonder de tabel in [refactoring.md](./refactoring.md). Kijk er pas naar als je denkt alles gevonden te hebben, en controleer dan of je niets gemist hebt.
