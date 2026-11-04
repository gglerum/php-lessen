# Opgaven design patterns deel 1

Probeer de opgaven eerst zelf te maken. De uitleg in [patterns_1.md](./patterns_1.md) helpt je met de patterns, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Singleton

Een prijsberekening haalt het btw percentage uit een singleton.

```PHP
class Settings
{
    private static ?Settings $instance = null;

    private array $values = ['vat' => 21];

    private function __construct()
    {
    }

    public static function getInstance(): Settings
    {
        if (self::$instance === null) {
            self::$instance = new Settings();
        }

        return self::$instance;
    }

    public function get(string $key): int
    {
        return $this->values[$key];
    }
}

class PriceCalculator
{
    public function withVat(float $price): float
    {
        return $price + ($price * Settings::getInstance()->get('vat') / 100);
    }
}

echo (new PriceCalculator())->withVat(100.0) . "\n";
```

1. Schrijf in een comment wat de nadelen zijn van deze oplossing. Noem er minstens twee.
2. Zet de code om naar dependency injection: `Settings` wordt een gewone class en `PriceCalculator` krijgt hem via de constructor. De uitvoer blijft `121`.
3. Laat daarna zien dat je met de nieuwe oplossing ook kunt rekenen met 9% btw (`['vat' => 9]`), zonder `PriceCalculator` aan te passen. Wat geeft `withVat(100.0)` dan?

**Hint:** Het idee van één instantie blijft: je maakt één `Settings` aan en geeft die door.

### Opgave 2: Strategy

Een webshop berekent de prijs afhankelijk van het soort klant.

```PHP
function calculatePrice(float $price, string $customerType): float
{
    if ($customerType === 'regular') {
        return $price;
    } elseif ($customerType === 'student') {
        return $price * 0.90;
    } elseif ($customerType === 'member') {
        return $price * 0.85;
    }

    throw new InvalidArgumentException('Onbekend type klant');
}

echo calculatePrice(100.0, 'regular') . "\n";
echo calculatePrice(100.0, 'student') . "\n";
echo calculatePrice(100.0, 'member') . "\n";
```

1. Zet dit om naar het Strategy pattern, met een interface `PriceStrategy` en een class per soort klant. De uitvoer blijft `100`, `90` en `85`.
2. Voeg een vierde soort klant toe, `senior` (20% korting), zonder een bestaande class of functie aan te passen. Schrijf in een comment op wat je wel en niet hebt moeten aanpassen.
3. Waarom is een closure (zoals in les 03) hier ook een mogelijke oplossing? Wanneer zou je toch voor classes kiezen?

**Hint:** De functie krijgt straks een `PriceStrategy` mee in plaats van een `string`.

### Opgave 3: Factory

Een applicatie verstuurt berichten via e-mail of sms, en kiest de class op twee plekken.

```PHP
interface Message
{
    public function send(string $text): string;
}

class EmailMessage implements Message
{
    public function send(string $text): string
    {
        return "E-mail: $text";
    }
}

class SmsMessage implements Message
{
    public function send(string $text): string
    {
        return "Sms: $text";
    }
}

function sendFromForm(string $channel, string $text): string
{
    if ($channel === 'email') {
        $message = new EmailMessage();
    } elseif ($channel === 'sms') {
        $message = new SmsMessage();
    } else {
        throw new InvalidArgumentException("Onbekend kanaal: $channel");
    }

    return $message->send($text);
}

function sendFromQueue(array $job): string
{
    switch ($job['channel']) {
        case 'email':
            return (new EmailMessage())->send($job['text']);
        case 'sms':
            return (new SmsMessage())->send($job['text']);
    }

    throw new InvalidArgumentException('Onbekend kanaal: ' . $job['channel']);
}

echo sendFromForm('email', 'Hallo') . "\n";
echo sendFromQueue(['channel' => 'sms', 'text' => 'Hallo']) . "\n";
```

1. Maak een `MessageFactory` met een method `create(string $channel): Message` en laat beide functies die gebruiken. De uitvoer blijft hetzelfde.
2. Voeg een kanaal `push` toe. Schrijf in een comment op hoeveel plekken je hebt moeten aanpassen, en vergelijk dat met de oorspronkelijke code.

**Hint:** Gebruik `match` zoals in de uitleg, of een `switch`. Het gaat erom dat de keuze op één plek staat.

### Opgave 4: Observer

Een bestelling roept na het plaatsen alle onderdelen zelf aan.

```PHP
class StockService
{
    public function reduce(string $product): void
    {
        echo "Voorraad van $product verlaagd\n";
    }
}

class InvoiceService
{
    public function create(string $product): void
    {
        echo "Factuur gemaakt voor $product\n";
    }
}

class MailService
{
    public function sendConfirmation(string $product): void
    {
        echo "Bevestiging gestuurd voor $product\n";
    }
}

class Order
{
    private StockService $stock;
    private InvoiceService $invoice;
    private MailService $mail;

    public function __construct(StockService $stock, InvoiceService $invoice, MailService $mail)
    {
        $this->stock = $stock;
        $this->invoice = $invoice;
        $this->mail = $mail;
    }

    public function place(string $product): void
    {
        $this->stock->reduce($product);
        $this->invoice->create($product);
        $this->mail->sendConfirmation($product);
    }
}

$order = new Order(new StockService(), new InvoiceService(), new MailService());
$order->place('Boek');
```

1. Zet de code om naar het Observer pattern met een interface `OrderObserver`. `Order` kent de drie onderdelen niet meer. De uitvoer blijft hetzelfde.
2. Voeg een vierde reactie toe: een `LogObserver` die `[LOG] Bestelling geplaatst: Boek` toont. Je mag `Order` daarvoor niet aanpassen.
3. Welk principe uit de eerdere lessen verbetert deze oplossing? Schrijf het op in een comment.

**Hint:** `Order` krijgt een `addObserver()` en roept bij `place()` alle observers aan. Wie ze toevoegt bepaal je buiten `Order`.
