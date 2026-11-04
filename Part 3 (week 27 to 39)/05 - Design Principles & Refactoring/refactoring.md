# Refactoring

## Bestaande code verbeteren

In de vorige lessen hebben we gekeken naar principes: KISS, DRY, SOLID, GRASP. Maar wanneer pas je ze toe? Zelden op een leeg bestand. Meestal zit je met code die al werkt, maar waar je moeilijk iets aan kunt veranderen. Refactoren is het verbeteren van de structuur van code, zonder dat het gedrag verandert.

Dat laatste is belangrijk. Tijdens een refactoring voeg je geen functionaliteit toe en repareer je ook geen bugs. Verandert het gedrag, dan weet je niet meer of een fout uit je refactoring komt of uit de code die er al stond.

Dat betekent ook dat je een manier nodig hebt om te controleren dat het gedrag gelijk blijft. In Part 1 hebben we PHPUnit gezien (les 05). Een eenvoudiger alternatief is een script dat de uitvoer vergelijkt met wat de code eerder teruggaf, zoals we straks doen.

## De werkwijze

1. Zorg dat je kunt controleren dat het gedrag gelijk blijft.
2. Kies één smell. Een smell is een signaal dat er iets mis kan zijn, zoals de rode vlaggen uit de tabellen in les 01 t/m 04.
3. Maak één kleine wijziging die die smell oplost.
4. Controleer opnieuw.
5. Commit, en begin bij stap 2.

Dus niet alles tegelijk omgooien. Een kleine stap die mislukt is makkelijk terug te draaien, een grote niet.

Twee nieuwe principes helpen je bij het herkennen van smells: de Law of Demeter en Tell, Don't Ask.

## Law of Demeter

Praat alleen met je directe buren. Een method mag alleen iets aanroepen van zichzelf, van zijn parameters, van objecten die hij zelf aanmaakt en van zijn eigen properties. Niet van de objecten die je daaruit terugkrijgt.

Je herkent het aan een keten van aanroepen:

```PHP
$max = $member->getOrganisation()->getMaxLoans();
```

Deze regel kent `Member`, `Organisation` en weet hoe die aan elkaar vastzitten. Je ziet hetzelfde in `bad/FileController.php`, waar `$user->organisation->id` staat. Verandert de relatie tussen `Member` en `Organisation`, dan moet elke plek met zo'n keten mee veranderen.

De oplossing is dat `Member` zelf zegt wat zijn maximum is:

```PHP
class Member
{
    public function getMaxLoans(): int
    {
        return $this->organisation->getMaxLoans();
    }
}

$max = $member->getMaxLoans();
```

Dit is dezelfde oplossing als bij Low Coupling in les 04. Hier is het de regel erachter.

Niet elke keten is een overtreding. Een *fluent interface* (waar elke method hetzelfde object teruggeeft, zoals bij Laravel `Model::where()->orderBy()->get()`) praat nog steeds met één object. Hetzelfde geldt voor collecties waar je methods aan elkaar koppelt. Het gaat om ketens die van het ene object naar het volgende doorlopen.

## Tell, Don't Ask

Vraag een object niet om zijn gegevens om er zelf een beslissing mee te nemen. Geef het object de opdracht.

```PHP
if ($member->getLoanCount() >= $max) {
    echo "...";
    return;
}

$member->setLoanCount($member->getLoanCount() + 1);
```

De functie vraagt het aantal uitleningen op, vergelijkt het zelf met het maximum en zet het zelf weer terug. De regel "een lid mag maximaal zoveel boeken lenen" staat dus in de functie, terwijl `Member` alle gegevens heeft. Een andere functie die ook uitleent, moet de regel kopiëren.

Door het object te laten beslissen, staat de regel op één plek:

```PHP
class Member
{
    public function canBorrow(): bool
    {
        return $this->loanCount < $this->getMaxLoans();
    }

    public function borrow(): void
    {
        $this->loanCount++;
    }
}

if (!$member->canBorrow()) { ... }

$member->borrow();
```

Er is ook geen `setLoanCount()` meer nodig. Niemand van buiten kan het aantal nog op een willekeurige waarde zetten.

Dit is Information Expert uit les 04 vanuit een andere kant bekeken. In het `better` voorbeeld is dit ook gedaan: in plaats van `$user->getUploadLimit() < $total + $user->getTotalUploadSize()` in de controller staat er `$user->canUpload($size)` in het `User` model.

## Refactoren in stappen

Hier is een script waarin een boek wordt uitgeleend. Het werkt, maar er zitten een aantal smells in. Zie `lend_0_start.php`:

```PHP
function lendBook(Member $member, Book $book): void
{
    // smell 1: een keten van aanroepen
    $max = $member->getOrganisation()->getMaxLoans();

    // smell 2: we vragen gegevens op en beslissen zelf
    if ($member->getLoanCount() >= $max) {
        // smell 3: de beslissing en het tonen van de tekst zitten door elkaar
        echo $member->getName() . " mag niet meer dan $max boeken lenen\n";
        return;
    }

    $member->setLoanCount($member->getLoanCount() + 1);
    echo $book->getTitle() . ' is uitgeleend aan ' . $member->getName() . "\n";
}

// smell 4: hetzelfde blok staat meerdere keren onder elkaar
lendBook($anna, new Book('Misery'));
lendBook($anna, new Book('It'));
lendBook($anna, new Book('Carrie'));
```

