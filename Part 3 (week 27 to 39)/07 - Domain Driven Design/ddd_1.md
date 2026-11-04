# Domain Driven Design deel 1: taal, entities en value objects

## Code rondom het probleem

In de vorige lessen hebben we steeds gekeken naar hoe code is opgebouwd: wat is een goede naam, welke class krijgt welke taak, welk pattern past hier. Domain Driven Design (DDD) kijkt vanuit een andere kant: het probleem dat de applicatie oplost.

Dat probleem noemen we het *domein*. Voor een bibliotheek systeem is het domein: leden, boeken, uitleningen, boetes. Het idee van DDD is dat je code is opgezet rondom dat domein en dat de woorden uit het domein ook in de code staan, in plaats van technische woorden als `Manager`, `Handler` of `Data`.

Waarom is dat nuttig? De mensen die de applicatie gebruiken (de bibliothecaris) en de mensen die hem bouwen (jij) hebben anders vaak andere woorden voor dezelfde dingen. Dat leidt tot misverstanden, en een misverstand in de code is een bug.

DDD is groot. In deze les en in [ddd_2.md](./ddd_2.md) kijken we naar de belangrijkste onderdelen. Bijna alles ken je al, onder een andere naam:

| DDD begrip | Al behandeld |
|---|---|
| Repository | `BookRepository` (les 04, Pure Fabrication) |
| Value Object | `FileSize` in het `better` voorbeeld (les 05) |
| Aggregate | `Library::lend()` bij Creator en Controller (les 04) |
| Domain Event | Observer (les 06) |
| Domain Service | `AnswerService` in `better` (les 05) en de facade uit les 06 |
| Entity | `Book`, `Member` en `Loan` uit de voorbeelden |

Nieuw is vooral de taal, de scheiding tussen domein en techniek, en hoe je de onderdelen samen gebruikt.

Een applicatie met DDD heeft grofweg drie lagen:

- **Domein**: de regels en de begrippen uit het probleem (`Member`, `Book`, `Library`).
- **Applicatie**: de dingen die de gebruiker kan doen (een boek uitlenen), door het domein aan te roepen.
- **Infrastructuur**: de techniek eromheen (database, e-mail, bestanden).

Het domein weet niets van de andere twee.

## Ubiquitous language

Ubiquitous language betekent: één woordenlijst, die ontwikkelaars en gebruikers allebei gebruiken, en die ook in de code staat.

Kijk naar deze code. De bibliothecaris zegt "een lid leent een boek", maar in de code staat dit:

```PHP
class Item { ... }

class User
{
    public function takeItem(Item $item): void { ... }

    public function countRecords(): int { ... }
}

$user->takeItem(new Item('Misery'));
```

Wie de code leest moet elk woord vertalen: `User` is een lid, `Item` is een boek, `takeItem` is lenen en een `record` is een uitlening. Elke vertaalstap is een kans op een misverstand. Met de woorden van de bibliothecaris:

```PHP
class Book { ... }

class Member
{
    public function borrow(Book $book): void { ... }

    public function countBorrowedBooks(): int { ... }
}

$member->borrow(new Book('Misery'));
```

Nu kan de bibliothecaris, of een nieuwe collega, de code lezen zonder te vertalen.

Dit lijkt op wat we bij clean code (les 02) over namen hebben gezien, maar het gaat een stap verder: de woorden komen niet uit jouw hoofd, ze komen uit het domein. Je hebt ook een voorbeeld gezien in het `bad` voorbeeld, waar een ingevulde vragenlijst in de code een "application" heet. Niemand uit het domein noemt het zo.

Het gaat niet alleen om vertalen. Door na te denken over de juiste woorden kom je erachter wat het domein eigenlijk is. Als niemand kan uitleggen wat een `record` is, is dat een teken dat het begrip niet goed is begrepen.

Zie `language_voor.php` en `language_na.php`.

## Bounded context

Een woord betekent niet overal hetzelfde. Een `Book` voor de uitleenbalie is iets met een titel en de vraag of hij beschikbaar is. Een `Book` voor de inkoopafdeling is iets met een leverancier en een prijs. Willen we één class `Book` die alles kan, dan wordt het een grote class waar elke afdeling een eigen stuk van gebruikt.

Een *bounded context* is de grens waarbinnen een woord één betekenis heeft. Binnen de uitleenbalie is `Book` één ding, binnen de inkoop iets anders. Het mogen dus twee classes met dezelfde naam zijn, in twee onderdelen van de applicatie.

Voor kleine applicaties hoef je dit niet uit te werken. Het is wel goed om te weten dat het bestaat: wordt een class steeds groter omdat iedereen er iets bij wil, dan is dit vaak de oorzaak. Het is ook een idee dat je bij grote applicaties terugziet in aparte modules of services.

## Entities

Een entity is een object met een identiteit die in de tijd gelijk blijft, ook als de rest verandert.

Kijk wat er gebeurt als een `Member` geen identiteit heeft:

```PHP
class Member
{
    private string $name;
    private string $city;
    // ...
}

$annaFromUtrecht = new Member('Anna', 'Utrecht');
$otherAnna = new Member('Anna', 'Utrecht');

echo $annaFromUtrecht == $otherAnna ? 'Gelijk' : 'Verschillend';
```

