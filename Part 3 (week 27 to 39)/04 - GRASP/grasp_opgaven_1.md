# Opgaven GRASP

Probeer de opgaven eerst zelf te maken. De uitleg in [grasp.md](./grasp.md) helpt je met de principes, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Information Expert

Een webshop berekent het totaal van een bestelling buiten de bestelling zelf.

```PHP
class OrderLine
{
    private string $product;
    private float $price;
    private int $quantity;

    public function __construct(string $product, float $price, int $quantity)
    {
        $this->product = $product;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

class Order
{
    private array $lines = [];

    public function addLine(OrderLine $line): void
    {
        $this->lines[] = $line;
    }

    public function getLines(): array
    {
        return $this->lines;
    }
}

function printTotal(Order $order): void
{
    $total = 0.0;

    foreach ($order->getLines() as $line) {
        $total += $line->getPrice() * $line->getQuantity();
    }

    echo "Totaal: $total\n";
}

$order = new Order();
$order->addLine(new OrderLine('Boek', 12.5, 2));
$order->addLine(new OrderLine('Pen', 1.5, 4));
printTotal($order);
```

1. Welke class heeft de gegevens voor de berekening van het totaal van een regel, en welke voor het totaal van de bestelling? Schrijf het op in een comment.
2. Verplaats de berekeningen naar de juiste classes. `printTotal()` toont daarna alleen nog het resultaat. De uitvoer blijft `Totaal: 31`.

**Hint:** Een `OrderLine` kent prijs en aantal. Een `Order` kent de regels.

### Opgave 2: Creator

Een winkelmand wordt door de code erbuiten gevuld.

```PHP
class CartLine
{
    private string $product;
    private float $price;
    private int $quantity;

    public function __construct(string $product, float $price, int $quantity)
    {
        $this->product = $product;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function describe(): string
    {
        return $this->quantity . 'x ' . $this->product . ' (' . $this->price . ' per stuk)';
    }
}

class Cart
{
    private array $lines = [];

    public function addLine(CartLine $line): void
    {
        $this->lines[] = $line;
    }

    public function describe(): array
    {
        return array_map(fn(CartLine $line) => $line->describe(), $this->lines);
    }
}

$cart = new Cart();
$cart->addLine(new CartLine('Boek', 12.5, 2));
$cart->addLine(new CartLine('Pen', 1.5, 4));

echo implode("\n", $cart->describe()) . "\n";
```

1. Welke class houdt de `CartLine` objecten bij? Schrijf in een comment waarom die class ze ook zou moeten aanmaken.
2. Pas `Cart` aan met een method `add(string $product, float $price, int $quantity)` die de `CartLine` zelf aanmaakt. Pas de aanroepen onderaan aan. De uitvoer blijft hetzelfde.
3. Wat is een nadeel van deze aanpak? Schrijf het op in een comment.

**Hint:** Bij het nadeel kun je denken aan wat er gebeurt als een `CartLine` straks meer gegevens nodig heeft, zoals een korting.

### Opgave 3: Controller

Een parkeergarage rekent bij het uitrijden af. De controller doet alles.

```PHP
class ParkingController
{
    public function handleExit(array $request): void
    {
        $ticket = $request['ticket'] ?? '';
        $hours = (int)($request['hours'] ?? 0);

        if ($ticket === '') {
            echo "Voer een kaartnummer in\n";
            return;
        }

        $fee = $hours * 2.5;

        if ($fee > 20.0) {
            $fee = 20.0;
        }

        echo "Kaart $ticket: $fee euro te betalen\n";
    }
}

$controller = new ParkingController();
$controller->handleExit(['ticket' => 'A12', 'hours' => 3]);
$controller->handleExit(['ticket' => 'A13', 'hours' => 12]);
$controller->handleExit(['ticket' => '', 'hours' => 1]);
```

1. Wat hoort bij een controller en wat is een bedrijfsregel van de parkeergarage? Schrijf het op in een comment.
2. Maak een class `ParkingGarage` met een method die het te betalen bedrag berekent, inclusief het maximum per dag. Laat de controller deze class gebruiken en alleen nog het verzoek lezen, doorgeven en tonen. De uitvoer blijft hetzelfde.
3. Maak een tweede script dat ook een bedrag moet berekenen, maar geen controller gebruikt. Bewijs dat je de regels niet hoefde te kopiëren.

**Hint:** Het tarief en het dagmaximum zijn regels van de parkeergarage. De controller weet niet hoeveel het kost.

### Opgave 4: Low Coupling

Een school toont de e-mail van de docent van een klas.

```PHP
class Teacher
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

class SchoolClass
{
    private string $name;
    private Teacher $teacher;

    public function __construct(string $name, Teacher $teacher)
    {
        $this->name = $name;
        $this->teacher = $teacher;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTeacher(): Teacher
    {
        return $this->teacher;
    }
}

class School
{
    private array $classes;

    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }
}

$school = new School([new SchoolClass('1A', new Teacher('meester@example.com'))]);

echo 'De docent van ' . $school->getClasses()[0]->getName() . ' is te bereiken op ' . $school->getClasses()[0]->getTeacher()->getEmail() . "\n";
```

1. Welke classes kent de laatste regel? Wat gebeurt er als een klas straks twee docenten krijgt?
2. Verlaag de koppeling zodat het script niet meer langs `Teacher` hoeft. De uitvoer blijft hetzelfde.

**Hint:** Laat `SchoolClass` zelf het e-mailadres van zijn docent geven, en laat `School` de eerste klas teruggeven.

### Opgave 5: Pure Fabrication

Een `Student` weet ook hoe hij wordt opgeslagen.

```PHP
class Student
{
    private static array $storage = [];

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function save(): void
    {
        self::$storage[$this->name] = $this;
    }

    public static function findByName(string $name): ?Student
    {
        return self::$storage[$name] ?? null;
    }
}

(new Student('Anna'))->save();
(new Student('Bram'))->save();

echo Student::findByName('Anna')?->getName() . "\n";
echo (Student::findByName('Chris')?->getName() ?? 'Niet gevonden') . "\n";
```

1. Haal het opslaan uit `Student` en zet het in een nieuwe class `StudentRepository`. De uitvoer blijft hetzelfde.
2. Information Expert zegt dat `Student` zichzelf zou opslaan, omdat hij de gegevens heeft. Schrijf in een comment waarom je het toch hebt verplaatst en wanneer je dit in een klein script juist niet zou doen.

**Hint:** Een repository bestaat niet in een school. Hij is verzonnen om iets anders los te houden.