De uitvoer is:

```
Misery is uitgeleend aan Anna
It is uitgeleend aan Anna
Anna mag niet meer dan 2 boeken lenen
```

We lossen de smells één voor één op. Elke stap staat in een eigen bestand en geeft precies dezelfde uitvoer.

1. [lend_1_demeter.php](./example/refactoring/lend_1_demeter.php): `Member` krijgt `getMaxLoans()`, zodat de functie `Organisation` niet meer hoeft te kennen.
2. [lend_2_tell_dont_ask.php](./example/refactoring/lend_2_tell_dont_ask.php): `Member` krijgt `canBorrow()` en `borrow()`. `getLoanCount()` en `setLoanCount()` zijn weg.
3. [lend_3_separation.php](./example/refactoring/lend_3_separation.php): `lendBook()` geeft alleen nog terug of het is gelukt. Wat de gebruiker te zien krijgt, bepaalt degene die de functie aanroept (Separation of Concerns).
4. [lend_4_dry.php](./example/refactoring/lend_4_dry.php): het herhaalde blok is vervangen door een loop.

Hoe weten we dat de uitvoer na elke stap hetzelfde is? Met [check.php](./example/refactoring/check.php). Dat script voert het beginpunt en elke stap uit en vergelijkt de uitvoer:

```
ok   lend_1_demeter.php
ok   lend_2_tell_dont_ask.php
ok   lend_3_separation.php
ok   lend_4_dry.php
```

Verander je in een stap per ongeluk iets aan de tekst, dan staat er `FOUT` bij die stap. Dit is dus de "controle" uit stap 1 van de werkwijze, en je ziet dat je hem bij elke stap opnieuw draait.

## Een grotere case: de FileController

Het `bad` voorbeeld is een controller waar alles in zit. Uitgewerkt staat het in [First steps](./01%20-%20First%20steps/README.md), hier vind je de samenvatting. In `bad/FileController.php` staan zeven fouten, elk gemarkeerd als `MISTAKE`. Ze zijn allemaal te koppelen aan wat we hebben geleerd:

| In `bad/FileController.php` | Principe |
|---|---|
| Mistake 1: onnodig ingewikkeld ophalen van de application | KISS (les 01) |
| Mistake 2: autorisatie in de controller | Controller (les 04) en Separation of Concerns (les 01) |
| Mistake 3: dezelfde validatieregels als in de Form Request | DRY (les 01) |
| Mistake 4: de uploadlimiet controleren in de controller, met losse getters van `User` | Information Expert (les 04) en Tell, Don't Ask |
| Mistake 5: bedrijfslogica in de controller | Single Responsibility (les 03) en Controller (les 04) |
| Mistake 6 en 7: een query per bestand in een loop | Niet onnodig herhalen in een loop (de checklist uit Part 1) |

En de Law of Demeter staat er ook in: `$user->organisation->id`.

Het resultaat is een controller van een paar regels. De regels zijn verhuisd naar de plek die erbij hoort: de `StoreFileRequest`, de `AnswerService`, het `User` model en het `FileSize` value object.

## Wanneer refactor je niet?

- **Zonder controle.** Heb je geen test of vergelijkbaar script en kun je er niet een maken, dan is de kans groot dat je iets stukmaakt zonder het te merken. Schrijf dan eerst een test voor het gedrag zoals het nu is, ook als dat gedrag niet perfect is.
- **Vlak voor een deadline.** Een refactoring levert niets op voor de gebruiker. Schuif het niet voor je uit, maar doe het niet als er geen tijd is om het af te maken.
- **Code die niet meer verandert.** Is een stuk code lelijk maar werkt het en komt niemand er meer aan, dan levert verbeteren weinig op.

Een bekende vuistregel is de *boy scout rule*: laat de code iets beter achter dan je hem hebt gevonden. Je hoeft niet alles op te ruimen, één kleine verbetering per keer is genoeg.

## Samengevat

| Onderwerp | Vraag die je jezelf stelt | Rode vlag in de code |
|---|---|---|
| Werkwijze | Kan ik controleren dat het gedrag gelijk blijft? | Een refactoring zonder test of controle |
| Law of Demeter | Praat ik alleen met mijn directe buren? | `$a->getB()->getC()->getD()` |
| Tell, Don't Ask | Beslis ik iets wat het object zelf kan beslissen? | Een `get`, een `if` en een `set` direct achter elkaar |
| Stappen | Is dit één kleine wijziging? | Een commit waarin je van alles tegelijk hebt aangepast |

## Voorbeeldbestanden

Alle voorbeelden staan in `example/refactoring/`. Je kunt ze uitvoeren met `php bestandsnaam.php` vanuit die map.

1. [lend_0_start.php](./example/refactoring/lend_0_start.php) is het beginpunt met de smells.
2. [lend_1_demeter.php](./example/refactoring/lend_1_demeter.php) tot en met [lend_4_dry.php](./example/refactoring/lend_4_dry.php) zijn de vier stappen.
3. [check.php](./example/refactoring/check.php) controleert dat elke stap dezelfde uitvoer geeft als het beginpunt.

De Laravel voorbeelden staan in [bad](../example/bad/app) en [better](../example/better/app).

## Opgaven

De opgaven staan in [refactoring_opgaven_1.md](./refactoring_opgaven_1.md).
