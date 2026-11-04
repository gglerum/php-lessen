# Opgaven SOLID deel 1

Probeer de opgaven eerst zelf te maken. De uitleg in [solid_1.md](./solid_1.md) helpt je met de principes, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Single Responsibility

Deze class van een webshop doet meer dan één ding.

```PHP
class Order
{
    private array $lines = [];

    public function addLine(string $product, float $price, int $quantity): void
    {
        $this->lines[] = ['product' => $product, 'price' => $price, 'quantity' => $quantity];
    }

    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->lines as $line) {
            $total += $line['price'] * $line['quantity'];
        }

        return $total;
    }

    public function printReceipt(): void
    {
        foreach ($this->lines as $line) {
            echo $line['quantity'] . 'x ' . $line['product'] . ': ' . $line['price'] * $line['quantity'] . "\n";
        }

        echo 'Totaal: ' . $this->getTotal() . "\n";
    }

    public function sendConfirmationEmail(string $email): void
    {
        echo "E-mail verstuurd naar $email met een totaal van " . $this->getTotal() . "\n";
    }
}

$order = new Order();
$order->addLine('Boek', 12.5, 2);
$order->addLine('Pen', 1.5, 4);
$order->printReceipt();
$order->sendConfirmationEmail('anna@example.com');
```

1. Schrijf in een comment bij welke onderdelen elk een eigen reden hebben om te veranderen.
2. Splits de class op in meerdere classes, zodat elke class één reden heeft om te veranderen. De uitvoer van het script blijft hetzelfde.

**Hint:** Vraag je bij elke method af: *wie moet ik bellen als dit verandert?* Wat blijft er over in `Order`?

### Opgave 2: Open/Closed

Een webshop berekent de verzendkosten per bezorgdienst.

```PHP
function calculateShipping(string $carrier, float $weightInKilos): float
{
    if ($carrier === 'postnl') {
        return 3.95 + ($weightInKilos * 0.50);
    } elseif ($carrier === 'dhl') {
        return 5.50 + ($weightInKilos * 0.30);
    } elseif ($carrier === 'pickup') {
        return 0.0;
    }

    throw new InvalidArgumentException('Onbekende bezorgdienst');
}

echo calculateShipping('postnl', 2.0) . "\n";
echo calculateShipping('dhl', 2.0) . "\n";
echo calculateShipping('pickup', 2.0) . "\n";
```

1. Zet dit om naar een interface of abstracte class `Carrier` met een class per bezorgdienst. De aanroepen geven hetzelfde resultaat.
2. Voeg een vierde bezorgdienst toe, `UPS` (7.00 plus 0.25 per kilo), zonder `calculateShipping()` aan te passen. Schrijf in een comment op welke bestanden of functies je hebt moeten veranderen.

**Hint:** De functie `calculateShipping()` krijgt straks een `Carrier` in plaats van een `string` mee.

### Opgave 3: Liskov Substitution

Hier is een klassiek voorbeeld van vormen.

```PHP
class Rectangle
{
    protected int $width = 0;
    protected int $height = 0;

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getArea(): int
    {
        return $this->width * $this->height;
    }
}

// een vierkant is een rechthoek met gelijke zijden, dus laten we het overerven
class Square extends Rectangle
{
    public function setWidth(int $width): void
    {
        $this->width = $width;
        $this->height = $width;
    }

    public function setHeight(int $height): void
    {
        $this->width = $height;
        $this->height = $height;
    }
}

function makeWide(Rectangle $rectangle): void
{
    $rectangle->setWidth(5);
    $rectangle->setHeight(2);

    echo "Verwacht 10, kreeg " . $rectangle->getArea() . "\n";
}

makeWide(new Rectangle());
makeWide(new Square());
```

1. Leg in een comment uit wat er misgaat en welke afspraak van `Rectangle` door `Square` wordt gebroken.
2. Los het op zodat `makeWide()` niet meer met een `Square` kan worden aangeroepen en niets verrassends meer gebeurt. Je mag zelf bedenken hoe: bijvoorbeeld geen overerving, maar wel een gemeenschappelijke class of interface `Shape` met `getArea()`.

**Hint:** Kijk wat `makeWide()` verwacht van een `Rectangle` als hij `setWidth()` en `setHeight()` na elkaar aanroept.
