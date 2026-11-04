# Opgaven design patterns deel 3

Probeer de opgaven eerst zelf te maken. De uitleg in [patterns_3.md](./patterns_3.md) helpt je met de patterns, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Builder

Een pizzeria maakt bestellingen met een constructor van zes parameters.

```PHP
class Pizza
{
    private string $size;
    private string $crust;
    private bool $cheese;
    private bool $mushrooms;
    private bool $pepperoni;
    private bool $extraSauce;

    public function __construct(
        string $size,
        string $crust,
        bool $cheese,
        bool $mushrooms,
        bool $pepperoni,
        bool $extraSauce
    ) {
        $this->size = $size;
        $this->crust = $crust;
        $this->cheese = $cheese;
        $this->mushrooms = $mushrooms;
        $this->pepperoni = $pepperoni;
        $this->extraSauce = $extraSauce;
    }

    public function describe(): string
    {
        $toppings = array_filter([
            $this->cheese ? 'kaas' : null,
            $this->mushrooms ? 'champignons' : null,
            $this->pepperoni ? 'pepperoni' : null,
            $this->extraSauce ? 'extra saus' : null,
        ]);

        return "Pizza {$this->size}, {$this->crust} bodem, met " . ($toppings ? implode(', ', $toppings) : 'niets extra');
    }
}

echo (new Pizza('groot', 'dun', true, false, true, false))->describe() . "\n";
echo (new Pizza('klein', 'dik', true, false, false, false))->describe() . "\n";
```

1. Maak een `PizzaBuilder` met standaardwaarden (formaat `medium`, bodem `normaal`, met kaas) en methods als `size()`, `crust()`, `withMushrooms()`, `withPepperoni()` en `withExtraSauce()`. De class `Pizza` blijft zoals hij is.
2. Bouw beide pizza's uit het voorbeeld met de builder. De uitvoer blijft hetzelfde. Voor de tweede pizza moet je dus ook `size('klein')` en `crust('dik')` aanroepen.
3. Waarom zou je hier ook *named arguments* kunnen gebruiken? Wanneer is de builder dan beter?

**Hint:** Elke method geeft `$this` terug, en `build()` maakt de `Pizza` aan.

### Opgave 2: Chain of Responsibility

Een wachtwoord wordt gecontroleerd in één functie.

```PHP
function validatePassword(string $password): string
{
    if (strlen($password) < 8) {
        return 'Het wachtwoord is te kort';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return 'Het wachtwoord bevat geen hoofdletter';
    }

    if (!preg_match('/[0-9]/', $password)) {
        return 'Het wachtwoord bevat geen cijfer';
    }

    return 'ok';
}

echo validatePassword('kort') . "\n";
echo validatePassword('langgenoeg') . "\n";
echo validatePassword('Langgenoeg') . "\n";
echo validatePassword('Langgenoeg1') . "\n";
```

1. Zet de controles om in een ketting, met een class per controle. De uitvoer blijft hetzelfde.
2. Bouw een tweede ketting voor een "beheerder" met een extra controle (minimaal 12 tekens) en zonder de controle op een cijfer. Je mag geen bestaande controle aanpassen.

**Hint:** Je kunt `LoanCheck` uit de uitleg als voorbeeld gebruiken, maar noem je classes zelf.

### Opgave 3: Command

Een teksteditor kan tekst toevoegen, maar niets ongedaan maken.

```PHP
class Document
{
    private string $text = '';

    public function append(string $text): void
    {
        $this->text .= $text;
    }

    public function removeLast(int $length): void
    {
        $this->text = substr($this->text, 0, -$length);
    }

    public function getText(): string
    {
        return $this->text;
    }
}

$document = new Document();
$document->append('Hallo');
$document->append(' wereld');
echo $document->getText() . "\n";
```

1. Maak een interface `Command` met `execute()` en `undo()` en een class `AppendText` die tekst toevoegt en bij `undo()` weer verwijdert.
2. Maak een `History` (zoals in de uitleg) en gebruik die om twee stukken tekst toe te voegen. Print de tekst, maak de laatste actie ongedaan en print opnieuw. De tekst wordt `Hallo wereld` en daarna `Hallo`.
3. Voeg een tweede command toe, `ClearText`, dat alle tekst wist en bij `undo()` terugzet. Wat moet dit command onthouden om `undo()` te kunnen doen?

**Hint:** Een command krijgt het `Document` en zijn gegevens via de constructor mee.

### Opgave 4: Template Method

Twee exporters herhalen dezelfde stappen.

```PHP
class CsvExporter
{
    public function export(array $rows): string
    {
        $output = "naam,leeftijd\n";

        foreach ($rows as $row) {
            $output .= $row['name'] . ',' . $row['age'] . "\n";
        }

        $output .= 'aantal: ' . count($rows) . "\n";

        return $output;
    }
}

class TextExporter
{
    public function export(array $rows): string
    {
        $output = "Naam | Leeftijd\n";

        foreach ($rows as $row) {
            $output .= $row['name'] . ' | ' . $row['age'] . "\n";
        }

        $output .= 'Aantal: ' . count($rows) . "\n";

        return $output;
    }
}

$rows = [['name' => 'Anna', 'age' => 30], ['name' => 'Bram', 'age' => 25]];

echo (new CsvExporter())->export($rows);
echo (new TextExporter())->export($rows);
```

1. Maak een abstracte class `Exporter` met een `final` method `export()` die de volgorde bepaalt (kop, regels, totaal). De subclasses vullen alleen de stappen in. De uitvoer blijft hetzelfde.
2. Voeg een `JsonExporter` toe zonder `Exporter` aan te passen. Je mag zelf bepalen hoe de uitvoer eruit ziet, maar de volgorde van de stappen mag niet in `JsonExporter` staan.

**Hint:** Welke stappen verschillen per exporter, en welke zijn voor alle exporters gelijk?

### Opgave 5: State

Een snoepautomaat controleert zijn toestand in elke method.

```PHP
class VendingMachine
{
    private string $state = 'no_coin';

    public function insertCoin(): string
    {
        if ($this->state === 'no_coin') {
            $this->state = 'has_coin';

            return 'Munt geaccepteerd';
        } elseif ($this->state === 'has_coin') {
            return 'Er zit al een munt in';
        }

        return 'Een moment, er wordt snoep uitgegeven';
    }

    public function pressButton(): string
    {
        if ($this->state === 'has_coin') {
            $this->state = 'no_coin';

            return 'Snoep uitgegeven';
        } elseif ($this->state === 'no_coin') {
            return 'Gooi eerst een munt in';
        }

        return 'Een moment, er wordt snoep uitgegeven';
    }
}

$machine = new VendingMachine();
echo $machine->pressButton() . "\n";
echo $machine->insertCoin() . "\n";
echo $machine->insertCoin() . "\n";
echo $machine->pressButton() . "\n";
```

1. Zet dit om naar het State pattern met een interface `MachineState` en classes `NoCoinState` en `HasCoinState`. De uitvoer blijft hetzelfde: `Gooi eerst een munt in`, `Munt geaccepteerd`, `Er zit al een munt in` en `Snoep uitgegeven`.
2. In de oorspronkelijke code is er een derde toestand die nooit wordt gebruikt (de laatste `return` in beide methods). Is die in jouw oplossing nodig? Schrijf je antwoord in een comment.
3. Wat is het verschil tussen State en Strategy? Schrijf het in je eigen woorden in een comment.

**Hint:** De `VendingMachine` geeft het werk door aan zijn huidige toestand, en de toestand verandert zichzelf via `changeState()`.
