# Opgaven DDD deel 2

Probeer de opgaven eerst zelf te maken. De uitleg in [ddd_2.md](./ddd_2.md) helpt je met de begrippen, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenan. Controleer na het aanpassen steeds dat je code nog hetzelfde doet als de oorspronkelijke code, tenzij de opgave anders zegt.

### Opgave 1: Aggregate

Een winkelwagen mag maximaal drie regels hebben, maar niets bewaakt dat.

```PHP
class CartLine
{
    private string $product;
    private int $quantity;

    public function __construct(string $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getProduct(): string
    {
        return $this->product;
    }
}

class Cart
{
    public array $lines = [];
}

$cart = new Cart();
$cart->lines[] = new CartLine('Boek', 1);
$cart->lines[] = new CartLine('Pen', 2);
$cart->lines[] = new CartLine('Schrift', 1);
$cart->lines[] = new CartLine('Liniaal', 1);

echo count($cart->lines) . " regels\n";
```

1. Maak van `Cart` de root van een aggregate: de lijst wordt `private` en er komt een method `add(string $product, int $quantity)` die de `CartLine` zelf aanmaakt en bij een vierde regel een `DomainException` gooit.
2. Laat zien dat de vier toevoegingen nu een foutmelding geven bij de vierde. De uitvoer is daarna `3 regels`.
3. Waarom is het goed dat de `CartLine` alleen in `Cart` wordt aangemaakt? Schrijf het in een comment.

**Hint:** Kijk naar `Library::lend()` in de uitleg, waar `Loan` alleen binnen `Library` wordt aangemaakt.

### Opgave 2: Repository

Een `Cart` slaat zichzelf op.

```PHP
class Cart
{
    private static array $storage = [];

    private array $products = [];

    public function add(string $product): void
    {
        $this->products[] = $product;
    }

    public function save(): void
    {
        self::$storage = $this->products;
    }

    public static function load(): Cart
    {
        $cart = new Cart();
        $cart->products = self::$storage;

        return $cart;
    }

    public function count(): int
    {
        return count($this->products);
    }
}

$cart = new Cart();
$cart->add('Boek');
$cart->save();

echo Cart::load()->count() . " product(en)\n";
```

1. Maak een interface `CartRepository` met `get(): Cart` en `save(Cart $cart): void`, en een `InMemoryCartRepository` die hem implementeert. Haal `save()` en `load()` uit `Cart`.
2. Maak een class `AddToCart` (applicatielaag) die een product toevoegt via de repository. De uitvoer blijft `1 product(en)`.
3. Maak een tweede uitwerking van `CartRepository`, bijvoorbeeld `LoggingCartRepository` die een bericht toont bij elke `save()` en intern een andere repository gebruikt. Laat zien dat `AddToCart` en `Cart` niet hoeven te veranderen.

**Hint:** De interface hoort bij het domein, de uitwerking bij de infrastructuur.

### Opgave 3: Domain event

Een `Order` verstuurt zelf een e-mail als hij is betaald.

```PHP
class Mailer
{
    public function send(string $to, string $message): void
    {
        echo "E-mail naar $to: $message\n";
    }
}

class Order
{
    private Mailer $mailer;
    private bool $paid = false;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function pay(string $customer): void
    {
        $this->paid = true;
        $this->mailer->send($customer, 'Bedankt voor je betaling');
    }
}

(new Order(new Mailer()))->pay('Anna');
```

1. Maak een event `OrderPaid` met een `customer`, in de verleden tijd en `readonly`.
2. Laat `Order` het event alleen verzamelen (`pullEvents()`). `Order` krijgt geen `Mailer` meer.
3. Laat de applicatie het event afhandelen: stuur de e-mail en toon `[LOG] Order betaald door Anna`. De uitvoer bevat daarna beide regels.
4. Welk pattern uit les 06 komt hier terug? Schrijf het in een comment.

**Hint:** Kijk naar `domain_event_na.php`. De lijst met listeners staat buiten `Order`.

### Opgave 4: Anemic model naar rijk model

Een supportticket heeft alleen getters en setters, en de regels staan in een service.

```PHP
class Ticket
{
    private string $title;
    private string $status = 'open';

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}

class TicketService
{
    public function close(Ticket $ticket): string
    {
        if ($ticket->getStatus() === 'closed') {
            return 'Het ticket is al gesloten';
        }

        $ticket->setStatus('closed');

        return 'Ticket gesloten: ' . $ticket->getTitle();
    }

    public function reopen(Ticket $ticket): string
    {
        if ($ticket->getStatus() !== 'closed') {
            return 'Het ticket is niet gesloten';
        }

        $ticket->setStatus('open');

        return 'Ticket heropend: ' . $ticket->getTitle();
    }
}

$ticket = new Ticket('Inloggen lukt niet');
$service = new TicketService();

echo $service->close($ticket) . "\n";
echo $service->close($ticket) . "\n";
echo $service->reopen($ticket) . "\n";

// niets houdt andere code tegen om dit te doen
$ticket->setStatus('wacht-op-klant-en-gesloten');
```

1. Schrijf in een comment wat er mis is met deze opzet.
2. Verplaats de regels naar `Ticket`: maak methods `close()` en `reopen()` die een `DomainException` gooien als het niet mag. Haal `setStatus()` weg. De uitvoer van het script blijft hetzelfde: vang de exception in het script af en toon dezelfde berichten als eerst.
3. Hoeft `TicketService` nog te bestaan? Schrijf je antwoord in een comment.

**Hint:** Je kunt de uitleg bij `Book::lendTo()` als voorbeeld gebruiken. Een status die niet bestaat kan nu niet meer worden gezet.
