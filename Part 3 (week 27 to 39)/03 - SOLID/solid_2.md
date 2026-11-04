# SOLID deel 2: Interface Segregation en Dependency Inversion

In [solid_1.md](./solid_1.md) hebben we Single Responsibility, Open/Closed en Liskov Substitution behandeld. Nu volgen de laatste twee principes. Beide gaan over interfaces en over hoe classes van elkaar afhangen. Daarom eerst een korte terugblik.

## Terugblik: interfaces en dependency injection

Twee dingen uit Part 1 hebben we nodig.

**Een interface** is een afspraak over wat een class kan, zonder te zeggen hoe. De class die de interface implementeert bepaalt het hoe. Dit hebben we in OOP 3 gezien.

**Dependency injection** betekent dat een class zijn afhankelijkheden meekrijgt van buiten, in plaats van ze zelf aan te maken. We zagen dit in `hangman_v3`, waar `RandomWordGenerator` een `Words` object meekrijgt. Kijk naar het verschil:

```PHP
class LongestWordPicker
{
    private array $words;

    public function __construct()
    {
        // de class maakt zijn eigen woordenlijst aan
        $words = new Words();
        $this->words = $words->get();
    }
}

$picker = new LongestWordPicker();
```

```PHP
class LongestWordPicker
{
    private array $words;

    // de woordenlijst krijgen we mee van buiten
    public function __construct(Words $words)
    {
        $this->words = $words->get();
    }
}

$picker = new LongestWordPicker(new Words());
```

In de eerste versie kunnen we de picker alleen gebruiken met precies deze woordenlijst. In de tweede bepaalt degene die de picker aanmaakt welke lijst er wordt gebruikt.

In les 04 stond de vraag: *waarom geven we `Words` mee en maken we de woordenlijst niet in `RandomWordGenerator`?* Het antwoord is dat de class dan niet meer afhankelijk is van waar de woorden vandaan komen. Maar in de tweede versie hierboven kunnen we alleen nog de class `Words` meegeven. Het echte voordeel komt pas bij het laatste principe, Dependency Inversion.

Zie `di_voor.php` en `di_na.php`.

## I: Interface Segregation

Liever een paar kleine interfaces dan één grote. Een class hoeft niet te implementeren wat hij niet gebruikt.

Stel dat we één interface maken voor alles wat een repository kan:

```PHP
interface Repository
{
    public function find(int $id): ?array;

    public function save(array $item): void;

    public function delete(int $id): void;
}
```

Voor `BookRepository` is dat prima. Maar het archief van de bibliotheek is alleen-lezen. Toch moet de `ArchiveRepository` alle drie de methods hebben:

```PHP
class ArchiveRepository implements Repository
{
    public function find(int $id): ?array { ... }

    public function save(array $item): void
    {
        throw new LogicException('Het archief is alleen-lezen');
    }

    public function delete(int $id): void
    {
        throw new LogicException('Het archief is alleen-lezen');
    }
}
```

Dit is niet alleen onhandig, het is ook gevaarlijk: wie een `ArchiveRepository` als `Repository` gebruikt, ontdekt pas tijdens het draaien dat `save()` niet werkt. Je herkent hier het probleem uit Liskov Substitution.

De oplossing is de interface op te splitsen in kleine afspraken:

```PHP
interface ReadableRepository
{
    public function find(int $id): ?array;
}

interface WritableRepository
{
    public function save(array $item): void;

    public function delete(int $id): void;
}

class BookRepository implements ReadableRepository, WritableRepository { ... }

class ArchiveRepository implements ReadableRepository
{
    public function find(int $id): ?array { ... }
}
```

Een class mag meerdere interfaces implementeren. De `ArchiveRepository` kiest alleen wat hij kan. Ook de code die een repository gebruikt vraagt nu om wat hij nodig heeft:

```PHP
// leest alleen, dus vraagt alleen om iets wat kan lezen
function printTitle(ReadableRepository $repository, int $id): void { ... }
```

Rode vlaggen zijn een method die alleen een exception gooit, een lege method die niets doet, en een interface waarvan je bij elke class een deel niet nodig hebt.

Zie `isp_voor.php` en `isp_na.php`.

## D: Dependency Inversion

Code op een hoog niveau (de class die iets *regelt*) hangt niet af van code op een laag niveau (de class die iets *uitvoert*). Beide hangen af van een interface.

Kijk naar de picker uit de terugblik. Die vraagt om een concrete class, `ArrayWords`:

