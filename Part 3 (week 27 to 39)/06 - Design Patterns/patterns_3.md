# Design patterns deel 3: Builder, Chain of Responsibility, Command, Template Method en State

In de vorige twee delen hebben we patterns behandeld die je direct kunt koppelen aan de principes uit de vorige lessen. Er zijn nog meer patterns. In totaal zijn er 23 klassieke patterns beschreven door de "Gang of Four", en veel daarvan komen terug in het Laravel framework dat je in Part 2 hebt gebruikt. In dit deel behandelen we vijf daarvan. Bij elk pattern staat waar je het in Laravel terugziet. We doen dat zonder nieuwe Laravel uitleg: het gaat om herkennen wat je al gebruikt.

## Builder

Je bouwt een object stap voor stap op, in plaats van alles in één keer in een constructor te zetten.

Een constructor met veel parameters is lastig te lezen:

```PHP
$loan = new Loan('Misery', 'Anna', 21, true, 2, '');
```

Wat is `21`? Wat is `true`? Dat moet je in de constructor opzoeken, en ook als je alleen de standaardwaarden wilt moet je alle zes de waarden opgeven. Met een Builder heeft elke instelling een naam en een standaardwaarde:

```PHP
class LoanBuilder
{
    private int $days = 14;
    private bool $sendReminder = false;
    private int $maxRenewals = 1;

    public static function for(string $title, string $member): self { ... }

    public function days(int $days): self
    {
        $this->days = $days;

        return $this;
    }

    public function withReminder(): self
    {
        $this->sendReminder = true;

        return $this;
    }

    public function build(): Loan { ... }
}

$loan = LoanBuilder::for('Misery', 'Anna')->days(21)->withReminder()->maxRenewals(2)->build();
$simple = LoanBuilder::for('It', 'Bram')->build();
```

Elke method geeft het builder object terug (`return $this`), zodat je ze achter elkaar kunt zetten. Je leest nu wat er wordt ingesteld, en wat je niet noemt krijgt de standaardwaarde.

PHP heeft ook *named arguments*, die je al in `BookController` hebt gezien (`new Book(title: ..., author: ...)`). Daarmee wordt een constructor met veel parameters ook leesbaar. Voor een eenvoudige class is dat vaak genoeg. Een builder loont pas als het opbouwen stappen heeft, afhankelijk is van keuzes, of als je het resultaat pas aan het eind wilt valideren.

**In Laravel:** de Query Builder (`DB::table('books')->where(...)->orderBy(...)->get()`) werkt volgens hetzelfde idee: je bouwt een query stap voor stap op met methods die `$this` teruggeven, en pas bij `get()` wordt de query uitgevoerd.

Zie `builder_voor.php` en `builder_na.php`.

## Chain of Responsibility

Een verzoek gaat langs een reeks schakels. Elke schakel doet één ding en geeft het door aan de volgende, of stopt het.

Voor het uitlenen van een boek moeten meerdere controles gebeuren. Zonder het pattern staan ze achter elkaar in één functie:

```PHP
function validateLoan(array $request, array $members, array $availableBooks): string
{
    if (!in_array($request['member'], $members)) {
        return 'Onbekend lid';
    }

    if ($request['loans'] >= 2) {
        return 'Maximaal aantal uitleningen bereikt';
    }

    if (!in_array($request['title'], $availableBooks)) {
        return 'Boek is niet beschikbaar';
    }

    return 'ok';
}
```

Een nieuwe controle of een andere volgorde (bijvoorbeeld bij een zelfscan) betekent de functie aanpassen of kopiëren. Met een ketting is elke controle een eigen class:

```PHP
abstract class LoanCheck
{
    private ?LoanCheck $next = null;

    public function then(LoanCheck $next): LoanCheck
    {
        $this->next = $next;

        return $next;
    }

    public function handle(array $request): string
    {
        $error = $this->check($request);

        if ($error !== null) {
            return $error;
        }

        return $this->next?->handle($request) ?? 'ok';
    }

    abstract protected function check(array $request): ?string;
}

$chain = new MemberExists();
$chain->then(new UnderLoanLimit())->then(new BookAvailable());

echo $chain->handle($request);
```

