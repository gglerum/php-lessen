# SOLID deel 1: Single Responsibility, Open/Closed en Liskov

## Van Separation of Concerns naar SOLID

In les 01 hebben we gezien dat elk deel van de code één verantwoordelijkheid hoort te hebben (Separation of Concerns). SOLID werkt dat idee verder uit voor classes. SOLID is een afkorting van vijf principes:

- **S**ingle Responsibility
- **O**pen/Closed
- **L**iskov Substitution
- **I**nterface Segregation
- **D**ependency Inversion

In dit deel behandelen we de eerste drie, in [solid_2.md](./solid_2.md) de laatste twee. Net als de principes uit les 01 zijn het vuistregels en geen wetten. We gebruiken voorbeelden uit het bibliotheek systeem en Galgje uit Part 1.

## S: Single Responsibility

Een class heeft één reden om te veranderen.

Kijk naar deze class, die een boek verwerkt dat een gebruiker invoert:

```PHP
class BookHandler
{
    private array $books = [];

    public function handle(array $input): void
    {
        // valideren
        if ($input['title'] === '') {
            echo "De titel ontbreekt\n";
            return;
        }

        if ((int)$input['pages'] < 1) {
            echo "Een boek heeft minimaal 1 pagina\n";
            return;
        }

        // het boek opbouwen
        $book = ['title' => $input['title'], 'pages' => (int)$input['pages']];

        // opslaan
        $this->books[] = $book;

        // tonen
        echo "Boek opgeslagen: " . $book['title'] . ' (' . $book['pages'] . " pagina's)\n";
    }
}
```

Het werkt, maar deze class heeft vier redenen om te veranderen: als de validatieregels veranderen, als de opbouw van een boek verandert, als we niet meer in een array maar in een database opslaan en als we niet meer met `echo` maar met HTML tonen. Elke wijziging raakt dezelfde class, en elke wijziging kan de andere drie stukken per ongeluk stukmaken.

Dit zag je ook al in `BookController::store()` uit les 05: die leest `$_POST`, bouwt een `Book`, bewaart het in de session en stuurt door naar een andere pagina.

Een handige vraag om jezelf te stellen is: *wie moet ik bellen als X verandert?* Als dat bij elke wijziging dezelfde persoon of dezelfde class is, zit er te veel in.

De oplossing is elk onderdeel in een eigen class te zetten:

```PHP
class BookValidator
{
    // geeft een foutmelding terug, of null als alles klopt
    public function validate(array $input): ?string { ... }
}

class BookRepository
{
    public function add(array $book): void { ... }
}

class BookConsolePrinter
{
    public function printSaved(array $book): void { ... }
}
```

De `BookHandler` verbindt de onderdelen alleen nog en bepaalt de volgorde:

```PHP
public function handle(array $input): void
{
    $error = $this->validator->validate($input);

    if ($error !== null) {
        echo $error . "\n";
        return;
    }

    $book = ['title' => $input['title'], 'pages' => (int)$input['pages']];

    $this->repository->add($book);
    $this->printer->printSaved($book);
}
```

Het resultaat is hetzelfde, maar elke class kan nu apart veranderd worden. Een database in plaats van een array? Alleen `BookRepository` verandert.

Let op: dat betekent niet dat elke class maar één method mag hebben. Het gaat om één *reden om te veranderen*, niet om één stuk code.

Zie `srp_voor.php` en `srp_na.php`.

## O: Open/Closed

Een class of functie is open voor uitbreiding, maar gesloten voor wijziging. Je kunt gedrag toevoegen zonder de bestaande code aan te passen.

Stel dat we de boete voor te laat ingeleverde items berekenen:

```PHP
function calculateLateFee(object $item, int $daysLate): float
{
    if ($item instanceof Book) {
        return $daysLate * 0.25;
    } elseif ($item instanceof Magazine) {
        return $daysLate * 0.10;
    } elseif ($item instanceof Boardgame) {
        return $daysLate * 0.50;
    }

    throw new InvalidArgumentException('Onbekend type item');
}
```

Elk nieuw type item betekent dat we deze functie moeten aanpassen. Vergeten we dat, dan gaat het pas tijdens het draaien mis. En zo'n `if` is meestal niet de enige: de volgende functie heeft dezelfde keuze voor `getLoanDays()` en de derde voor de manier van tonen.

Je kent de oplossing al uit les 08: een abstracte class en polymorphism. Elk type vertelt zelf wat de boete per dag is:

