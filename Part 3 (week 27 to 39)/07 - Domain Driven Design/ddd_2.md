# Domain Driven Design deel 2: aggregates, repositories, services en events

In [ddd_1.md](./ddd_1.md) hebben we de taal, entities en value objects behandeld. Nu kijken we naar hoe je ze samen laat werken. We gebruiken weer het bibliotheek systeem.

## Aggregates

Een aggregate is een groep objecten die samen één geheel vormen. Eén van die objecten is de *root*: het enige toegangspunt van buitenaf, dat de regels bewaakt.

Kijk wat er gebeurt als de lijst met uitleningen publiek is:

```PHP
class Library
{
    public array $loans = [];
}

$library->loans[] = new Loan('Misery', 'Anna');
$library->loans[] = new Loan('It', 'Anna');
$library->loans[] = new Loan('Carrie', 'Anna');
```

De regel "maximaal twee boeken per lid" moet elke aanroeper zelf onthouden. Niets voorkomt dat Anna er drie leent. Met een aggregate bewaakt de root de regel:

```PHP
class Library
{
    private const MAX_LOANS_PER_MEMBER = 2;

    private array $loans = [];

    public function lend(string $title, string $member): void
    {
        if ($this->countLoansOf($member) >= self::MAX_LOANS_PER_MEMBER) {
            throw new DomainException("$member mag niet meer dan " . self::MAX_LOANS_PER_MEMBER . ' boeken lenen');
        }

        $this->loans[] = new Loan($title, $member);
    }
}
```

`Library` is de root. De lijst is `private`, en een `Loan` wordt alleen binnen `Library` aangemaakt. De regel kan niet meer worden omzeild. Dit is Creator en Controller uit les 04: de bedrijfsregel zit in het domein en niet in de controller.

Een paar vuistregels:

- Van buitenaf praat je alleen met de root, nooit rechtstreeks met een `Loan` die erin zit.
- Een aggregate wordt in zijn geheel opgeslagen en opgehaald.
- Verwijs naar een andere aggregate met een id, niet met het hele object.
- Maak aggregates klein. Een aggregate met te veel in zich wordt traag en moeilijk te begrijpen.

Zie `aggregate_voor.php` en `aggregate_na.php`.

## Repositories

Een repository haalt een hele aggregate op en bewaart hem. Het domein hoeft daardoor niets te weten van de database of van een andere manier van opslaan.

Dit ken je uit les 04 (Pure Fabrication) en SOLID deel 2 (Dependency Inversion). Zonder repository zit de opslag in het domein zelf:

```PHP
class Library
{
    public function save(): void { ... }

    public static function load(): Library { ... }
}
```

De `Library` is nu vast aan één manier van opslaan. In DDD staat de *afspraak* in het domein, en de *uitwerking* bij de infrastructuur:

```PHP
interface LibraryRepository
{
    public function get(): Library;

    public function save(Library $library): void;
}

class InMemoryLibraryRepository implements LibraryRepository { ... }
```

De interface hoort bij het domein: het domein bepaalt wat het nodig heeft. De uitwerking (een array, een database, een bestand) staat daarbuiten. De applicatie praat alleen met de interface:

```PHP
class LendBook
{
    public function execute(string $title, string $member): void
    {
        $library = $this->repository->get();
        $library->lend($title, $member);
        $this->repository->save($library);
    }
}
```

`LendBook` is de laag "applicatie": een taak die de gebruiker kan doen, door het domein aan te roepen. Een database is een andere class die `LibraryRepository` implementeert; `Library` en `LendBook` veranderen niet.

Zie `repository_voor.php` en `repository_na.php`.

## Domain services

Soms past een regel niet bij één entity. Een boek doorgeven van het ene lid aan het andere raakt twee leden tegelijk, dus waar hoort het?

Zonder service staat het in de controller, en die kent de interne opbouw van `Member`:

```PHP
if (!in_array($book, $from->books)) { ... }

$from->books = array_values(array_diff($from->books, [$book]));
$to->books[] = $book;
```

De regel zit nu in de controller. Met een domain service krijgt hij een eigen plek, met een naam uit de domeintaal:

```PHP
class BookTransfer
{
    public function transfer(string $book, Member $from, Member $to): void
    {
        if (!$from->hasBook($book)) {
            throw new DomainException($from->getName() . " heeft $book niet");
        }

        $from->give($book);
        $to->receive($book);
    }
}
```

`Member` beheert nu zelf zijn boeken (`hasBook()`, `give()`, `receive()`) en `BookTransfer` bepaalt de regel voor het doorgeven.

Pas op voor het verschil met een "dikke service" waar alle logica in terechtkomt. Een domain service doet één ding, met een naam uit het domein (`BookTransfer`), en alleen als de regel echt niet bij één entity past. In de meeste gevallen hoort logica gewoon in de entity zelf.

Zie `domain_service_voor.php` en `domain_service_na.php`.

## Domain events

Een domain event is een feit dat in het domein is gebeurd. Daarom staat de naam altijd in de verleden tijd: `BookLent`, niet `LendBook`.

Zonder event stuurt het domein zelf een e-mail:

```PHP
class Library
{
    public function lend(string $title, string $member): void
    {
        echo "$title uitgeleend aan $member\n";
        $this->mailer->send($member, "$title is uitgeleend");
    }
}
```

`Library` weet nu van e-mail, en elke nieuwe reactie (een statistiek, een bericht aan de balie) betekent `Library` aanpassen. Met een event meldt het domein alleen wat er is gebeurd:

