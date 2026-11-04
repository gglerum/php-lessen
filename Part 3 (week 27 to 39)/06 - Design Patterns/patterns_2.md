# Design patterns deel 2: Decorator, Adapter en Facade

In [patterns_1.md](./patterns_1.md) hebben we Singleton, Strategy, Factory en Observer behandeld. Nu volgen drie structural patterns: ze gaan over hoe je classes om elkaar heen zet. We gebruiken weer `WordSource` uit SOLID deel 2 en de bibliotheek uit de vorige lessen.

## Decorator

Je voegt gedrag toe door een object in te pakken, zonder de class te veranderen en zonder een subclass te maken.

Stel dat we alleen woorden van minimaal vijf letters willen. Met overerving krijgen we een subclass per bron:

```PHP
class LongArrayWords extends ArrayWords
{
    public function get(): array
    {
        return array_values(array_filter(parent::get(), fn(string $word) => strlen($word) >= 5));
    }
}

class LongDatabaseWords extends DatabaseWords
{
    // precies dezelfde filter nog een keer
}
```

Voor elke nieuwe bron en elke nieuwe filter groeit het aantal classes: bij twee bronnen en twee filters zijn het er al vier. Met een Decorator is de filter zelf een `WordSource` die een andere `WordSource` inpakt:

```PHP
class LongWordsOnly implements WordSource
{
    private WordSource $source;

    public function __construct(WordSource $source)
    {
        $this->source = $source;
    }

    public function get(): array
    {
        return array_values(array_filter($this->source->get(), fn(string $word) => strlen($word) >= 5));
    }
}

new LongWordsOnly(new ArrayWords());
new LongWordsOnly(new DatabaseWords());
```

De decorator werkt met elke bron en we hebben maar één extra class nodig. Omdat een decorator zelf een `WordSource` is, kun je ze ook stapelen: `new LongWordsOnly(new LongWordsOnly(new ArrayWords()))`.

Dit is Open/Closed uit SOLID deel 1: we voegen gedrag toe zonder bestaande classes te veranderen. En het is een voorbeeld van "composition over inheritance": we combineren objecten in plaats van te erven.

Zie `decorator_voor.php` en `decorator_na.php`.

## Adapter

Je past een class aan zodat hij op een interface past die je zelf hebt, zonder de class te veranderen.

Je krijgt vaak te maken met code die je niet zelf hebt geschreven en niet kunt aanpassen, zoals een externe API. Stel dat een externe woorden API alleen een JSON string teruggeeft:

```PHP
class ThirdPartyWordApi
{
    public function fetchWordsAsJson(): string
    {
        return '{"words": ["kiwi", "watermelon", "apple"]}';
    }
}
```

Zonder Adapter moet de `LongestWordPicker` weten hoe deze API werkt:

```PHP
$words = json_decode($this->api->fetchWordsAsJson(), true)['words'];
```

De picker is nu vast aan deze API en aan JSON. Met een Adapter zit alle kennis over de API in één class, die onze eigen `WordSource` implementeert:

```PHP
class ApiWords implements WordSource
{
    private ThirdPartyWordApi $api;

    public function __construct(ThirdPartyWordApi $api)
    {
        $this->api = $api;
    }

    public function get(): array
    {
        return json_decode($this->api->fetchWordsAsJson(), true)['words'];
    }
}

new LongestWordPicker(new ApiWords(new ThirdPartyWordApi()));
```

De picker weet niets van de API of van JSON. Verandert de API, dan verandert alleen de adapter. Dit is Protected Variations uit les 04: we zetten een stabiele interface om iets dat kan veranderen.

Het verschil met een Decorator: een decorator voegt gedrag toe aan iets dat al op onze interface past. Een adapter zorgt dat iets dat er *niet* op past er alsnog op past.

Zie `adapter_voor.php` en `adapter_na.php`.

## Facade

Je geeft een groep classes één eenvoudige ingang.