```PHP
class LongestWordPicker
{
    private ArrayWords $source;

    public function __construct(ArrayWords $source)
    {
        $this->source = $source;
    }
}
```

Met dependency injection kunnen we de bron nu van buiten meegeven. Maar alleen een `ArrayWords`. Willen we woorden uit een database gebruiken, dan moeten we de picker aanpassen of een kopie maken die om `DatabaseWords` vraagt.

De oplossing is dat de picker om een interface vraagt:

```PHP
interface WordSource
{
    public function get(): array;
}

class ArrayWords implements WordSource { ... }

class DatabaseWords implements WordSource { ... }

class LongestWordPicker
{
    private WordSource $source;

    public function __construct(WordSource $source)
    {
        $this->source = $source;
    }
}

echo (new LongestWordPicker(new ArrayWords()))->pick();
echo (new LongestWordPicker(new DatabaseWords()))->pick();
```

De picker weet niet meer of de woorden uit een array, een database of een bestand komen. Hij weet alleen dat er een `get()` is die een array teruggeeft. De afhankelijkheid is omgedraaid: niet de picker bepaalt wat de bron is, maar de interface bepaalt wat de picker nodig heeft, en de bronnen passen zich daaraan aan.

Dit is precies wat er in `hangman_v4` is gebeurd. In `hangman_v3` kreeg `RandomWordGenerator` een `Words` object mee. In `hangman_v4` krijgt hij een `WordSource`, met de uitwerkingen `Words` en `DbWords`. De spellogica in `GameService` hoefde niet te veranderen om met de database te werken, en in `RandomWordGenerator` is alleen het type van de constructor parameter aangepast van `Words` naar `WordSource`. De database zelf zit in een nieuwe class, `DbWords`. Dat is ook Open/Closed uit deel 1.

Dependency Inversion en dependency injection zijn dus niet hetzelfde. Dependency injection is *hoe* een class zijn afhankelijkheid krijgt (via de constructor). Dependency Inversion is *waar* hij van afhankelijk is (een interface in plaats van een concrete class). Je hebt beide nodig.

Rode vlaggen zijn `new` in een constructor of method van een class die een andere class gebruikt, en een parameter die een concrete class verwacht terwijl er meerdere uitwerkingen denkbaar zijn.

Zie `dip_voor.php` en `dip_na.php`.

## Hoe werken ze samen?

Ze versterken elkaar. Single Responsibility houdt classes klein, zodat er iets te vervangen valt. Open/Closed en Liskov Substitution zorgen dat je dat veilig kunt doen. Interface Segregation houdt de afspraken klein en Dependency Inversion zorgt dat classes van die afspraken afhangen in plaats van van elkaar.

## Wanneer gebruik je het niet?

Net als bij DRY en YAGNI uit les 01 kun je ook hier doorschieten. Een interface met één uitwerking en geen reden voor een tweede is vaak te vroeg gebouwd. Een class voor elke regel code maakt je programma niet beter, alleen groter.

Een goede vuistregel: schrijf eerst de eenvoudige versie. Merk je dat dezelfde class steeds om verschillende redenen moet veranderen, of dat er een tweede uitwerking komt, dan maak je de interface. Dat geldt voor `WordSource` ook: de interface kwam er pas toen we een database wilden gebruiken.

## Samengevat

| Principe | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Interface Segregation | Hoef ik alles te implementeren wat in deze interface staat? | Een method die alleen een exception gooit of niets doet |
| Dependency Inversion | Weet deze class welke uitwerking hij gebruikt? | `new` in een constructor, een parameter die een concrete class vraagt |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/solid/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [di_voor.php](./example/solid/di_voor.php) en [di_na.php](./example/solid/di_na.php) laten dependency injection zien: een class die zijn woordenlijst zelf maakt en een class die hem meekrijgt.
2. [isp_voor.php](./example/solid/isp_voor.php) en [isp_na.php](./example/solid/isp_na.php) vergelijken één grote `Repository` interface met twee kleinere, waarbij het archief alleen-lezen is.
3. [dip_voor.php](./example/solid/dip_voor.php) en [dip_na.php](./example/solid/dip_na.php) laten de picker eerst afhangen van een concrete class en daarna van de interface `WordSource`, met een array en een gesimuleerde database als bron.

## Opgaven

De opgaven staan in [solid_opgaven_2.md](./solid_opgaven_2.md).
