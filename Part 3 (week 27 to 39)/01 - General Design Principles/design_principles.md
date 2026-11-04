# Design principles

## Werkende code is niet genoeg

In Part 1 hebben we steeds hetzelfde gedaan: eerst code die werkt en daarna code die overzichtelijker is. Functies, classes en `hangman_v2` naar `hangman_v3` zijn daar voorbeelden van. Maar waarom is de ene oplossing eigenlijk beter dan de andere, terwijl ze allebei hetzelfde doen?

Code wordt veel vaker gelezen en aangepast dan geschreven. Een aanpassing over een half jaar, door jezelf of door een collega die jouw code nooit gezien heeft, moet makkelijk zijn. Design principles zijn vuistregels die daarbij helpen.

Let op: het zijn vuistregels en geen wetten. Ze kunnen elkaar zelfs tegenspreken. Je leert er een vraag mee te stellen aan je eigen code, het antwoord blijft je eigen keuze.

In deze les kijken we naar vier principes: KISS, DRY, YAGNI en Separation of Concerns. Alle vier komen terug in SOLID en GRASP.

## KISS: Keep It Simple, Stupid

Maak het zo eenvoudig als kan. Hoe meer code, hoe meer er fout kan gaan en hoe meer iemand moet lezen om het te snappen. Python heeft hier een bekende uitspraak over: *simple is better than complex*.

Kijk naar deze functie uit Galgje, die het woord toont met een `_` voor elke letter die nog niet geraden is:

```PHP
function displayWord(string $word, array $guessedLetters): string
{
    $result = [];
    $position = 0;

    while ($position < strlen($word)) {
        $letter = $word[$position];

        $isGuessed = false;
        foreach ($guessedLetters as $guessedLetter) {
            if ($guessedLetter === $letter) {
                $isGuessed = true;
            }
        }

        if ($isGuessed === true) {
            $result[$position] = $letter;
        } else {
            $result[$position] = '_';
        }

        $position++;
    }

    $output = '';
    foreach ($result as $character) {
        $output = $output . $character;
    }

    return $output;
}
```

Dit werkt, maar er gebeurt veel meer dan nodig. We zoeken zelf of een letter geraden is, we houden een tussenresultaat bij in een array en plakken het aan het eind weer aan elkaar. Vergelijk dit met de versie uit `hangman_v2`, die hier sterk op lijkt:

```PHP
function displayWord(string $word, array $guessedLetters): string
{
    $display = '';

    for ($i = 0; $i < strlen($word); $i++) {
        $display .= in_array($word[$i], $guessedLetters) ? $word[$i] : '_';
    }

    return $display;
}
```

Hetzelfde resultaat in veel minder code. Let op wat er verdwenen is: de hulpvariabele `$isGuessed`, het tussenresultaat en de tweede loop. `in_array()` doet het zoeken al voor ons.

Een goede vraag om jezelf te stellen is: *zou een collega dit in tien seconden snappen?* Is het antwoord nee, kijk dan of je iets kunt weghalen voordat je iets toevoegt.

Zie `kiss_voor.php` en `kiss_na.php`.

## DRY: Don't Repeat Yourself

Elke kennis of regel staat op één plek in de code. Kopieer je een stuk code om het net iets anders te gebruiken, dan moet je bij elke wijziging alle kopieën aanpassen. Vergeet je er één, dan heb je een bug.

Dit hebben we al gezien. In `hangman_v2` wordt `handleMistake()` aangeroepen vanuit zowel `handleLetter()` als `handleWord()`. Zonder die functie stond de code voor een foute gok op twee plekken.

Een ander voorbeeld uit het bibliotheek systeem. We willen boeken zoeken op auteur en op titel:

```PHP
function findBooksByAuthor(array $books, string $author): array
{
    $found = [];

    foreach ($books as $book) {
        if ($book['author'] === $author) {
            $found[] = $book;
        }
    }

    return $found;
}

function findBooksByTitle(array $books, string $title): array
{
    $found = [];

    foreach ($books as $book) {
        if ($book['title'] === $title) {
            $found[] = $book;
        }
    }

    return $found;
}
```

De twee functies verschillen op één plek: de key waarop we zoeken. Wil je later ook op hoofdletters letten, dan moet je dat op twee plekken aanpassen. Met één functie hoeft dat maar één keer:

```PHP
function findBooks(array $books, string $field, string $value): array
{
    return array_filter($books, fn(array $book) => $book[$field] === $value);
}

findBooks($books, 'author', 'Stephen King');
findBooks($books, 'title', 'It');
```

Zie `dry_voor.php` en `dry_na.php`.

### Wanneer gebruik je DRY niet?

Niet alle code die er hetzelfde uitziet is ook echt hetzelfde. Kijk naar deze twee functies:

```PHP
function getStudentDiscount(float $price): float
{
    return $price * 0.10;
}

function getSeniorDiscount(float $price): float
{
    return $price * 0.10;
}
```

Ze zijn nu identiek, maar het zijn twee verschillende regels. Voeg je ze samen tot één `getDiscount()` en krijgen studenten later 15%, dan krijgen senioren er onbedoeld ook 15% bij. Of je moet een uitzondering inbouwen waardoor de samengevoegde functie ingewikkelder is dan de twee losse functies.

De vraag is dus niet *ziet het er hetzelfde uit?* maar *veranderen deze twee altijd samen?* Doen ze dat, voeg ze dan samen. Doen ze dat niet, laat ze dan los van elkaar. Zie `dry_valkuil.php`.

## YAGNI: You Aren't Gonna Need It

Bouw niets voor later. "Misschien hebben we dit straks nodig" is bijna altijd een slechte reden om code te schrijven.

