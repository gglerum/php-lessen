# GRASP

## Welke class krijgt welke taak?

In Part 1 heb je steeds dezelfde vraag moeten beantwoorden: waar zet ik deze code? Hoort `calculateLateFee()` in `Book`, in `BookController` of in een nieuwe class? Met SOLID in de vorige les hebben we gekeken hoe een class is opgebouwd. GRASP gaat over de vraag *welke class een taak moet krijgen*.

GRASP staat voor General Responsibility Assignment Software Patterns. Het zijn negen vuistregels. Net als alle principes uit deze lessen zijn het geen wetten. Ze kunnen elkaar zelfs tegenspreken, we zien daar straks een voorbeeld van.

Vier van de negen hebben we eigenlijk al gezien onder een andere naam:

| GRASP | Al behandeld als |
|---|---|
| High Cohesion | Single Responsibility (SOLID deel 1) |
| Polymorphism | Open/Closed en Liskov Substitution (SOLID deel 1) |
| Protected Variations | Dependency Inversion (SOLID deel 2) |
| Indirection | Dependency Inversion en de `Router` uit les 05 |

Die vier bespreken we kort aan het eind. De andere vijf zijn nieuw en krijgen elk een eigen voorbeeld: Information Expert, Creator, Controller, Low Coupling en Pure Fabrication. We gebruiken een bibliotheek waarin boeken worden uitgeleend.

## Information Expert

De class die de gegevens heeft, krijgt de taak die die gegevens nodig heeft.

Een uitlening (`Loan`) weet wanneer het boek terug moet zijn en hoeveel de boete per dag is. Kijk wie de boete berekent:

```PHP
class LoanController
{
    public function showLateFee(Loan $loan, DateTimeImmutable $today): void
    {
        $daysLate = (int)$loan->getDueDate()->diff($today)->format('%r%a');
        $fee = max(0, $daysLate) * $loan->getFeePerDay();

        echo $loan->getTitle() . ': ' . $fee . " boete\n";
    }
}
```

De controller haalt alle gegevens uit `Loan` om er zelf iets mee te doen. Verandert de berekening (bijvoorbeeld een maximumboete), dan moeten we de controller aanpassen, terwijl alle gegevens in `Loan` zitten. Een andere class die de boete ook nodig heeft, zou de berekening moeten kopiëren.

`Loan` is de expert, dus `Loan` berekent de boete:

```PHP
class Loan
{
    // ...

    public function calculateLateFee(DateTimeImmutable $today): float
    {
        $daysLate = (int)$this->dueDate->diff($today)->format('%r%a');

        return max(0, $daysLate) * $this->feePerDay;
    }
}

// in de controller
echo $loan->getTitle() . ': ' . $loan->calculateLateFee($today) . " boete\n";
```

Een goede vraag om jezelf te stellen is: *welke class heeft de gegevens die dit nodig heeft?* Haal je veel `get`-methods van één object op om iets uit te rekenen, dan hoort die berekening waarschijnlijk in dat object.

Zie `expert_voor.php` en `expert_na.php`.

## Creator

Wie maakt een object aan? De class die het object bevat, bijhoudt, gebruikt of de gegevens heeft om het te maken.

```PHP
class LoanController
{
    public function lend(Library $library, string $title, string $member, DateTimeImmutable $today): void
    {
        // de controller maakt de uitlening aan en moet daarvoor weten hoe lang we uitlenen
        $loan = new Loan($title, $member, $today->modify('+14 days'));

        $library->addLoan($loan);
        // ...
    }
}
```

De bibliotheek houdt alle uitleningen bij. Toch maakt de controller ze aan en moet daarvoor weten dat een boek 14 dagen mag worden uitgeleend. Die regel hoort bij de bibliotheek. Komt er een tweede plek bij waar een boek wordt uitgeleend (een console script, een API), dan staan de 14 dagen op twee plaatsen.

De bibliotheek bewaart de `Loan` objecten, dus de bibliotheek maakt ze ook aan:

```PHP
class Library
{
    private const LOAN_DAYS = 14;

    private array $loans = [];

    public function lend(string $title, string $member, DateTimeImmutable $today): Loan
    {
        $loan = new Loan($title, $member, $today->modify('+' . self::LOAN_DAYS . ' days'));
        $this->loans[] = $loan;

        return $loan;
    }
}
```

Let op: Creator gaat over het aanmaken van *domeinobjecten*, dingen die in het probleem voorkomen (een uitlening, een boek). Services die je class nodig heeft (zoals een `WordSource`) maak je niet zelf aan, die krijg je mee via dependency injection. Dat hebben we in SOLID deel 2 gezien.

Zie `creator_voor.php` en `creator_na.php`.

## Controller

Het eerste object na de gebruikersinterface dat een verzoek afhandelt, geeft het werk door aan de rest van de applicatie zonder het zelf te doen.