```PHP
abstract class Item
{
    abstract public function getLateFeePerDay(): float;
}

class Book extends Item
{
    public function getLateFeePerDay(): float
    {
        return 0.25;
    }
}

function calculateLateFee(Item $item, int $daysLate): float
{
    return $daysLate * $item->getLateFeePerDay();
}
```

Een nieuw type item toevoegen betekent nu een nieuwe class schrijven. `calculateLateFee()` is gesloten voor wijziging, maar het systeem is wel open voor uitbreiding.

Een rode vlag is een lange `if` of `switch` die kijkt naar het *type* van iets (`instanceof`, een `type` property of een string als `'book'`).

Zie `ocp_voor.php` en `ocp_na.php`.

## L: Liskov Substitution

Een child moet overal gebruikt kunnen worden waar de parent verwacht wordt, zonder dat er iets onverwachts gebeurt. Dit is polymorphism uit les 08, met één belangrijke voorwaarde: de child mag de afspraak van de parent niet breken.

```PHP
class Book
{
    // ...

    public function borrow(): string
    {
        return $this->title . ' is uitgeleend';
    }
}

// een naslagwerk is ook een boek, dus we laten het overerven van Book
class ReferenceBook extends Book
{
    // maar een naslagwerk mag niet mee naar huis
    public function borrow(): string
    {
        throw new LogicException($this->title . ' mag niet worden uitgeleend');
    }
}
```

Een naslagwerk *is* een boek, dus overerven voelt logisch. Maar de afspraak van `Book` is dat je het kunt uitlenen. Een functie die een lijst boeken uitleent werkt voor elk `Book`, behalve voor de `ReferenceBook`: die gooit een exception halverwege de lijst.

```PHP
borrowAll([new Book('Misery'), new ReferenceBook('Woordenboek'), new Book('It')]);
// Misery is uitgeleend
// Fout: Woordenboek mag niet worden uitgeleend
```

Het boek `It` is nooit uitgeleend, en degene die `borrowAll()` heeft geschreven heeft niets fout gedaan.

Het probleem is niet dat de code niet werkt, maar dat de child zich niet gedraagt als zijn parent. De oplossing is niet te erven van `Book` wanneer het gedrag niet past. Wat een boek en een naslagwerk gemeen hebben staat in `Item`, en alleen een `Book` heeft een `borrow()`:

```PHP
abstract class Item { /* title, getTitle() */ }

class Book extends Item
{
    public function borrow(): string { ... }
}

class ReferenceBook extends Item { }

function borrowBook(Book $book): void
{
    echo $book->borrow() . "\n";
}
```

Probeer je nu een `ReferenceBook` uit te lenen, dan krijg je meteen een `TypeError` in plaats van een verrassing halverwege het uitlenen.

Rode vlaggen zijn een child die een method overschrijft met een exception (`throw new Exception('Niet ondersteund')`), een method die niets doet terwijl de parent wel iets doet, of een `instanceof` in code die eigenlijk om de parent vraagt.

Zie `lsp_voor.php` en `lsp_na.php`.

## Samengevat

| Principe | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Single Responsibility | Wie moet ik bellen als X verandert? | Een class met meerdere redenen om te veranderen (valideren, opslaan en tonen) |
| Open/Closed | Moet ik bestaande code aanpassen om iets toe te voegen? | Een lange `if` of `switch` op het type van iets |
| Liskov Substitution | Kan een child overal waar de parent verwacht wordt? | Een child die een exception gooit of niets doet in een geërfde method |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/solid/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [srp_voor.php](./example/solid/srp_voor.php) en [srp_na.php](./example/solid/srp_na.php) verwerken een boek, eerst in één class en daarna verdeeld over een validator, een repository, een printer en een handler.
2. [ocp_voor.php](./example/solid/ocp_voor.php) en [ocp_na.php](./example/solid/ocp_na.php) berekenen de boete per type item, eerst met `instanceof` en daarna met een abstracte class.
3. [lsp_voor.php](./example/solid/lsp_voor.php) en [lsp_na.php](./example/solid/lsp_na.php) laten een `ReferenceBook` zien die de afspraak van `Book` breekt, en de versie waarin dat niet kan.

## Opgaven

De opgaven staan in [solid_opgaven_1.md](./solid_opgaven_1.md). In [solid_2.md](./solid_2.md) gaan we verder met Interface Segregation en Dependency Inversion.
