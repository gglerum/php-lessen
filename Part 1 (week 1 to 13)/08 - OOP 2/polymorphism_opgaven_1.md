# Opgaven polymorphism

Probeer de opgaven eerst zelf te maken. De voorbeelden in [polymorphism.md](./polymorphism.md) helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Opgave 3 en 4 bouwen voort op opgave 2: kopieer je classes van daar.

### Opgave 1: Dieren

Hieronder staat een abstracte class `Animal` met drie children:

```PHP
abstract class Animal
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function makeSound(): string;
}

class Dog extends Animal
{
    public function makeSound(): string
    {
        return "Woef";
    }
}

class Cat extends Animal
{
    public function makeSound(): string
    {
        return "Miauw";
    }
}

class Cow extends Animal
{
    public function makeSound(): string
    {
        return "Boe";
    }
}
```

Schrijf een functie `introduce(Animal $animal): string` die bijvoorbeeld `Rex zegt Woef` teruggeeft. Maak een array met `new Dog("Rex")`, `new Cat("Minoes")` en `new Cow("Bertha")` en toon in een loop wat `introduce()` voor elk dier teruggeeft:

```
Rex zegt Woef
Minoes zegt Miauw
Bertha zegt Boe
```

Voeg daarna een `Duck` toe die `Kwak` zegt. Wat moet je aan `introduce()` en de loop veranderen? Schrijf het antwoord in een comment.

**Hint:** De functie hoeft alleen `getName()` en `makeSound()` aan te roepen, beide staan in `Animal`. De rest van de code weet niet of het een hond of een eend is.

### Opgave 2: Bioscoop

Een bioscoop verkoopt verschillende tickets. Hieronder staat de parent met drie children:

```PHP
abstract class Ticket
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getPrice(): int;
}

class AdultTicket extends Ticket
{
    public function getPrice(): int
    {
        return 12;
    }
}

class ChildTicket extends Ticket
{
    public function getPrice(): int
    {
        return 7;
    }
}

class GroupTicket extends Ticket
{
    private int $people;

    public function __construct(string $name, int $people)
    {
        parent::__construct($name);
        $this->people = $people;
    }

    public function getPrice(): int
    {
        return $this->people * 9;
    }
}
```

Schrijf een class `Order` met een `private` array met tickets, een method `add(Ticket $ticket): void` en een method `getTotal(): int` die de prijzen van alle tickets bij elkaar optelt. Voeg een `AdultTicket("Anna")`, een `ChildTicket("Tim")` en een `GroupTicket("Klas 2B", 4)` toe en toon het totaal:

```
Totaal: 55
```

Voeg daarna een `StudentTicket` toe met prijs 8 zonder iets aan `Order` te veranderen. Wat verandert er aan het totaal als je er een toevoegt?

**Hint:** `add()` en `getTotal()` weten niets van de soorten tickets, alleen van `Ticket`. In `getTotal()` loop je over de tickets en tel je `getPrice()` op.

### Opgave 3: Geen ticket

Probeer in je `Order` een object toe te voegen dat geen ticket is, bijvoorbeeld van de class `Customer`. Vang de fout op en toon de melding van PHP. Controleer dat het totaal daarna nog klopt.

Voeg daarna een method `getCheapest(): ?Ticket` toe aan `Order`. Die geeft het goedkoopste ticket terug, of `null` als er nog geen tickets zijn. Toon bij een lege `Order` de tekst `De bestelling is leeg`. Toon bij de bestelling uit opgave 2 de naam van het goedkoopste ticket:

```
Geen ticket: Order::add(): Argument #1 ($ticket) must be of type Ticket, Customer given
Totaal: 55
De bestelling is leeg
Goedkoopste: Tim
```

**Hint:** Voor de melding gebruik je `catch (TypeError $error)` en `$error->getMessage()`. De melding van PHP is langer dan hierboven, omdat er ook staat waar de fout gebeurde. Je mag hem afkappen met `strstr($error->getMessage(), ', called in', true)`. Je kunt `getCheapest()` bijhouden met een variabele die begint op `null`.

### Opgave 4: Bezoekers

`GroupTicket` heeft een `getPeople()` nodig: een `GroupTicket` is voor meerdere mensen. Voeg die method toe aan `GroupTicket`. Schrijf daarna in `Order` een method `countVisitors(): int` die telt hoeveel mensen er naar de bioscoop komen. Een `AdultTicket` en `ChildTicket` tellen voor 1, een `GroupTicket` voor het aantal personen. Gebruik hiervoor `instanceof`. Met de tickets uit opgave 2 is het antwoord 6:

```
Bezoekers: 6
```

Los het daarna op zonder `instanceof`, in een tweede method `countVisitorsPolymorphic(): int`. Schrijf in een comment wat er in `Ticket` en in `GroupTicket` moet komen en waarom dit beter is dan `instanceof`. Wat gebeurt er met `countVisitors()` als er een `StudentTicket` bijkomt?

**Hint:** Bij de tweede versie krijgt `Ticket` een gewone method `getVisitors(): int` die `1` teruggeeft. Welke child moet deze overschrijven?

### Opgave 5: Voorspel de uitvoer

Hieronder staan vijf stukjes code. Gebruik de classes van opgave 2. Bepaal bij elk stukje wat PHP doet voordat je het draait en schrijf het antwoord in een comment. Zet daarna de code in een bestand en kijk of je gelijk had.

**a)**
```PHP
$ticket = new ChildTicket("Tim");

var_dump($ticket instanceof Ticket);
var_dump($ticket instanceof AdultTicket);
```

**b)**
```PHP
function showName(AdultTicket $ticket): void
{
    echo $ticket->getName() . "\n";
}

showName(new ChildTicket("Tim"));
```

Wat verandert er als je het parameter type `Ticket` maakt?

**c)**
```PHP
function showPrice(?Ticket $ticket): void
{
    echo $ticket->getPrice() . "\n";
}

showPrice(new GroupTicket("Klas 2B", 3));
showPrice(null);
```

**d)**
```PHP
$tickets = [new AdultTicket("Anna"), new ChildTicket("Tim")];

foreach ($tickets as $ticket) {
    echo $ticket::class . ": " . $ticket->getPrice() . "\n";
}
```

**e)**
```PHP
$ticket = new AdultTicket("Anna");

echo $ticket->getPeople();
```

**Hint:** Bij elke vraag helpt het te bedenken: welk type verwacht de parameter, wat is het object echt, en welke methods heeft het type dat PHP kent?
