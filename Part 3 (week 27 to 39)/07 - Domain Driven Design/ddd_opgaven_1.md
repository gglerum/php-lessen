# Opgaven DDD deel 1

Probeer de opgaven eerst zelf te maken. De uitleg in [ddd_1.md](./ddd_1.md) helpt je met de begrippen, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde doet als de oorspronkelijke code.

### Opgave 1: Ubiquitous language

Een medewerker van een restaurant zegt: "een gast reserveert een tafel voor een aantal personen". In de code staat dit:

```PHP
class Thing
{
    private int $size;

    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}

class Person
{
    private string $name;
    private array $stuff = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function doStuff(Thing $thing, int $number): void
    {
        $this->stuff[] = ['thing' => $thing, 'number' => $number];
    }

    public function describe(): string
    {
        $parts = [];

        foreach ($this->stuff as $item) {
            $parts[] = $item['number'] . ' personen aan een tafel van ' . $item['thing']->getSize();
        }

        return $this->name . ': ' . implode(', ', $parts);
    }
}

$person = new Person('Anna');
$person->doStuff(new Thing(4), 3);
echo $person->describe() . "\n";
```

1. Schrijf in een comment op welke woorden uit de code bij welke woorden van de medewerker horen.
2. Herschrijf de code met de woorden van de medewerker. De uitvoer blijft `Anna: 3 personen aan een tafel van 4`.
3. Waarom is dit niet alleen een kwestie van mooiere namen? Schrijf in een comment wat er misgaat als de medewerker en de ontwikkelaar verschillende woorden gebruiken.

**Hint:** Kies de woorden van de medewerker (gast, tafel, reserveren), ook als je ze niet zelf zou hebben bedacht.

### Opgave 2: Entity of value object?

Een webshop heeft de volgende begrippen: een klant, een bestelling, een postcode, een prijs in euro's, een winkelwagenregel (product en aantal) en een bezorgadres.

1. Zet elk begrip in een tabel met een kolom "entity of value object" en een kolom "waarom". Gebruik de vuistregel uit de les: zijn twee exemplaren met dezelfde gegevens uitwisselbaar?
2. Kies er één uit waarover je twijfelt. Schrijf op in welke situatie het een entity zou zijn en in welke situatie een value object.
3. Hieronder staat een `Customer` zonder identiteit. Pas de class zo aan dat twee verschillende klanten met dezelfde naam niet als dezelfde klant worden gezien, en dat een klant die van naam verandert dezelfde klant blijft.

```PHP
class Customer
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }
}

$first = new Customer('Anna');
$second = new Customer('Anna');

echo $first == $second ? "Gelijk\n" : "Verschillend\n";

$before = clone $first;
$first->rename('Anna de Vries');

echo $first == $before ? "Gelijk\n" : "Verschillend\n";
```

Nu is de uitvoer `Gelijk` en `Verschillend`. Na jouw aanpassing en een `equals()` method moet het resultaat voor dezelfde stappen `Verschillend` en `Gelijk` worden.

**Hint:** Een entity krijgt een `id` mee in de constructor en `equals()` vergelijkt alleen dat.

### Opgave 3: Een eigen value object

Een webshop bewaart het e-mailadres van een klant als string.

```PHP
function sendWelcomeMail(string $email): string
{
    return "Welkomstmail verstuurd naar $email";
}

echo sendWelcomeMail('anna@example.com') . "\n";
echo sendWelcomeMail('geen-geldig-adres') . "\n";
echo sendWelcomeMail('Anna de Vries') . "\n";
```

1. Maak een value object `EmailAddress` dat bij het aanmaken controleert of het adres geldig is (gebruik `filter_var($value, FILTER_VALIDATE_EMAIL)`) en anders een `InvalidArgumentException` gooit. Maak de waarde `readonly`.
2. Voeg een `equals()` method toe die twee adressen vergelijkt zonder op hoofdletters te letten (`Anna@Example.com` is hetzelfde adres als `anna@example.com`).
3. Pas `sendWelcomeMail()` aan zodat hij een `EmailAddress` vraagt. Wat gebeurt er nu met de tweede en derde aanroep?

**Hint:** Het idee is dat een ongeldig adres niet kan bestaan. Je controleert dus één keer, bij het aanmaken.
