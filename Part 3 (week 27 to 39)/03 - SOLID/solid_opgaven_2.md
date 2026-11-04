# Opgaven SOLID deel 2

Probeer de opgaven eerst zelf te maken. De uitleg in [solid_2.md](./solid_2.md) helpt je met de principes, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Interface Segregation

Een applicatie verstuurt notificaties op verschillende manieren.

```PHP
interface Notifier
{
    public function sendEmail(string $to, string $message): void;

    public function sendSms(string $phoneNumber, string $message): void;

    public function sendPush(string $deviceId, string $message): void;
}

class EmailNotifier implements Notifier
{
    public function sendEmail(string $to, string $message): void
    {
        echo "E-mail naar $to: $message\n";
    }

    public function sendSms(string $phoneNumber, string $message): void
    {
        throw new LogicException('Een EmailNotifier kan geen sms versturen');
    }

    public function sendPush(string $deviceId, string $message): void
    {
        throw new LogicException('Een EmailNotifier kan geen push bericht versturen');
    }
}

class SmsNotifier implements Notifier
{
    public function sendEmail(string $to, string $message): void
    {
        throw new LogicException('Een SmsNotifier kan geen e-mail versturen');
    }

    public function sendSms(string $phoneNumber, string $message): void
    {
        echo "Sms naar $phoneNumber: $message\n";
    }

    public function sendPush(string $deviceId, string $message): void
    {
        throw new LogicException('Een SmsNotifier kan geen push bericht versturen');
    }
}

function notifyByEmail(Notifier $notifier, string $to): void
{
    $notifier->sendEmail($to, 'Je boek is binnen');
}

notifyByEmail(new EmailNotifier(), 'anna@example.com');
```

1. Benoem in een comment wat er mis is met deze interface.
2. Splits de interface op en pas de classes aan, zodat geen enkele class een method heeft die alleen een exception gooit. `notifyByEmail()` moet blijven werken.
3. Voeg een `PushNotifier` toe die alleen een push bericht kan versturen.

**Hint:** Welke methods hebben `EmailNotifier` en `SmsNotifier` echt nodig? Maak voor elke manier van versturen een kleine interface.

### Opgave 2: Dependency Inversion

Een `OrderService` verwerkt een bestelling en schrijft een logregel weg.

```PHP
class ScreenLogger
{
    public function log(string $message): void
    {
        echo "[LOG] $message\n";
    }
}

class OrderService
{
    private ScreenLogger $logger;

    public function __construct()
    {
        $this->logger = new ScreenLogger();
    }

    public function placeOrder(string $product): void
    {
        $this->logger->log("Bestelling geplaatst: $product");
    }
}

$service = new OrderService();
$service->placeOrder('Boek');
```

1. Schrijf in een comment op wat er hier mis is. Er zijn twee problemen: één gaat over dependency injection, de ander over Dependency Inversion.
2. Los beide problemen op: maak een interface `Logger`, laat `OrderService` die interface via de constructor meekrijgen en laat `ScreenLogger` hem implementeren.
3. Maak een tweede uitwerking, `MemoryLogger`, die de berichten bewaart in een array in plaats van ze te tonen. Geef het een `getMessages(): array` method. Laat zien dat je met een `MemoryLogger` de `OrderService` kunt gebruiken zonder dat er iets op het scherm komt, en print daarna de bewaarde berichten.

**Hint:** Als een class een `new` gebruikt voor iets wat hij gebruikt, krijgt hij het niet van buiten. En als hij daarbij om een concrete class vraagt, kan hij geen andere uitwerking krijgen.