Elke schakel doet één controle (Single Responsibility), en je bouwt de ketting op zoals je wilt. Voor een andere volgorde bouw je een andere ketting met dezelfde schakels.

**In Laravel:** middleware. Een request gaat langs een reeks middleware classes (bijvoorbeeld authenticatie en CSRF), en elke class kan het request afhandelen of doorgeven met `$next($request)`.

Zie `chain_voor.php` en `chain_na.php`.

## Command

Je maakt van een actie een object. Daardoor kun je de actie bewaren, uitstellen of ongedaan maken.

Zonder het pattern worden acties met een `switch` uitgevoerd:

```PHP
function runAction(string $action, string $title, array &$books): void
{
    switch ($action) {
        case 'add':
            $books[] = $title;
            break;
        case 'remove':
            $books = array_values(array_diff($books, [$title]));
            break;
    }
}
```

We hebben niet bewaard wat er is gedaan, dus ongedaan maken is lastig, en elke nieuwe actie betekent de `switch` uitbreiden. Met Command is elke actie een object met `execute()` en `undo()`:

```PHP
interface Command
{
    public function execute(): void;

    public function undo(): void;
}

class AddBook implements Command { ... }

class RemoveBook implements Command { ... }

class History
{
    private array $commands = [];

    public function run(Command $command): void
    {
        $command->execute();
        $this->commands[] = $command;
    }

    public function undoLast(): void
    {
        $command = array_pop($this->commands);
        $command?->undo();
    }
}
```

Elke actie weet zelf hoe hij moet worden uitgevoerd en teruggedraaid, en `History` onthoudt wat er is gebeurd. Een nieuwe actie is een nieuwe class.

**In Laravel:** Artisan commands (`php artisan ...`) zijn classes met een `handle()` method die de actie uitvoert, en queued jobs ook: een job is een object dat nu wordt gemaakt en later wordt uitgevoerd.

Zie `command_voor.php` en `command_na.php`.

## Template Method

Een class legt de volgorde van de stappen vast en laat subclasses bepalen hoe sommige stappen werken.

Twee soorten rapporten hebben dezelfde stappen (kop, regels, voet), maar een andere uitvoer. Zonder het pattern staat die volgorde in beide classes:

```PHP
class TextReport
{
    public function render(array $books): string
    {
        $output = "== Catalogus ==\n";

        foreach ($books as $book) {
            $output .= "- $book\n";
        }

        $output .= 'Totaal: ' . count($books) . "\n";

        return $output;
    }
}

class HtmlReport { /* dezelfde stappen met andere tekst */ }
```

Past de volgorde aan, dan moeten we dat in beide classes doen. Met Template Method staat de volgorde op één plek:

```PHP
abstract class Report
{
    final public function render(array $books): string
    {
        $output = $this->header();

        foreach ($books as $book) {
            $output .= $this->line($book);
        }

        return $output . $this->footer(count($books));
    }

    abstract protected function header(): string;

    abstract protected function line(string $book): string;

    abstract protected function footer(int $total): string;
}

class TextReport extends Report { /* alleen header(), line() en footer() */ }
```

`render()` is de template: de volgorde staat vast, en `final` voorkomt dat een subclass hem verandert. De stappen die verschillen, vult elke subclass zelf in. Dit is een vorm van overerving die je al kent uit les 08, maar nu met een duidelijke reden: de basis class bepaalt het verloop, de subclasses de details.

**In Laravel:** een Form Request. Het framework roept `authorize()` en `rules()` aan in een vaste volgorde, jij vult alleen die methods in. Ook een Artisan command werkt zo: het framework bepaalt hoe het wordt gestart, jij schrijft `handle()`.

Zie `template_voor.php` en `template_na.php`.

## State

Een object gedraagt zich anders afhankelijk van zijn toestand. Elke toestand is een eigen class die weet wat er in die toestand mag.

Een uitlening kan actief, te laat of ingeleverd zijn. Zonder het pattern controleert elke method de status:

