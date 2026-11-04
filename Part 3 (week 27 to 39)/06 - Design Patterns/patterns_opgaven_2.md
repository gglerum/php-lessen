# Opgaven design patterns deel 2

Probeer de opgaven eerst zelf te maken. De uitleg in [patterns_2.md](./patterns_2.md) helpt je met de patterns, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Decorator

Een applicatie toont berichten. Voor elke combinatie van opmaak is een subclass gemaakt.

```PHP
interface Message
{
    public function getText(): string;
}

class PlainMessage implements Message
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}

class UppercaseMessage extends PlainMessage
{
    public function getText(): string
    {
        return strtoupper(parent::getText());
    }
}

class ExclaimedMessage extends PlainMessage
{
    public function getText(): string
    {
        return parent::getText() . '!';
    }
}

class UppercaseExclaimedMessage extends PlainMessage
{
    public function getText(): string
    {
        return strtoupper(parent::getText()) . '!';
    }
}

echo (new PlainMessage('hallo'))->getText() . "\n";
echo (new UppercaseMessage('hallo'))->getText() . "\n";
echo (new ExclaimedMessage('hallo'))->getText() . "\n";
echo (new UppercaseExclaimedMessage('hallo'))->getText() . "\n";
```

1. Wat gebeurt er met het aantal classes als er een derde opmaak bij komt, bijvoorbeeld het weglaten van witruimte (`trim`)? Schrijf het op in een comment.
2. Vervang de drie subclasses door decorators die een `Message` inpakken: `UppercaseDecorator` en `ExclaimDecorator`. De uitvoer blijft `hallo`, `HALLO`, `hallo!` en `HALLO!`.
3. Voeg de `TrimDecorator` toe en laat zien dat je er `new TrimDecorator(new PlainMessage('  hallo  '))` mee kunt maken, zonder een bestaande class aan te passen.

**Hint:** Een decorator is zelf een `Message` en krijgt een andere `Message` mee in de constructor.

### Opgave 2: Adapter

Een rapport wordt afgedrukt via een eigen interface. Nu moet een oude printer class worden gebruikt die niet op die interface past.

```PHP
interface Printer
{
    public function print(string $document): void;
}

class ScreenPrinter implements Printer
{
    public function print(string $document): void
    {
        echo "Scherm: $document\n";
    }
}

// een class van een leverancier die we niet mogen aanpassen
class LegacyPrinter
{
    public function printText(string $text, int $copies): void
    {
        for ($i = 0; $i < $copies; $i++) {
            echo "Papier: $text\n";
        }
    }
}

class Report
{
    private Printer $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function publish(): void
    {
        $this->printer->print('Jaarverslag');
    }
}

(new Report(new ScreenPrinter()))->publish();
```

1. Maak een `LegacyPrinterAdapter` die `Printer` implementeert en intern een `LegacyPrinter` gebruikt. Hij drukt het document één keer af.
2. Laat zien dat `new Report(new LegacyPrinterAdapter(new LegacyPrinter()))` werkt zonder `Report` of `LegacyPrinter` aan te passen. De uitvoer is `Papier: Jaarverslag`.
3. Wat is het verschil tussen een Adapter en een Decorator? Schrijf het in je eigen woorden in een comment.

**Hint:** De adapter vertaalt `print($document)` naar `printText($document, 1)`.

### Opgave 3: Facade

Het afrekenen van een bestelling bestaat uit meerdere stappen, die op twee plekken worden herhaald.

```PHP
class StockService
{
    public function reserve(string $product): void
    {
        echo "Voorraad gereserveerd: $product\n";
    }
}

class PaymentService
{
    public function charge(string $customer, float $amount): void
    {
        echo "$customer betaalt $amount\n";
    }
}

class ReceiptPrinter
{
    public function print(string $customer, string $product): void
    {
        echo "Bon voor $customer: $product\n";
    }
}

$stock = new StockService();
$payment = new PaymentService();
$receipt = new ReceiptPrinter();

// balie
$stock->reserve('Boek');
$payment->charge('Anna', 12.5);
$receipt->print('Anna', 'Boek');

// webshop
$stock->reserve('Pen');
$payment->charge('Bram', 1.5);
$receipt->print('Bram', 'Pen');
```

1. Maak een `CheckoutFacade` met een method `checkout(string $customer, string $product, float $amount)` die de drie stappen in de goede volgorde uitvoert.
2. Laat de balie en de webshop de facade gebruiken. De uitvoer blijft hetzelfde.
3. Een nieuwe stap (bijvoorbeeld een e-mail sturen) komt erbij. Op hoeveel plekken moet je dat aanpassen vóór en na de facade? Schrijf het op in een comment.

**Hint:** De facade krijgt de drie services via de constructor, zoals `LibraryFacade` in de uitleg.

### Opgave 4: Wanneer niet?

Kies één van de patterns uit deze twee delen (Singleton, Strategy, Factory, Observer, Decorator, Adapter of Facade). Schrijf een kort stuk code of een beschrijving van een situatie in een kleine applicatie waarin je dat pattern juist *niet* zou gebruiken. Leg uit welk principe je daarbij gebruikt (bijvoorbeeld YAGNI) en wat je wel zou doen.

**Hint:** Denk aan een pattern waarbij je maar één uitwerking hebt en geen reden om een tweede te verwachten.