Dit ken je al: `BookController` uit les 05 en `console.php` met `GameService` uit `hangman_v3`. De controller leest het verzoek, geeft het door en kiest wat de gebruiker daarna te zien krijgt. Wat er daartussen gebeurt, is niet meer zijn zaak.

De fout die je vaak ziet is dat de controller meer gaat doen. Kijk wat er gebeurt als we de regel "een lid mag maximaal 2 boeken lenen" invoeren:

```PHP
class LoanController
{
    private array $loansPerMember = [];

    public function handleLendRequest(array $request): void
    {
        // ...

        // een bedrijfsregel: een lid mag maximaal 2 boeken tegelijk lenen
        $current = $this->loansPerMember[$member] ?? 0;
        if ($current >= 2) {
            echo "$member mag niet meer dan 2 boeken lenen\n";
            return;
        }

        $this->loansPerMember[$member] = $current + 1;
        echo "$title is uitgeleend aan $member\n";
    }
}
```

De bedrijfsregel zit nu in de controller. Een console script of een API die ook boeken uitleent, moet de regel kopiëren of vergeet hem. Dit heet een "fat controller": een controller waarin de logica is gekropen.

De regel hoort bij de bibliotheek. De controller geeft door en vangt het resultaat op:

```PHP
class Library
{
    private const MAX_LOANS_PER_MEMBER = 2;

    public function lend(string $title, string $member): void
    {
        // ...
        if ($current >= self::MAX_LOANS_PER_MEMBER) {
            throw new DomainException("$member mag niet meer dan " . self::MAX_LOANS_PER_MEMBER . ' boeken lenen');
        }
        // ...
    }
}

class LoanController
{
    public function handleLendRequest(array $request): void
    {
        // het verzoek lezen en controleren dat er iets is ingevuld ...

        try {
            $this->library->lend($title, $member);
            echo "$title is uitgeleend aan $member\n";
        } catch (DomainException $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}
```

In de controller blijven over: het verzoek lezen, controleren of er iets is ingevuld, doorgeven en kiezen wat je toont. Dit zie je ook in `Library::lend()` bij Creator hierboven.

Zie `controller_voor.php` en `controller_na.php`.

## Low Coupling

Hoe minder een class van andere classes weet, hoe makkelijker hij te veranderen is. Koppeling betekent: als B verandert, moet A ook veranderen.

```PHP
echo $library->getMembers()[0]->getName() . ' woont in ' . $library->getMembers()[0]->getAddress()->getCity() . "\n";
```

Deze regel kent drie classes (`Library`, `Member` en `Address`) en weet hoe ze aan elkaar zitten: een lid heeft een adres en een adres heeft een stad. Krijgt een lid later meerdere adressen, dan breekt deze regel, en elke andere plek waar zo'n keten staat ook.

Door `Member` zelf te laten zeggen waar hij woont, hoeft de rest `Address` niet meer te kennen:

```PHP
class Member
{
    // ...

    public function getCity(): string
    {
        return $this->address->getCity();
    }
}

$member = $library->getFirstMember();
echo $member->getName() . ' woont in ' . $member->getCity() . "\n";
```

Hoe je zulke ketens herkent en waarom het een probleem is, komt in les 05 terug als de Law of Demeter. Hier gaat het om het idee: koppeling kost je flexibiliteit.

Interfaces uit SOLID deel 2 verlagen ook de koppeling: de picker kent alleen `WordSource`, niet `ArrayWords` of `DatabaseWords`.

Let op: nul koppeling bestaat niet. Classes moeten met elkaar praten om iets te doen. Het gaat erom dat je weet waar de koppeling zit en dat je hem niet groter maakt dan nodig.

Zie `coupling_voor.php` en `coupling_na.php`.

## Pure Fabrication

Soms hoort een taak nergens goed thuis. Dan verzin je een class die niet in het domein bestaat, om de cohesie hoog en de koppeling laag te houden.

Wat hoort bij een boek? Een titel, een auteur. Waar het boek wordt bewaard hoort daar eigenlijk niet bij, maar Information Expert zegt dat `Book` de gegevens heeft, dus laten we hem zichzelf opslaan?

```PHP
class Book
{
    private static array $storage = [];

    // ...

    public function save(): void
    {
        self::$storage[] = $this;
    }

    public static function findAll(): array
    {
        return self::$storage;
    }
}
```

Nu weet `Book` ook hoe en waar hij wordt bewaard. Willen we een database in plaats van een array, dan moeten we `Book` zelf aanpassen. Een `Book` kun je niet meer gebruiken zonder dat hij een opslag meebrengt.

De oplossing is een class te verzinnen die in een echte bibliotheek niet bestaat: een `BookRepository`.

```PHP
class Book
{
    // weet alleen nog wat een boek is
}

class BookRepository
{
    public function save(Book $book): void { ... }

    public function findAll(): array { ... }
}
```