Dat geeft `Gelijk`: PHP vergelijkt de waarden van de properties. Maar het zijn twee verschillende mensen met toevallig dezelfde naam en woonplaats. En andersom: verhuist Anna, dan is ze niet meer gelijk aan wat ze eerst was, terwijl ze dezelfde persoon blijft.

Een entity krijgt daarom een `id`, en twee leden zijn dezelfde persoon als hun `id` gelijk is:

```PHP
class Member
{
    private int $id;
    // ...

    public function equals(Member $other): bool
    {
        return $this->id === $other->id;
    }
}
```

Nu zijn twee Anna's met een verschillend `id` twee personen, en Anna blijft Anna als ze verhuist.

Een entity bevat ook gedrag: niet alleen de gegevens, maar ook de regels die erbij horen. Dat is Information Expert uit les 04 en Tell, Don't Ask uit les 05. In de `Member` van les 05 zijn dat `canBorrow()` en `borrow()`.

Zie `entity_voor.php` en `entity_na.php`.

## Value objects

Een value object heeft geen identiteit. Het wordt bepaald door zijn waarden, en het verandert niet. Twee value objects met dezelfde waarden zijn hetzelfde.

Een voorbeeld is een ISBN. Als string moet iedereen die het gebruikt zelf controleren of het klopt:

```PHP
function registerBook(string $title, string $isbn): string
{
    // de controle staat hier ...
    if (strlen(str_replace('-', '', $isbn)) !== 13) {
        return 'Ongeldig ISBN';
    }

    // ...
}

function printLabel(string $isbn): string
{
    // ... en hier zou dezelfde controle moeten staan, maar die is vergeten
    return "ISBN: $isbn";
}

printLabel('Misery');  // een titel in plaats van een ISBN, en niemand merkt het
```

Een string is een string, dus PHP kan niet zien dat dit geen ISBN is. Met een value object gebeurt de controle één keer, bij het aanmaken:

```PHP
class Isbn
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $digits = str_replace('-', '', $value);

        if (!$this->hasValidChecksum($digits)) {
            throw new InvalidArgumentException("Ongeldig ISBN: $value");
        }

        $this->value = $digits;
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }
}

function registerBook(string $title, Isbn $isbn): string { ... }
```

- Een ongeldig ISBN kan niet bestaan: de constructor gooit een exception.
- De controle staat op één plek (DRY).
- Een titel meegeven als ISBN geeft een `TypeError`.
- `readonly` zorgt dat de waarde niet verandert.
- Twee ISBN's met dezelfde waarde zijn gelijk, ook als het twee objecten zijn.

Dit ken je al: `FileSize` in het `better` voorbeeld van les 05 is een value object. Het zorgt dat je niet per ongeluk bytes en kilobytes door elkaar haalt, en het bevat de berekeningen (`add()`, `exceedsLimit()`) die bij een bestandsgrootte horen.

Zie `value_object_voor.php` en `value_object_na.php`.

## Entity of value object?

Een korte vuistregel: *als twee exemplaren met dezelfde gegevens uitwisselbaar zijn, is het een value object. Zo niet, een entity.*

| Voorbeeld | Entity of value object? | Waarom |
|---|---|---|
| Een lid | Entity | Twee leden met dezelfde naam zijn niet dezelfde persoon |
| Een boek (een fysiek exemplaar) | Entity | Twee exemplaren van dezelfde titel zijn niet uitwisselbaar: de één kan uitgeleend zijn |
| Een ISBN | Value object | Twee gelijke ISBN's zijn hetzelfde ISBN |
| Een bedrag (`12,50 euro`) | Value object | Een briefje van 10 euro is uitwisselbaar met een ander briefje van 10 euro |
| Een adres | Meestal value object | Als je het adres wijzigt, vervang je het hele adres |

Het hangt af van het domein. Voor een bank is elk briefje met een serienummer een entity, voor een winkel is het een value object.

## Samengevat

| Begrip | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Ubiquitous language | Gebruikt de code dezelfde woorden als de gebruiker? | `Item`, `Record`, `Data`, `Handler` voor iets met een eigen naam in het domein |
| Bounded context | Betekent dit woord overal hetzelfde? | Een class waar elke afdeling een eigen stuk van gebruikt |
| Entity | Is dit object dezelfde, ook als zijn gegevens veranderen? | `==` om twee objecten te vergelijken terwijl ze een identiteit hebben |
| Value object | Zijn twee gelijke exemplaren uitwisselbaar? | Een `string` of `int` die steeds opnieuw wordt gecontroleerd |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/ddd/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [language_voor.php](./example/ddd/language_voor.php) en [language_na.php](./example/ddd/language_na.php) laten een lid een boek lenen, eerst met `User` en `Item` en daarna met de woorden uit het domein.
2. [entity_voor.php](./example/ddd/entity_voor.php) en [entity_na.php](./example/ddd/entity_na.php) vergelijken leden, eerst op hun gegevens en daarna op hun `id`.
3. [value_object_voor.php](./example/ddd/value_object_voor.php) en [value_object_na.php](./example/ddd/value_object_na.php) werken met een ISBN, eerst als string en daarna als value object.

## Opgaven

De opgaven staan in [ddd_opgaven_1.md](./ddd_opgaven_1.md). In [ddd_2.md](./ddd_2.md) gaan we verder met aggregates, repositories, domain services en domain events.