```PHP
class GameSettings
{
    private int $maxAttempts = 7;
    private string $language = 'nl';
    private string $theme = 'dark';
    private int $timeLimitInSeconds = 0;
    private bool $showHints = false;

    // vijf getters ...
}
```

Hier is een class gemaakt met allerlei instellingen die we misschien ooit willen hebben. In het spel wordt maar één waarde gebruikt: `maxAttempts`. De rest is code die je moet lezen, onderhouden en testen, terwijl hij niets doet.

Daarnaast is de kans groot dat je het later anders nodig hebt dan je nu verwacht. Dan moet je de code die je alvast had gebouwd alsnog aanpassen. Wat we nodig hebben is dit:

```PHP
const MAX_ATTEMPTS = 7;
```

Komt er later echt een taal of thema bij, dan voeg je het op dat moment toe. Je weet dan wat er nodig is en hoe het gebruikt wordt.

Zie `yagni_voor.php` en `yagni_na.php`.

YAGNI en KISS lijken op elkaar. KISS gaat over hoe je iets bouwt, YAGNI over of je het überhaupt bouwt.

## Separation of Concerns

Elk deel van de code heeft één verantwoordelijkheid. Code die iets anders doet, staat ergens anders. Dit is het belangrijkste principe van de vier, en we hebben het in Part 1 al meerdere keren gedaan zonder het zo te noemen.

In de eerste versie van Galgje stond alles door elkaar. Hier een klein voorbeeld daarvan:

```PHP
function playRound(): bool
{
    global $word, $guessedLetters, $attempts;

    echo "Raad een letter: ";
    $letter = trim(fgets(STDIN));
    $guessedLetters[] = $letter;

    if (strpos($word, $letter) === false) {
        $attempts--;
        echo "Fout, nog $attempts pogingen\n";
        return false;
    }

    echo "Goed!\n";
    return true;
}
```

Deze functie doet drie dingen: hij leest invoer uit de terminal, hij controleert de letter en hij toont een melding. Wat gebeurt er als dit spel straks via een webformulier moet werken? We kunnen niets hergebruiken, want de controle zit vast aan `fgets()` en `echo`.

De oplossing is hetzelfde als wat we deden van `hangman_v2` naar `hangman_v3`: de spellogica in een class zonder invoer of uitvoer, en een apart bestand voor de terminal.

```PHP
class Word
{
    // ...

    // geeft terug of de letter in het woord zit
    public function guessLetter(string $letter): bool
    {
        $this->guessedLetters[] = $letter;

        return strpos($this->word, $letter) !== false;
    }
}
```

De class geeft alleen terug of de letter goed was. Wat de speler daarvan te zien krijgt is aan degene die de class aanroept:

```PHP
// console.php
if ($word->guessLetter($letter)) {
    echo "Goed!\n";
} else {
    $attempts--;
    echo "Fout, nog $attempts pogingen\n";
}
```

En nu kunnen we dezelfde `Word` class ook gebruiken voor een webpagina, zonder één regel van de class aan te passen. Zie `soc_na/web.php`.

Je ziet hetzelfde in het bibliotheek systeem uit les 05: `Router` bepaalt welke controller wordt aangeroepen, `BookController` verwerkt het request en `Book` bevat de gegevens van een boek.

Een goede vraag om jezelf te stellen is: *wat moet ik aanpassen als de invoer niet meer uit de terminal komt maar uit een webformulier? Of de uitvoer niet meer een echo is maar een JSON antwoord?* Moet je daarvoor code aanpassen waar de spelregels staan, dan zit er te veel door elkaar.

Zie `soc_voor.php` en de map `soc_na`.

## Samengevat

| Principe | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| KISS | Zou een collega dit in tien seconden snappen? | Hulpvariabelen en tussenstappen die niets toevoegen |
| DRY | Veranderen deze twee stukken code altijd samen? | Gekopieerde code die je op meerdere plekken moet aanpassen |
| YAGNI | Gebruikt iets dit nu al? | Code, parameters of classes die nergens worden gebruikt |
| Separation of Concerns | Wat moet ik aanpassen als de invoer of uitvoer verandert? | Een functie die leest, beslist en toont |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/design_principles/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map. De voorbeelden met `voor` en `na` in de naam laten dezelfde code zien zonder en met het principe.

1. [kiss_voor.php](./example/design_principles/kiss_voor.php) en [kiss_na.php](./example/design_principles/kiss_na.php) tonen het woord in Galgje, eerst ingewikkeld en daarna eenvoudig.
2. [dry_voor.php](./example/design_principles/dry_voor.php) en [dry_na.php](./example/design_principles/dry_na.php) zoeken boeken op auteur en titel, eerst met twee bijna gelijke functies en daarna met één.
3. [dry_valkuil.php](./example/design_principles/dry_valkuil.php) laat twee regels zien die er hetzelfde uitzien maar niet samengevoegd moeten worden.
4. [yagni_voor.php](./example/design_principles/yagni_voor.php) en [yagni_na.php](./example/design_principles/yagni_na.php) vergelijken een class vol ongebruikte instellingen met één constante.
5. [soc_voor.php](./example/design_principles/soc_voor.php) is een klein galgje waarin invoer, logica en uitvoer door elkaar staan. Dit script wacht op invoer in de terminal.
6. [soc_na/](./example/design_principles/soc_na/) splitst dat in `Word.php` (logica), `console.php` (terminal) en `web.php` (webpagina). Voor de webpagina start je `php -S localhost:8090` in de map `soc_na`.

## Opgaven

De opgaven staan in [design_principles_opgaven_1.md](./design_principles_opgaven_1.md).