Je hebt in Part 1 al repositories gezien en de `Router` uit les 05 is er ook een. Dat zijn beide geen dingen uit het probleem (een bibliotheek heeft geen router), maar we hebben ze nodig om de rest overzichtelijk te houden.

Hier zie je ook dat principes elkaar tegenspreken. Information Expert zegt dat `Book` zichzelf opslaat omdat hij de gegevens heeft. Pure Fabrication, High Cohesion en Low Coupling zeggen dat de opslag ergens anders hoort. Er is geen goed antwoord dat altijd klopt: je weegt af. Bij een klein script is een `save()` in `Book` prima. Pas als je merkt dat opslag de class vervuilt of vast zit aan één manier van opslaan, verzin je een extra class.

Zie `fabrication_voor.php` en `fabrication_na.php`.

## De overige vier

Deze vier hebben we in SOLID al gezien. Hier kort wat GRASP erover zegt.

**High Cohesion.** Alles in een class hoort bij elkaar. Een class met methods die niets met elkaar te maken hebben (`calculateLateFee()`, `sendEmail()` en `drawHangman()` in één class) heeft lage cohesie. Dit is Single Responsibility van de andere kant bekeken. Je zag het in `hangman_v3`, waar het tekenen van de galg een eigen class kreeg (`DrawnHangman`) in plaats van in `GameService` te staan.

**Polymorphism.** Heb je gedrag dat per type verschilt, gebruik dan polymorphism in plaats van `if` of `switch` op het type. De boete per type item in `ocp_na.php` is hiervan een voorbeeld, en het is Open/Closed uit SOLID deel 1.

**Protected Variations.** Zet een stabiele interface om een plek heen waar dingen veranderen. In `hangman_v4` verandert de bron van de woorden (een array of een database), dus `WordSource` beschermt de rest van het spel daartegen. Dit is Dependency Inversion uit SOLID deel 2.

**Indirection.** Zet een tussenlaag tussen twee dingen zodat ze elkaar niet direct hoeven te kennen. De `Router` zit tussen het verzoek en de controller, en de `BookRepository` zit tussen het domein en de opslag. Dit verlaagt de koppeling.

## Wanneer gebruik je het niet?

GRASP is geen checklist. Voor een klein script is één class of één bestand prima, anders loop je tegen YAGNI uit les 01 aan. En zoals je bij Pure Fabrication zag, spreken de principes elkaar tegen: Information Expert wil dat een class meer doet, High Cohesion wil juist dat hij minder doet. Zie het als vragen die je jezelf stelt, niet als regels die je afvinkt.

## Samengevat

| Principe | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Information Expert | Welke class heeft de gegevens die dit nodig heeft? | Veel `get`-methods van één object om iets uit te rekenen |
| Creator | Welke class houdt dit object bij of heeft de gegevens om het te maken? | Een controller die `new` doet op iets dat een andere class bijhoudt |
| Controller | Geeft deze class alleen door, of doet hij zelf het werk? | Bedrijfsregels en berekeningen in de controller |
| Low Coupling | Als dit verandert, hoeveel andere classes moeten dan mee? | Lange ketens van `get()` aanroepen |
| Pure Fabrication | Maakt deze taak de class minder samenhangend, en kan een extra class dit oplossen? | Een domeinclass die ook weet hoe hij wordt opgeslagen of getoond |
| High Cohesion | Horen alle methods bij elkaar? | Een class met methods die niets met elkaar te maken hebben |
| Polymorphism | Kijk ik naar het type om te bepalen wat ik moet doen? | `instanceof` of een `switch` op een type |
| Protected Variations | Wat verandert hier waarschijnlijk, en wie hoeft dat niet te weten? | Een concrete class op een plek waar een andere uitwerking denkbaar is |
| Indirection | Moeten deze twee elkaar echt direct kennen? | Twee classes die elkaars interne details gebruiken |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/grasp/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [expert_voor.php](./example/grasp/expert_voor.php) en [expert_na.php](./example/grasp/expert_na.php) berekenen de boete van een uitlening, eerst in de controller en daarna in `Loan`.
2. [creator_voor.php](./example/grasp/creator_voor.php) en [creator_na.php](./example/grasp/creator_na.php) maken een uitlening aan, eerst in de controller en daarna in `Library`.
3. [controller_voor.php](./example/grasp/controller_voor.php) en [controller_na.php](./example/grasp/controller_na.php) laten een bedrijfsregel eerst in de controller staan en verplaatsen die daarna naar `Library`.
4. [coupling_voor.php](./example/grasp/coupling_voor.php) en [coupling_na.php](./example/grasp/coupling_na.php) vergelijken een keten van `get()` aanroepen met een method die de keten verbergt.
5. [fabrication_voor.php](./example/grasp/fabrication_voor.php) en [fabrication_na.php](./example/grasp/fabrication_na.php) laten een `Book` eerst zichzelf opslaan en daarna met een `BookRepository`.

## Opgaven

De opgaven staan in [grasp_opgaven_1.md](./grasp_opgaven_1.md).
