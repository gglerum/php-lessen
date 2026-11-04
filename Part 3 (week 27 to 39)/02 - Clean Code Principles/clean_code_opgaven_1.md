# Opgaven clean code

Probeer de opgaven eerst zelf te maken. De uitleg in [clean_code.md](./clean_code.md) helpt je met de principes, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: Namen

Deze functie berekent het totaal van een bestelling (prijs maal aantal) en geeft 10% korting aan klanten die al meer dan 50 maanden klant zijn.

```PHP
function process(array $d, int $t): float
{
    $tmp = 0.0;

    foreach ($d as $x) {
        $tmp += $x['p'] * $x['q'];
    }

    if ($t > 50) {
        $tmp = $tmp - ($tmp * 0.1);
    }

    return $tmp;
}

$data = [['p' => 4.5, 'q' => 2], ['p' => 10.0, 'q' => 6]];
echo process($data, 60) . "\n";
```

Geef alle functies, variabelen, keys en getallen een duidelijke naam. Het resultaat voor de gegeven data blijft `62.1`. Kies drie namen uit en schrijf in een comment op waarom je ze zo hebt genoemd.

**Hint:** Begin met de naam van de functie. Als je die goed kunt kiezen, wordt de rest vanzelf duidelijker. Heeft een getal een betekenis? Dan hoort daar een naam bij.

### Opgave 2: Functies

Deze functie handelt het afrekenen van een winkelmand af.

```PHP
function checkout(array $cart, bool $isMember, bool $printReceipt): string
{
    if (count($cart) > 0) {
        if ($isMember) {
            if (array_sum($cart) > 20) {
                $result = 'Gratis verzending';
            } else {
                $result = 'Verzending kost 4.95';
            }

            if ($printReceipt) {
                echo "Bon geprint\n";
            }

            return $result;
        } else {
            return 'Alleen voor leden';
        }
    } else {
        return 'De winkelmand is leeg';
    }
}

echo checkout([10.0, 15.0], true, true) . "\n";
```

Herschrijf de functie met early returns. Haal de `bool` die de bon print weg en maak er een aparte functie van, die de aanroeper zelf kan aanroepen. Het resultaat van `checkout()` blijft hetzelfde voor elke combinatie van een lege of gevulde winkelmand, een lid of geen lid, en een bedrag onder of boven de 20.

**Hint:** Begin met de eerste uitzondering: wat is het eerste dat fout kan gaan? Handel dat meteen af en ga dan door met de volgende.

### Opgave 3: Comments

In dit script staat veel commentaar.

```PHP
// cijferlijst
$grades = [7.5, 4.0, 9.0, 5.5];

// functie om te kijken of een cijfer voldoende is
function isPassed(float $grade): bool
{
    // als het cijfer 5.5 of hoger is
    if ($grade >= 5.5) {
        // geef true terug
        return true;
    }

    // anders geef false terug
    return false;
}

// tel het aantal voldoendes
$passed = 0;

// loop door alle cijfers
foreach ($grades as $grade) {
    // als het cijfer voldoende is
    if (isPassed($grade)) {
        // tel 1 op
        $passed++;
    }
}

// toon het aantal voldoendes
echo "Aantal voldoendes: $passed\n";
```

1. Haal alle comments weg die alleen herhalen wat de code al zegt.
2. Staat er een comment die je kunt vervangen door een betere naam? Pas de naam aan en haal de comment weg.
3. Voeg één comment toe die het *waarom* uitlegt: waarom is 5.5 de grens? Verzin zelf een geloofwaardige reden, zoals een regel uit de studiegids.
4. Schrijf in een comment bovenaan waarom je in de checklist van Part 1 toch comments zou schrijven en in een echt project niet.

**Hint:** Lees de code zonder de comments. Snap je nog wat er gebeurt? Dan was de comment overbodig.

### Opgave 4: Least Astonishment

Elk van deze drie functies doet iets anders dan de naam zegt.

```PHP
$grades = [7.5, 4.0, 9.0, 5.5];
$students = ['Anna', 'Bram'];

function getAverage(array &$grades): float
{
    sort($grades);

    return array_sum($grades) / count($grades);
}

function isPassed(float $grade): bool
{
    echo "Het cijfer is " . $grade . "\n";

    return $grade >= 5.5;
}

function getStudentName(array &$students, int $id): string
{
    if (!isset($students[$id])) {
        $students[$id] = 'Onbekend';
    }

    return $students[$id];
}

echo getAverage($grades) . "\n";
echo isset($grades[0]) ? $grades[0] : '', "\n";
echo isPassed(6.0) ? "Voldoende\n" : "Onvoldoende\n";
echo getStudentName($students, 5) . "\n";
echo count($students) . "\n";
```

1. Zoek bij elke functie op wat er onverwacht gebeurt. Schrijf het met een comment op (bijvoorbeeld: `getAverage()` sorteert ook de lijst met cijfers).
2. Pas elke functie aan zodat hij doet wat de naam zegt. Als er iets bij hoort dat je wel wilt behouden, maak er een aparte functie met een eigen naam van.
3. Pas de aanroepen onderaan aan zodat het script nog steeds dezelfde regels op het scherm zet, maar nu omdat de aanroeper dat bewust zelf doet (bijvoorbeeld door zelf `sort()` aan te roepen) in plaats van als verrassing.

**Hint:** Een functie die `get` of `is` heet verandert niets en toont niets. Vraag je bij elke regel af of die in de naam past.