```PHP
class Loan
{
    private string $status = 'active';

    public function renew(): string
    {
        if ($this->status === 'active') {
            return 'Verlengd met 14 dagen';
        } elseif ($this->status === 'overdue') {
            return 'Verlengen kan niet: eerst de boete betalen';
        } elseif ($this->status === 'returned') {
            return 'Verlengen kan niet: het boek is al ingeleverd';
        }

        throw new LogicException('Onbekende status');
    }

    // returnBook() en markOverdue() hebben dezelfde if's
}
```

Een vierde status betekent dat elke method moet worden aangepast, en de regels per status zijn verspreid over de class. Met State is elke status een class:

```PHP
interface LoanState
{
    public function renew(): string;

    public function returnBook(Loan $loan): string;

    public function markOverdue(Loan $loan): void;
}

class ActiveState implements LoanState { ... }

class OverdueState implements LoanState { ... }

class ReturnedState implements LoanState { ... }

class Loan
{
    private LoanState $state;

    public function renew(): string
    {
        return $this->state->renew();
    }
}
```

`Loan` geeft het werk door aan zijn huidige status, en er staat geen `if` op de status meer in. De status verandert zichzelf via `$loan->changeState(...)`. Een vierde status is een nieuwe class.

Dit lijkt op Strategy: ook hier wissel je een object met dezelfde interface. Het verschil zit in wie wisselt. Bij Strategy kiest degene die de class gebruikt de strategie. Bij State verandert het object zijn eigen toestand, op basis van wat er gebeurt.

**In Laravel:** er is geen vast onderdeel voor, maar je ziet het in de praktijk vaak in een model met een `status` kolom, bijvoorbeeld een bestelling die van `pending` naar `paid` naar `shipped` gaat. Zodra er per status veel `if`'s komen, is State een oplossing.

Zie `state_voor.php` en `state_na.php`.

## Er zijn er nog meer

De 23 klassieke patterns zijn nog niet allemaal aan bod gekomen. Je kunt de rest opzoeken op [refactoring.guru](https://refactoring.guru/design-patterns/catalog), waar elk pattern met voorbeelden wordt uitgelegd. Het is niet de bedoeling dat je ze allemaal uit je hoofd kent. Het gaat erom dat je het probleem herkent en weet dat er een naam en een oplossing voor bestaat.

## Samengevat

| Pattern | Probleem dat je herkent | In Laravel |
|---|---|---|
| Builder | Een constructor met veel parameters, of een object dat stap voor stap wordt opgebouwd | Query Builder |
| Chain of Responsibility | Meerdere controles of bewerkingen na elkaar, in een vaste volgorde | Middleware |
| Command | Een actie moet worden bewaard, uitgesteld of ongedaan gemaakt | Artisan commands, queued jobs |
| Template Method | Meerdere classes met dezelfde stappen maar andere details | Form Request, Artisan command |
| State | Een object gedraagt zich anders per status, met `if`'s in elke method | Een `status` kolom op een model |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/patterns/` en je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [builder_voor.php](./example/patterns/builder_voor.php) en [builder_na.php](./example/patterns/builder_na.php) maken een uitlening aan, eerst met een lange constructor en daarna met een builder.
2. [chain_voor.php](./example/patterns/chain_voor.php) en [chain_na.php](./example/patterns/chain_na.php) controleren een uitleenverzoek, eerst in één functie en daarna met een ketting van controles.
3. [command_voor.php](./example/patterns/command_voor.php) en [command_na.php](./example/patterns/command_na.php) beheren een catalogus, eerst met een `switch` en daarna met commands die ongedaan gemaakt kunnen worden. De uitvoer van `command_na.php` is langer, omdat daar `undo` wordt gebruikt.
4. [template_voor.php](./example/patterns/template_voor.php) en [template_na.php](./example/patterns/template_na.php) maken rapporten, eerst in twee losse classes en daarna met een gemeenschappelijke basis class.
5. [state_voor.php](./example/patterns/state_voor.php) en [state_na.php](./example/patterns/state_na.php) beheren de status van een uitlening, eerst met `if`'s op een string en daarna met state classes.

## Opgaven

De opgaven staan in [patterns_opgaven_3.md](./patterns_opgaven_3.md).
