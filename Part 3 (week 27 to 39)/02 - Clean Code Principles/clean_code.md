# Clean code

## Code die zichzelf uitlegt

Vorige keer ging het over de opbouw van code: wat hoort waar en wat bouw je wel of niet. Nu gaat het over het lezen ervan. Je schrijft code één keer, maar je (en je collega's) leest hem tientallen keren. Het doel van clean code is dat iemand begrijpt wat de code doet, zonder jou te hoeven vragen.

In de checklist van de huiswerkopdrachten uit Part 1 staan al een aantal regels die hierbij horen:

- Naamgeving van de variabelen is duidelijk en beschrijvend.
- Methodes doen maar 1 ding.
- Een methode heeft een zelf documenterende naam.

In deze les kijken we waarom die regels er staan. We behandelen namen, functies, comments, het principe van de minste verrassing en waarom je niet te vroeg moet optimaliseren.

## Namen

Een goede naam zegt wat iets is of doet. Kijk naar deze functie:

```PHP
function calc(int $d): float
{
    $r = 0.0;

    if ($d > 14) {
        $r = ($d - 14) * 0.25;
    }

    return $r;
}

$x = calc(20);
```

Wat is `$d`? Wat is `$r`? En wat is `$x` dat we terugkrijgen? Je moet de hele functie lezen en zelf bedenken dat het hier om de boete voor een boek gaat dat te laat is ingeleverd. Vergelijk dit met:

```PHP
const FREE_LOAN_DAYS = 14;
const LATE_FEE_PER_DAY = 0.25;

function calculateLateFee(int $daysBorrowed): float
{
    if ($daysBorrowed <= FREE_LOAN_DAYS) {
        return 0.0;
    }

    return ($daysBorrowed - FREE_LOAN_DAYS) * LATE_FEE_PER_DAY;
}

$lateFee = calculateLateFee(20);
```

Het gedrag is hetzelfde, maar je hoeft de functie niet meer te lezen om te snappen wat er gebeurt. Let ook op de getallen `14` en `0.25`: die hebben nu een naam, zodat je weet wat ze betekenen.

Een paar vuistregels:

- **Een naam zegt wat iets is of doet.** `$daysBorrowed` in plaats van `$d`, `calculateLateFee()` in plaats van `calc()` of `process()`.
- **Een boolean lees je als een vraag.** `isOverdue()`, `isGameOver()` en `hasWordBeenGuessed()` (die laatste zit al in `Word` uit `hangman_v3`). Je leest `if ($word->hasWordBeenGuessed())` bijna als gewone taal.
- **Geen ruis in de naam.** Woorden als `Data`, `Info`, `Manager` of `Array` in een naam voegen meestal niets toe. `$bookArray` is gewoon `$books` en `doStuff()` is nooit een goede naam.
- **Gebruik hetzelfde woord voor hetzelfde idee.** Gebruik je op de ene plek `get`, op de andere `fetch` en op een derde `retrieve` voor hetzelfde soort handeling, dan gaat de lezer zich afvragen of die iets verschillends doen.

Hoe je een naam schrijft (camelCase, hoofdletters) staat in [naming.md](../../Part%201%20(week%201%20to%2013)/01%20-%20Intro/naming.md). Hier gaat het erom wat de naam betekent.

Zie `names_voor.php` en `names_na.php`.

## Functies

### Eén ding, op één niveau

In les 01 hebben we gezien dat een stuk code één verantwoordelijkheid hoort te hebben. Voor een functie geldt hetzelfde: doet de functie meer dan één ding, dan is dat te zien aan de naam. Heb je `and` of `or` nodig om de functie te beschrijven (`validateAndSaveBook()`), dan zijn het waarschijnlijk twee functies.

### Weinig parameters

Hoe meer parameters, hoe moeilijker het is om te onthouden wat de volgorde is en wat elke waarde betekent. Bij meer dan drie parameters is het vaak beter om de waarden te groeperen. In het bibliotheek systeem is dat al gedaan: in `BookController::store()` maken we een `Book` aan met acht waarden, maar daarna geven we alleen nog dat ene `Book` object door.

### Early returns

Kijk naar deze functie:

```PHP
function borrowBook(array $book, int $age, bool $isMember): string
{
    if ($book['available']) {
        if ($isMember) {
            if ($age >= $book['ageRating']) {
                return 'Uitgeleend';
            } else {
                return 'Te jong voor dit boek';
            }
        } else {
            return 'Alleen voor leden';
        }
    } else {
        return 'Niet beschikbaar';
    }
}
```

Elke `if` zit in de vorige. Om te weten wanneer een boek wordt uitgeleend moet je alle voorwaarden tegelijk onthouden. Met early returns handel je eerst elke uitzondering af en houd je het gewone pad over:

```PHP
function borrowBook(array $book, int $age, bool $isMember): string
{
    if (!$book['available']) {
        return 'Niet beschikbaar';
    }

    if (!$isMember) {
        return 'Alleen voor leden';
    }

    if ($age < $book['ageRating']) {
        return 'Te jong voor dit boek';
    }

    return 'Uitgeleend';
}
```

Alles staat nu op hetzelfde niveau en je leest de functie van boven naar beneden.

### Geen bool die het gedrag omdraait

```PHP
showBook($book, true);
```

Wat betekent `true`? Dat moet je in de functie opzoeken. Zo'n `bool` doet eigenlijk twee dingen in één functie. Maak er liever twee functies met een duidelijke naam van:

```PHP
showBookTitle($book);
showBookDetails($book);
```

Zie `functions_voor.php` en `functions_na.php`.

## Comments

Een comment legt uit *waarom* iets zo is, niet *wat* de code doet. Wat de code doet kun je lezen, waarom iemand het zo heeft gekozen niet.

```PHP
// functie om de korting te berekenen
function calculateDiscount(float $price, bool $isMember): float
{
    // maak variabele discount aan en zet op 0
    $discount = 0.0;

    // als de klant lid is
    if ($isMember) {
        // 10 procent van de prijs
        $discount = $price * 0.10;
    }

    // geef discount terug
    return $discount;
}
```

Elke comment zegt precies wat de regel eronder al zegt. Dat levert niets op en het kost wel onderhoud: verander je de code en vergeet je de comment, dan liegt de comment.

Een goede naam vervangt vaak een comment:

```PHP
// controleer of het woord geraden is
if ($displayWord === $word) { ... }

// wordt:
if (hasWordBeenGuessed($displayWord, $word)) { ... }
```

Een comment is wel handig wanneer de code niet kan uitleggen waarom iets zo is:

```PHP
// leden krijgen 10% korting, dit percentage is vastgelegd in het ledencontract
// we rekenen niet met een afgeronde prijs omdat de afronding pas op de factuur gebeurt
```

In `GameService` staat zo'n comment: `// decrease attempts if the guess was false`. Zonder de comment is het gedrag ook duidelijk, maar de comment vertelt wel waarom we op die plek de pogingen verlagen.

Goede momenten voor een comment zijn dus: waarom je iets zo doet, een waarschuwing (bijvoorbeeld dat een volgorde belangrijk is) of een `TODO`. Een verouderde comment is erger dan geen comment.

### En de checklist uit Part 1?

In de checklist van Part 1 staat: *Elk code block wordt voorgegaan door een regel commentaar* en *een methode heeft een phpdoc commentaar*. Dat is een andere regel dan wat we hier zeggen, dus even expliciet:

- In Part 1 oefenen we met veel comments. Zo went je eraan om na te denken over wat een stuk code doet, en leer je de syntax van `phpdoc`. Je leert je code daarmee ook te structureren: kun je niet in één regel zeggen wat een blok doet, dan doet het waarschijnlijk te veel.
- In de praktijk schrijf je minder comments. De comments die overblijven leggen het *waarom* uit, en de rest wordt opgevangen door goede namen.

Je hoeft de regels uit Part 1 dus niet los te laten, maar je weet nu waarom ze er zijn en waar ze later minder streng worden.

Ook `phpdoc` heeft een plek: schrijf `@param` en `@return` als het type of de betekenis niet uit de naam blijkt, of als je een array beschrijft (`array<int, Book>`). `Gets the word to be guessed` boven `getWord()` zegt niets wat de naam niet al zegt.

Zie `comments_voor.php` en `comments_na.php`.

## Least Astonishment

Een functie doet wat de naam belooft, niet meer en niet minder. De lezer wordt niet verrast.

```PHP
// de naam belooft een getal, maar de functie leegt ook de winkelmand
function getTotal(array &$cart): float
{
    $total = 0.0;

    foreach ($cart as $item) {
        $total += $item['price'];
    }

    $cart = [];

    return $total;
}

echo "Totaal: " . getTotal($cart) . "\n";
echo "Aantal items in de winkelmand: " . count($cart) . "\n"; // 0, verrassing!
```

Niemand verwacht dat `getTotal()` iets aanpast. De bijwerking zit verstopt in de functie, dus de bug zit bij de aanroeper die niets fout heeft gedaan. Hetzelfde geldt voor `calculateAverage()` die ook iets op het scherm zet: een functie die `calculate` heet hoort te rekenen en niet te `echo`'en.

De oplossing is steeds hetzelfde: laat een functie één ding doen en geef dat een naam die erbij past. Het legen van de winkelmand wordt `clearCart()`, het tonen van het gemiddelde doet degene die de functie aanroept.

Vraag jezelf: *wat verwacht de lezer die alleen de naam leest?*

### Discussie: `guessLetter()`

Kijk eens naar deze method uit `Word` in `hangman_v3`:

```PHP
public function guessLetter(string $letter): bool
{
    if (!in_array($letter, $this->guessedLetters)) {
        $this->guessedLetters[] = $letter;
    }
    return str_contains($this->word, $letter);
}
```

De naam `guessLetter` zegt dat je een letter raadt. Wat je terugkrijgt is of de letter in het woord zit. Maar de method doet ook iets wat niet in de naam staat: hij onthoudt de letter. Roep je `guessLetter('a')` twee keer aan, dan krijg je twee keer hetzelfde antwoord, maar de toestand van het object is wel veranderd.

Is dit een verrassing? Dat is aan jou om te beoordelen:

- Een voordeel is dat je maar één method nodig hebt. Raden en onthouden horen bij elkaar.
- Een nadeel is dat een method die `bool` teruggeeft klinkt als een vraag, terwijl hij ook iets aanpast. Als je alleen wilt weten of de letter in het woord zit, zonder hem als geraden te onthouden, kan dat niet.

Daar is geen goed of fout antwoord op. Wel is het goed om te zien dat het een keuze is, en dat een naam als `guessLetter` iets belooft. Hoe zou je de method anders kunnen opsplitsen? Wat wordt de naam van de twee methods dan?

Zie `astonishment_voor.php` en `astonishment_na.php`.

## Premature optimization

Maak je code eerst begrijpelijk en juist. Pas daarna maak je hem sneller, en alleen als je hebt gemeten dat het nodig is. Optimaliseren voordat je weet waar het probleem zit, kost vaak leesbaarheid en levert weinig op.

Een voorbeeld: het tellen van even getallen kan met `$i % 2 === 0`, maar ook met de bitwise operator `($i & 1) === 0`. De tweede is korter en voelt "slimmer", dus waarschijnlijk sneller. Als we het meten (zie `optimization.php`) is de tweede inderdaad sneller. Maar bij vijf miljoen keer gaat het om een fractie van een seconde. Een enkele database query kost meer tijd dan dat verschil.

Wat het wel kost: wie `& 1` leest moet nadenken wat dat doet. De tijd die jij bespaart in de uitvoering betaal je terug in de tijd die elke lezer nodig heeft om het te begrijpen.

Je ziet dat dit bij KISS uit les 01 past: de eenvoudigste oplossing is de beste totdat je weet dat het niet genoeg is. Heb je gemeten dat iets te traag is en weet je welk stuk het probleem is, dan mag je de code ingewikkelder maken. Zet daar dan een comment bij met waarom.

*Let op: iets uit een loop halen dat bij elke ronde hetzelfde blijft (zoals in de checklist van Part 1) is geen premature optimization. Dat is gewoon geen onnodig werk doen.*

## Samengevat

| Onderwerp | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Namen | Kan ik aan de naam zien wat dit is of doet? | `$d`, `$tmp`, `process()`, `doStuff()` |
| Functies | Doet deze functie één ding? | `and` in de naam, meer dan drie parameters, vier niveaus ifs, een `bool` die het gedrag omdraait |
| Comments | Zegt de comment waarom, of alleen wat de code al zegt? | Een comment die de regel eronder herhaalt |
| Least Astonishment | Wat verwacht de lezer die alleen de naam leest? | Een `get` of `calculate` die iets aanpast of toont |
| Premature optimization | Heb ik gemeten dat dit te traag is? | Een truc die het moeilijker leesbaar maakt zonder dat het nodig is |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/clean_code/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [names_voor.php](./example/clean_code/names_voor.php) en [names_na.php](./example/clean_code/names_na.php) berekenen de boete van een te laat ingeleverd boek, eerst met korte namen en daarna met duidelijke namen.
2. [functions_voor.php](./example/clean_code/functions_voor.php) en [functions_na.php](./example/clean_code/functions_na.php) laten zien hoe geneste ifs en een `bool` parameter worden omgezet naar early returns en twee functies.
3. [comments_voor.php](./example/clean_code/comments_voor.php) en [comments_na.php](./example/clean_code/comments_na.php) vergelijken comments die alleen herhalen wat de code doet met namen die dat al zeggen en één comment die het waarom uitlegt.
4. [astonishment_voor.php](./example/clean_code/astonishment_voor.php) en [astonishment_na.php](./example/clean_code/astonishment_na.php) tonen functies die iets anders doen dan de naam zegt, en de versie waarin elke functie doet wat de naam belooft.
5. [optimization.php](./example/clean_code/optimization.php) meet het verschil tussen `% 2` en `& 1`. De tijden verschillen per computer.

## Opgaven

De opgaven staan in [clean_code_opgaven_1.md](./clean_code_opgaven_1.md).
