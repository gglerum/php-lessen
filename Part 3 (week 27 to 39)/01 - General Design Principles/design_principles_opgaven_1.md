# Opgaven design principles

Probeer de opgaven eerst zelf te maken. De uitleg in [design_principles.md](./design_principles.md) helpt je met de principes, maar de opdrachten gebruiken andere voorbeelden. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Controleer na het aanpassen steeds dat je code nog hetzelfde resultaat geeft als de oorspronkelijke code.

### Opgave 1: KISS

Deze functie berekent het totaalbedrag van een kassabon. Hij werkt, maar is veel ingewikkelder dan nodig.

```PHP
function calculateTotal(array $prices): float
{
    $total = 0.0;
    $index = 0;
    $count = count($prices);

    while ($index < $count) {
        if ($prices[$index] > 0) {
            $total = $total + $prices[$index];
        } else {
            $total = $total + 0;
        }
        $index = $index + 1;
    }

    $result = $total;
    return $result;
}

echo calculateTotal([2.5, 3.0, 1.25]) . "\n";
```

Maak de functie zo eenvoudig mogelijk. Het resultaat voor `[2.5, 3.0, 1.25]` blijft `6.75`. Schrijf in een comment op welke onderdelen je hebt weggehaald en waarom ze niets toevoegden.

**Hint:** Begin met schrappen wat niets doet. Daarna kun je kijken of PHP al een functie heeft die optellen voor je doet.

### Opgave 2: DRY

Een webshop berekent de prijs inclusief btw en de korting voor verschillende klanten.

```PHP
function foodPriceWithVat(float $price): float
{
    return $price + ($price * 0.09);
}

function drinkPriceWithVat(float $price): float
{
    return $price + ($price * 0.21);
}

function bookPriceWithVat(float $price): float
{
    return $price + ($price * 0.09);
}

function getStudentDiscount(float $price): float
{
    return $price * 0.10;
}

function getSeniorDiscount(float $price): float
{
    return $price * 0.10;
}
```

1. Voeg de eerste drie functies samen tot één functie. Roep hem vervolgens aan voor eten, drinken en een boek, met een prijs van `10.00`.
2. De laatste twee functies zijn nu identiek. Voeg je ze samen of laat je ze staan? Schrijf je keuze in een comment boven de functies en leg uit waarom.

**Hint:** Bij de eerste vraag verschillen de functies maar op één plek. Bij de tweede vraag helpt het om te bedenken wat er gebeurt als één van beide regels verandert.

### Opgave 3: YAGNI

Een student begint met een eenvoudige quiz en bouwt alvast een aantal dingen voor later.

```PHP
class QuizSettings
{
    private int $maxQuestions = 3;
    private string $language = 'nl';
    private string $theme = 'light';
    private int $timeLimitInSeconds = 60;
    private bool $shuffleQuestions = false;
    private string $difficulty = 'easy';

    public function getMaxQuestions(): int
    {
        return $this->maxQuestions;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getTimeLimitInSeconds(): int
    {
        return $this->timeLimitInSeconds;
    }

    public function shouldShuffleQuestions(): bool
    {
        return $this->shuffleQuestions;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }
}

$settings = new QuizSettings();
$questions = ['Hoofdstad van Nederland?', 'Hoeveel dagen heeft een week?', 'Welke kleur heeft gras?', 'Hoeveel benen heeft een spin?'];

for ($i = 0; $i < $settings->getMaxQuestions(); $i++) {
    echo $questions[$i] . "\n";
}
```

Zet in een comment een lijstje welke onderdelen van `QuizSettings` worden gebruikt en welke niet. Schrijf daarna de code om zodat alleen overblijft wat nodig is. Voeg als laatste, zonder de rest te veranderen, het schudden van de vragen toe alsof het nu wel is gevraagd. Wat valt je op aan hoeveel werk dat is?

**Hint:** Kijk naar welke methods ergens worden aangeroepen. Alles wat nergens wordt aangeroepen is code voor later.

### Opgave 4: Separation of Concerns

Dit is een quiz in de terminal waarin alles door elkaar staat.

```PHP
$questions = [
    ['question' => 'Hoofdstad van Nederland?', 'answer' => 'amsterdam'],
    ['question' => 'Hoeveel dagen heeft een week?', 'answer' => '7'],
    ['question' => 'Welke kleur heeft gras?', 'answer' => 'groen'],
];
$score = 0;

foreach ($questions as $question) {
    echo $question['question'] . " ";
    $answer = strtolower(trim(fgets(STDIN)));

    if ($answer === $question['answer']) {
        $score++;
        echo "Goed!\n";
    } else {
        echo "Fout, het antwoord was " . $question['answer'] . "\n";
    }
}

echo "Je score is $score van de " . count($questions) . "\n";
```

1. Maak een class `Quiz` die de vragen en de score bijhoudt en alleen de spellogica bevat: geen `echo` en geen `fgets()`. Denk aan methods voor het opvragen van de huidige vraag, het controleren van een antwoord en het opvragen van de score.
2. Maak een `console.php` die de `Quiz` gebruikt om de quiz in de terminal te spelen. Het resultaat is hetzelfde als de code hierboven.
3. Maak een `web.php` die dezelfde `Quiz` gebruikt voor een webpagina met één vraag. Je mag de vraag kiezen met een `?question=1` in de url en het antwoord met een formulier. Je hoeft de score niet bij te houden.

Pas de class niet aan om `web.php` te laten werken. Moet dat toch, dan zit er nog te veel terminal specifieke code in.

**Hint:** Laat de methods waarden teruggeven, bijvoorbeeld een `bool` voor een goed of fout antwoord. De code die de class aanroept bepaalt wat de gebruiker te zien krijgt.
