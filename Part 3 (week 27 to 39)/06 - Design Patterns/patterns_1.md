# Design patterns deel 1: Singleton, Strategy, Factory en Observer

## Wat is een design pattern?

Een design pattern is een bekende oplossing voor een probleem dat steeds terugkomt, met een naam. Het zijn geen stukken code die je kopieert, maar een manier om classes samen te laten werken. Vaak heb je een pattern al gebruikt zonder dat je wist dat het zo heette.

Waarom is de naam zo belangrijk? Een collega zegt "dit is een Strategy" en je weet in drie woorden wat in tien zinnen uitgelegd had moeten worden. Patterns zijn dus vooral een gedeelde taal.

De patterns worden grofweg in drie groepen verdeeld:

- **Creational**: hoe maak je objecten aan (Singleton, Factory, Builder).
- **Structural**: hoe zet je classes in elkaar (Decorator, Adapter, Facade).
- **Behavioural**: hoe werken objecten samen (Strategy, Observer, Command, State).

Let op: een pattern is geen doel. Je zet er geen in omdat het kan, maar omdat je het probleem herkent. Gebruik je een pattern zonder dat het probleem er is, dan krijg je alleen meer classes. Dit is YAGNI uit les 01.

In Part 1 en in de vorige lessen heb je al een aantal patterns gezien:

| Pattern | Waar je het al zag |
|---|---|
| Singleton | `DbManager` en `PdoService` (les 07 en 09) |
| Strategy | `WordSource` met `Words` en `DbWords` in `hangman_v4`, en callbacks uit les 03 |
| Repository | `BookRepository` (les 04 van Part 3, Pure Fabrication) en de repositories uit les 08 |
| MVC | `Router`, `BookController` en de HTML templates uit les 05 |

De nieuwe patterns koppelen we aan de principes die je net geleerd hebt:

| Pattern | Sluit aan op |
|---|---|
| Factory | Creator (GRASP) |
| Strategy | Open/Closed en Dependency Inversion (SOLID) |
| Observer | Low Coupling en Indirection (GRASP) |
| Decorator | Open/Closed (SOLID) |
| Adapter | Protected Variations (GRASP) |
| Facade | Indirection (GRASP) |

In dit deel behandelen we Singleton, Strategy, Factory en Observer. In [patterns_2.md](./patterns_2.md) volgen Decorator, Adapter en Facade en in [patterns_3.md](./patterns_3.md) de overige patterns.

## Singleton

Er bestaat maar één instantie van een class, en iedereen haalt die zelf op.

```PHP
class Config
{
    private static ?Config $instance = null;

    private array $values = ['loan_days' => 14];

    // de private constructor zorgt dat niemand zelf een Config kan aanmaken
    private function __construct()
    {
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function get(string $key): int
    {
        return $this->values[$key];
    }
}

class Library
{
    public function lend(string $title): string
    {
        $days = Config::getInstance()->get('loan_days');

        return "$title is uitgeleend voor $days dagen";
    }
}
```

Dit is wat je in `DbManager` hebt gezien, en het is verleidelijk: je hoeft niets door te geven, je roept het gewoon aan. Maar er zijn nadelen:

- **De afhankelijkheid is verstopt.** Aan `new Library()` zie je niet dat hij een `Config` nodig heeft. Dat zit pas in de method.
- **Je kunt niet wisselen.** Wil je `Library` uitproberen met een uitleenduur van 7 dagen, dan kan dat niet, omdat `Library` zelf kiest welke `Config` hij gebruikt.
- **Globale toestand.** Elke plek in de code kan bij de singleton en hem veranderen, zonder dat je het ziet.

Het alternatief is dependency injection uit SOLID deel 2. Het idee van "één instantie" blijft: we maken één `Config` en geven die door.

```PHP
class Library
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
}

$config = new Config(['loan_days' => 14]);
echo (new Library($config))->lend('Misery');

// en nu kunnen we Library ook uitproberen met een andere uitleenduur
echo (new Library(new Config(['loan_days' => 7])))->lend('It');
```

Er is nog steeds maar één `Config`, maar het is jouw keuze en geen verplichting van de class zelf. De service container van Laravel werkt zo: `singleton()` zegt dat er één instantie wordt gemaakt en doorgegeven, zonder dat de class zelf een `getInstance()` nodig heeft. Dat zie je in Part 2.

Zie `singleton_voor.php` en `singleton_na.php`.

## Strategy

Je wisselt gedrag door een andere class mee te geven die dezelfde afspraak (interface) heeft. Dit ken je al uit `hangman_v4`: `WordSource` met `Words` en `DbWords`.

Hier een catalogus die boeken kan sorteren. Zonder Strategy:

```PHP
public function getTitles(string $sortBy): array
{
    $books = $this->books;

    if ($sortBy === 'title') {
        usort($books, fn(array $a, array $b) => $a['title'] <=> $b['title']);
    } elseif ($sortBy === 'author') {
        usort($books, fn(array $a, array $b) => $a['author'] <=> $b['author']);
    }

    return array_column($books, 'title');
}
```

Elke nieuwe manier van sorteren betekent dat we deze method aanpassen. Dit is de lange `if` op het type die we bij Open/Closed zagen. Met Strategy:

```PHP
interface SortStrategy
{
    public function sort(array $books): array;
}

class SortByTitle implements SortStrategy { ... }

class SortByAuthor implements SortStrategy { ... }

public function getTitles(SortStrategy $strategy): array
{
    return array_column($strategy->sort($this->books), 'title');
}

$catalog->getTitles(new SortByTitle());
$catalog->getTitles(new SortByAuthor());
```