Stel dat het uitlenen van een boek uit vier stappen bestaat: controleren of het lid bestaat, het boek uitlenen, een bericht sturen en loggen. Zonder Facade moet elke plek die een boek uitleent de stappen en de volgorde kennen:

```PHP
if ($members->exists('Anna')) {
    $library->lend('Misery', 'Anna');
    $notifier->send('Anna', 'Misery is uitgeleend');
    $logger->log('Misery uitgeleend aan Anna');
}

// en dezelfde vier stappen nog eens op een andere plek
```

Met een Facade staan de stappen op één plek:

```PHP
class LibraryFacade
{
    public function __construct(MemberRepository $members, Library $library, Notifier $notifier, Logger $logger) { ... }

    public function lend(string $title, string $member): void
    {
        if (!$this->members->exists($member)) {
            return;
        }

        $this->library->lend($title, $member);
        $this->notifier->send($member, "$title is uitgeleend");
        $this->logger->log("$title uitgeleend aan $member");
    }
}

$facade->lend('Misery', 'Anna');
$facade->lend('It', 'Bram');
```

Wie een boek wil uitlenen hoeft alleen nog de facade te kennen. Dit is Indirection uit les 04: een tussenlaag zodat de aanroeper niet met alle losse classes hoeft te praten. De facade verbergt de classes niet voor altijd: wie meer nodig heeft kan ze nog steeds direct gebruiken.

*Let op: in Part 2 kom je de Laravel Facades tegen (zoals `Cache::get()` of `Auth::user()`). Die heten zo, maar het is niet hetzelfde als het klassieke Facade pattern dat we hier bespreken. Wat ze precies zijn komt daar aan bod.*

Zie `facade_voor.php` en `facade_na.php`.

## Wanneer gebruik je ze niet?

Net als bij alle principes uit deze lessen kun je ook met patterns doorschieten. Een bekende uitspraak: als je een hamer hebt, lijkt alles een spijker.

- Een **Decorator** voor één filter die je maar op één plek gebruikt is meer code dan de filter zelf.
- Een **Adapter** is overbodig als je de class zelf kunt aanpassen.
- Een **Facade** met één regel erin die alleen maar doorgeeft voegt niets toe.
- Een **Factory** voor één soort object, of een **Strategy** met één uitwerking en geen reden voor een tweede, is vaak te vroeg gebouwd.

Een goede vuistregel: schrijf eerst de eenvoudige versie. Zie je het probleem ontstaan (een `if` die steeds langer wordt, dezelfde stappen op meerdere plekken, een subclass per combinatie), dan pas gebruik je het pattern. Dit is YAGNI uit les 01.

## Samengevat

| Pattern | Probleem dat je herkent | Rode vlag in de code |
|---|---|---|
| Decorator | Je wilt gedrag toevoegen zonder een subclass per combinatie | Subclasses die elkaar bijna herhalen (`LongArrayWords`, `LongDatabaseWords`) |
| Adapter | Een class past niet op de interface die je nodig hebt | Code die details van een externe class of API kent |
| Facade | Dezelfde reeks stappen komt op meerdere plekken terug | Aanroepers die de volgorde van meerdere classes moeten kennen |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/patterns/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [decorator_voor.php](./example/patterns/decorator_voor.php) en [decorator_na.php](./example/patterns/decorator_na.php) filteren woorden, eerst met een subclass per bron en daarna met een decorator.
2. [adapter_voor.php](./example/patterns/adapter_voor.php) en [adapter_na.php](./example/patterns/adapter_na.php) gebruiken een externe woorden API, eerst direct en daarna via een adapter.
3. [facade_voor.php](./example/patterns/facade_voor.php) en [facade_na.php](./example/patterns/facade_na.php) lenen een boek uit, eerst met losse stappen en daarna via een facade.

## Opgaven

De opgaven staan in [patterns_opgaven_2.md](./patterns_opgaven_2.md). In [patterns_3.md](./patterns_3.md) volgen de overige patterns.