```PHP
class BookLent
{
    public readonly string $title;
    public readonly string $member;
    // ...
}

class Library
{
    public function lend(string $title, string $member): void
    {
        $this->events[] = new BookLent($title, $member);
    }

    public function pullEvents(): array { ... }
}
```

De applicatie bepaalt wie op het event reageert. Dit is het Observer pattern uit les 06, met als verschil dat het domein de events verzamelt en de applicatie ze afhandelt. Een event is een value object: het is een feit dat niet meer verandert, dus `readonly`.

Zie `domain_event_voor.php` en `domain_event_na.php`.

## Het anemic domain model

Het meest voorkomende probleem bij DDD is een *anemic* model: entities met alleen getters en setters, en alle logica in services.

```PHP
class Book
{
    public function isAvailable(): bool { ... }

    public function setAvailable(bool $available): void { ... }
}

class LendingService
{
    public function lend(Book $book, string $member): string
    {
        if (!$book->isAvailable()) {
            return $book->getTitle() . ' is niet beschikbaar';
        }

        $book->setAvailable(false);

        return $book->getTitle() . " is uitgeleend aan $member";
    }
}
```

Dit werkt, maar de regel zit in de service en niet in `Book`. Niets houdt andere code tegen om `setAvailable(true)` aan te roepen en het boek zo opnieuw uit te lenen. Dit is Tell, Don't Ask uit les 05: de service vraagt de toestand op en beslist zelf.

Een rijk model geeft de regel aan de entity:

```PHP
class Book
{
    private ?string $borrowedBy = null;

    public function lendTo(string $member): void
    {
        if (!$this->isAvailable()) {
            throw new DomainException($this->title . ' is niet beschikbaar');
        }

        $this->borrowedBy = $member;
    }

    public function giveBack(): void
    {
        $this->borrowedBy = null;
    }
}
```

Er is geen `setAvailable()` meer. Een boek komt alleen weer beschikbaar via `giveBack()`. Dit is dezelfde refactoring als in les 05: de beslissing verhuist naar het object dat de gegevens heeft.

Zie `anemic_voor.php` en `anemic_na.php`.

## Wanneer gebruik je DDD niet?

DDD is bedoeld voor een complex domein met veel regels, waar meerdere mensen aan werken. Voor een eenvoudige applicatie die vooral gegevens opslaat en toont (CRUD) is het overdreven: je krijgt meer classes en lagen zonder dat er regels zijn om te beschermen.

- Een prototype of een klein hulpmiddel: één class per tabel is genoeg.
- Weinig bedrijfsregels: een aggregate die alleen gegevens vasthoudt voegt niets toe.
- Een team dat het domein niet kent: bij DDD moet je het domein wel begrijpen en erover praten.

Dit is YAGNI uit les 01. Je kunt ook onderdelen gebruiken zonder alles te doen: de gedeelde taal en value objects kosten weinig en leveren direct iets op.

## Hoe past dit in Laravel?

De Eloquent models die je in Part 2 hebt gebruikt zijn *Active Record*: het model is tegelijk een stuk domein en een stuk database. Dat is handig, maar het betekent dat domein en infrastructuur door elkaar lopen. Je kunt DDD begrippen er wel in toepassen zonder alles te verplaatsen: een value object zoals `FileSize`, regels in het model zoals `canUpload()` in het `better` voorbeeld, en events die Laravel zelf ondersteunt.

Er is meer dan we hier behandelen, zoals Event Sourcing en CQRS. Die zijn voor grote systemen en vallen buiten deze cursus.

## Samengevat

| Begrip | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Aggregate | Wie bewaakt deze regel? | Een publieke lijst waar iedereen aan kan toevoegen |
| Repository | Weet het domein hoe het wordt opgeslagen? | `save()` en `load()` in een domeinclass |
| Domain service | Past deze regel bij één entity? | Een controller die de interne opbouw van twee entities kent |
| Domain event | Moet het domein weten wie hierop reageert? | Het domein dat zelf een e-mail stuurt of logt |
| Anemic model | Zit de regel in de entity of eromheen? | Entities met alleen getters en setters |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/ddd/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [aggregate_voor.php](./example/ddd/aggregate_voor.php) en [aggregate_na.php](./example/ddd/aggregate_na.php) bewaken de regel voor het maximaal aantal uitleningen, eerst met een publieke lijst en daarna met `Library` als root.
2. [repository_voor.php](./example/ddd/repository_voor.php) en [repository_na.php](./example/ddd/repository_na.php) slaan de bibliotheek op, eerst in het domein zelf en daarna met een repository.
3. [domain_service_voor.php](./example/ddd/domain_service_voor.php) en [domain_service_na.php](./example/ddd/domain_service_na.php) geven een boek door tussen twee leden, eerst in de controller en daarna met een `BookTransfer`.
4. [domain_event_voor.php](./example/ddd/domain_event_voor.php) en [domain_event_na.php](./example/ddd/domain_event_na.php) reageren op een uitlening, eerst met een e-mail in `Library` en daarna met een `BookLent` event.
5. [anemic_voor.php](./example/ddd/anemic_voor.php) en [anemic_na.php](./example/ddd/anemic_na.php) beschermen de beschikbaarheid van een boek, eerst met een service en setters en daarna met een rijk model.

## Opgaven

De opgaven staan in [ddd_opgaven_2.md](./ddd_opgaven_2.md).