`Catalog` weet niet hoe er gesorteerd wordt. Een nieuwe manier is een nieuwe class, en `Catalog` hoeft niet te veranderen.

Je kent ook de callback functions uit les 03 van Part 1. Een callback is eigenlijk een Strategy in het klein: `usort()` krijgt gedrag mee in een closure. Voor een sorteerregel van één regel is een closure meestal genoeg. Een class gaat pas lonen als de strategy meer doet, een eigen naam verdient of afhankelijkheden heeft.

Zie `strategy_voor.php` en `strategy_na.php`.

## Factory

Eén plek bepaalt welk object er wordt aangemaakt. Dit is Creator uit les 04 van Part 3 in actie.

Stel dat een item uit een formulier komt en ook uit een importbestand. Zonder Factory staat de keuze op twee plekken:

```PHP
function createItemFromForm(string $type, string $title): Item
{
    if ($type === 'book') {
        return new Book($title);
    } elseif ($type === 'magazine') {
        return new Magazine($title);
    } elseif ($type === 'boardgame') {
        return new Boardgame($title);
    }

    throw new InvalidArgumentException("Onbekend type: $type");
}

function createItemFromImport(array $row): Item
{
    switch ($row[0]) {
        case 'book':
            return new Book($row[1]);
        // en nog twee keer hetzelfde ...
    }
}
```

Een nieuw type item betekent dat je beide plekken moet aanpassen, en vergeet je er één, dan gaat het mis. Met een Factory staat de keuze op één plek:

```PHP
class ItemFactory
{
    public static function create(string $type, string $title): Item
    {
        return match ($type) {
            'book' => new Book($title),
            'magazine' => new Magazine($title),
            'boardgame' => new Boardgame($title),
            default => throw new InvalidArgumentException("Onbekend type: $type"),
        };
    }
}

ItemFactory::create('book', 'Misery');
ItemFactory::create($row[0], $row[1]);
```

Het formulier en de import gebruiken dezelfde factory. Een nieuw type item is nu één wijziging.

Let op: een Factory is geen vervanging voor elke `new`. Maak je maar één soort object aan, dan is `new Book($title)` gewoon duidelijker. De factory loont pas als er meerdere types zijn of als het aanmaken ingewikkeld is.

Zie `factory_voor.php` en `factory_na.php`.

## Observer

Een object meldt dat er iets is gebeurd, en andere objecten kunnen daarop reageren zonder dat het object ze kent.

Na het uitlenen van een boek willen we een e-mail sturen, loggen en een statistiek bijwerken. Zonder Observer kent `Library` ze alle drie:

```PHP
class Library
{
    public function __construct(Mailer $mailer, Logger $logger, Statistics $statistics) { ... }

    public function lend(string $title, string $member): void
    {
        $this->mailer->sendLoanMail($member, $title);
        $this->logger->log("$title uitgeleend aan $member");
        $this->statistics->countLoan();
    }
}
```

Een vierde reactie, bijvoorbeeld een sms, betekent dat we `Library` moeten aanpassen. Terwijl `Library` eigenlijk alleen hoeft te weten dat er iets is uitgeleend. Met Observer:

```PHP
interface LoanObserver
{
    public function onLoan(string $title, string $member): void;
}

class Library
{
    private array $observers = [];

    public function addObserver(LoanObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function lend(string $title, string $member): void
    {
        foreach ($this->observers as $observer) {
            $observer->onLoan($title, $member);
        }
    }
}

$library->addObserver(new MailObserver());
$library->addObserver(new LogObserver());
$library->addObserver(new StatisticsObserver());
```

`Library` meldt alleen dat er iets is uitgeleend. Wie daarna luistert, weet `Library` niet. Een vierde reactie is een nieuwe class en een regel erbij, `Library` blijft ongewijzigd. Dit verlaagt de koppeling, zoals we bij Low Coupling en Indirection zagen.

In Laravel heet dit events en listeners. Daar kom je in Part 2 mee in aanraking, dus dit is geen nieuw idee maar de basis eronder.

Zie `observer_voor.php` en `observer_na.php`.

## Samengevat

| Pattern | Probleem dat je herkent | Rode vlag in de code |
|---|---|---|
| Singleton | Je wilt maar één instantie van iets | `getInstance()` verspreid door de code, verstopte afhankelijkheden |
| Strategy | Gedrag moet wisselbaar zijn | Een lange `if` of `switch` op het soort gedrag |
| Factory | Het aanmaken van objecten hangt af van een type | Dezelfde `if` of `switch` met `new` op meerdere plekken |
| Observer | Meerdere dingen moeten reageren op één gebeurtenis | Een class die alle reacties zelf aanroept en bij elke nieuwe reactie verandert |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/patterns/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [singleton_voor.php](./example/patterns/singleton_voor.php) en [singleton_na.php](./example/patterns/singleton_na.php) vergelijken een `Config` singleton met een `Config` die via de constructor wordt meegegeven.
2. [strategy_voor.php](./example/patterns/strategy_voor.php) en [strategy_na.php](./example/patterns/strategy_na.php) sorteren een catalogus, eerst met een `if` en daarna met strategy classes.
3. [factory_voor.php](./example/patterns/factory_voor.php) en [factory_na.php](./example/patterns/factory_na.php) maken items aan, eerst op twee plekken en daarna met een `ItemFactory`.
4. [observer_voor.php](./example/patterns/observer_voor.php) en [observer_na.php](./example/patterns/observer_na.php) reageren op een uitlening, eerst met een class die alles aanroept en daarna met observers.

## Opgaven

De opgaven staan in [patterns_opgaven_1.md](./patterns_opgaven_1.md). In [patterns_2.md](./patterns_2.md) gaan we verder met Decorator, Adapter en Facade.
