# Stack, heap en de levensduur van objecten

## Vorige keer

Gisteren hebben we classes behandeld. Met `new BankAccount()` maak je een object en dat object heeft zijn eigen data. Vandaag kijken we wat er eigenlijk gebeurt als je zo'n object aan een functie meegeeft, en waar variabelen en objecten in het geheugen leven.

## Een gewone variabele geef je door als kopie

Eerst een functie met een gewone `int`:

```PHP
function addBonus(int $balance): int
{
    $balance = $balance + 50;
    echo "In de functie: $balance\n";

    return $balance;
}

$balance = 100;
addBonus($balance);

echo "Buiten de functie: $balance\n";
```

De uitvoer is:

```
In de functie: 150
Buiten de functie: 100
```

Let op: `$balance` buiten de functie is nog steeds 100. De functie heeft een **kopie** van de waarde gekregen. De parameter `$balance` en de variabele `$balance` hebben dezelfde naam, maar het zijn twee losse variabelen. Wil je het nieuwe saldo gebruiken, dan moet je de return waarde opslaan: `$balance = addBonus($balance);`.

Zie `pass_by_value.php`.

## Een object werkt anders

Dezelfde functie, maar nu met een `BankAccount` object:

```PHP
function addBonus(BankAccount $account): void
{
    $account->deposit(50);
    echo "In de functie: " . $account->getBalance() . "\n";
}

$account = new BankAccount(100);
addBonus($account);

echo "Buiten de functie: " . $account->getBalance() . "\n";
```

```
In de functie: 150
Buiten de functie: 150
```

Nu is het saldo buiten de functie **wel** veranderd. De functie heeft het object zelf aangepast, niet een kopie.

Let op: dit betekent niet dat een object "by reference" wordt doorgegeven. Kijk wat er gebeurt als we in de functie een nieuw object toewijzen:

```PHP
function replaceAccount(BankAccount $account): void
{
    $account = new BankAccount(9999);
    echo "In de functie: " . $account->getBalance() . "\n";
}

replaceAccount($account);
echo "Buiten de functie: " . $account->getBalance() . "\n";
```

```
In de functie: 9999
Buiten de functie: 150
```

Het originele object is niet vervangen. Wat er wel gekopieerd wordt is de **verwijzing** naar het object. De functie krijgt dus een eigen verwijzing, maar die wijst naar hetzelfde object. Passen we het object aan via die verwijzing, dan zien we de aanpassing overal. Wijzen we de verwijzing naar een nieuw object, dan verandert alleen de verwijzing in de functie.

Zie `pass_object.php`.

## Het gevolg: een object dat je niet wilde veranderen

Dit wordt een probleem als je ervan uitgaat dat het object onveranderd blijft. Stel we schrijven een functie die alleen wil laten zien wat het saldo zou zijn na een opname:

```PHP
function showBalanceAfterWithdraw(BankAccount $account, int $amount): void
{
    $account->withdraw($amount);
    echo "Saldo na opname: " . $account->getBalance() . "\n";
}

$account = new BankAccount(100);
showBalanceAfterWithdraw($account, 80);

echo "Saldo van de rekening: " . $account->getBalance() . "\n";
```

```
Saldo na opname: 20
Saldo van de rekening: 20
```

We verwachtten 100, maar de opname is echt uitgevoerd. Dit soort fouten is lastig te vinden: de functie lijkt "alleen te laten zien", maar het object is stiekem aangepast.

Hetzelfde gebeurt met een toewijzing:

```PHP
$account = new BankAccount(100);
$backup = $account;

$account->withdraw(30);

echo $backup->getBalance(); // 70
```

`$backup` is geen back-up. Er is nog steeds maar één object en twee variabelen die ernaar wijzen.

Let op: `private` helpt hier niet. Gisteren beschermden we de data tegen directe toegang van buiten de class. Maar `withdraw()` is een `public` method en mag het saldo gewoon aanpassen. Het probleem is niet dat iemand bij de data kan, maar dat we niet door hadden dat we hetzelfde object gebruikten.

Zie `shared_object.php`.

### Oplossing: clone

Wil je echt met een kopie werken, dan maak je die zelf met `clone`:

```PHP
function showBalanceAfterWithdraw(BankAccount $account, int $amount): void
{
    $copy = clone $account;
    $copy->withdraw($amount);
    echo "Saldo na opname: " . $copy->getBalance() . "\n";
}
```

```
Saldo na opname: 20
Saldo van de rekening: 100
```

`clone` maakt een nieuw object met dezelfde waarden. De functie werkt nu op de kopie en het origineel blijft gelijk. Met `===` kun je controleren of twee variabelen naar hetzelfde object wijzen.

Let op: `clone` kopieert alleen de properties van het object zelf. Zit er in een property weer een object, dan wordt dat object nog steeds gedeeld. Dat laten we voor nu zo.

Zie `clone.php`.

## Waarom is dit zo? De stack

Om te begrijpen waarom een `int` en een object zich anders gedragen, moeten we kijken hoe PHP het geheugen gebruikt. Er zijn twee plekken: de **stack** en de **heap**. We beginnen met de stack.

Elke keer dat je een functie aanroept, maakt PHP een **stack frame**. Dat is een stukje geheugen voor die ene aanroep, met daarin de parameters en de variabelen die je in de functie maakt. Het frame van de functie die het laatst is aangeroepen ligt bovenop de stapel.

```PHP
function calculateTotal(int $price): int
{
    $tax = addTax($price);
    return $price + $tax;
}

function addTax(int $price): int
{
    return intdiv($price * 21, 100);
}

$price = 100;
$total = calculateTotal($price);
```

Zo bouwt de stack zich op:

```
1. main start           2. calculateTotal       3. addTax
                        wordt aangeroepen       wordt aangeroepen

                                                | addTax           |
                                                |   price = 100    |
                        | calculateTotal   |    | calculateTotal   |
                        |   price = 100    |    |   price = 100    |
                        |   tax            |    |   tax            |
| main             |    | main             |    | main             |
|   price = 100    |    |   price = 100    |    |   price = 100    |
|   total          |    |   total          |    |   total          |
```

- Een frame dat erbij komt noemen we een **push**. Een frame dat eraf gaat noemen we een **pop**.
- Zodra `addTax` klaar is met een `return`, wordt zijn frame gepopt. De variabele `price` van `addTax` is dan weg uit het geheugen. Daarna draait `calculateTotal` verder, en als die klaar is verdwijnen ook zijn variabelen `price` en `tax`.
- Dit verklaart de kopie bij de `int`: de parameter `$price` van `addTax` is een nieuwe variabele in een nieuw frame. Er wordt een waarde in gekopieerd. Past de functie die variabele aan, dan verandert alleen de variabele in zijn eigen frame.

Zie `stack_frames.php`. Daar laat `debug_print_backtrace()` de echte stack zien op het moment dat je hem aanroept.

Let op: dit is een vereenvoudigd model. Intern gaat PHP iets anders met het geheugen om, maar voor het begrijpen van functies en objecten is dit model precies wat je nodig hebt.

## De heap

Een object past niet zomaar in een frame. Het moet langer kunnen leven dan één functie-aanroep. Daarom staan objecten op de **heap**. De heap is een grote plek in het geheugen waar objecten leven, los van de frames op de stack.

Op de stack staat dan alleen een variabele met een **verwijzing** naar het object:

```
Stack                      Heap

| main            |
|   $account  ----+------> BankAccount
|                 |          balance = 100
```

Geef je `$account` aan een functie, dan wordt de verwijzing gekopieerd. Het object zelf niet:

```
Stack                      Heap

| addBonus        |
|   $account  ----+--+
| main            |  +---> BankAccount
|   $account  ----+--+      balance = 100
```

Twee variabelen op de stack, één object op de heap. Dat is precies wat we in `pass_object.php` zagen. Het verklaart ook `$backup = $account`: we kopiëren alleen de verwijzing. Met `clone` maak je wel een nieuw object op de heap:

```
Stack                      Heap

| main            |
|   $account  ----+------> BankAccount (balance = 70)
|   $backup   ----+------> BankAccount (balance = 100)
```

| | Stack | Heap |
|---|---|---|
| Wat staat erin | Parameters en lokale variabelen van een functie-aanroep, en verwijzingen naar objecten | Objecten |
| Levensduur | Tot de functie klaar is (frame wordt gepopt) | Zolang er nog iets naar het object wijst |
| Opruimen | Automatisch bij het `return` | PHP ruimt het object op als er niets meer naar wijst |

### De levensduur van een object

Een object verdwijnt dus niet als de functie klaar is, maar pas als er geen enkele verwijzing meer naar is. Met `__destruct()` zie je precies wanneer dat gebeurt. Dit is de destructor, de tegenhanger van de constructor:

```PHP
public function __destruct()
{
    echo "rekening van {$this->owner} is opgeruimd\n";
}
```

In `object_lifetime.php` zie je drie situaties:

1. Een object dat alleen in een functie leeft: zodra het frame gepopt wordt, wijst er niets meer naar het object en wordt het opgeruimd.
2. Een object dat je uit de functie teruggeeft: de variabele in het frame verdwijnt, maar de verwijzing leeft door in `$kept`. Het object blijft bestaan.
3. `$kept = null`: de laatste verwijzing is weg, dus het object wordt opgeruimd.

Een object dat nergens meer naar wijst, kun je niet meer bereiken. PHP ruimt het daarom zelf op. Daar hoef je als programmeur niets voor te doen.

## PHP is request based

Tot nu toe draaiden onze scripts één keer van boven naar beneden. Bij een website werkt het hetzelfde: elke keer dat een bezoeker een pagina opent, start PHP het script opnieuw. Dit noemen we een **request**.

Dat heeft een grote invloed op de levensduur van een object: **aan het einde van het request verdwijnt alles**, zowel de stack als de heap. Het script is klaar, PHP ruimt alle objecten op en het volgende request begint met een lege situatie.

```PHP
$counter = new VisitCounter();
$counter->increment();

echo $counter->getCount();
```

Start `php -S localhost:8090` vanuit de map `example/stack_and_heap` en open http://localhost:8090/request_counter.php. Hoe vaak je de pagina ook herlaadt, het object geeft steeds `1`. Bij elk request wordt een nieuw object gemaakt en het object van het vorige request bestaat niet meer.

In dezelfde pagina staat ook een waarde in `$_SESSION`. Die telt wel door. Dat komt doordat PHP de sessiedata buiten het script bewaart, op de server. Dit is meteen de reden dat we sessions en databases gebruiken: alles wat langer moet bestaan dan één request, moet je ergens opslaan buiten de objecten in het geheugen.

Een script dat je in de terminal draait is hetzelfde: het is één "request" dat eindigt als het script klaar is.

Zie `request_counter.php`.

## Recursie

Een recursieve functie is een functie die zichzelf aanroept. Omdat elke aanroep een eigen frame krijgt, kun je recursie goed begrijpen met de stack.

```PHP
function countdown(int $number): void
{
    echo "push: countdown($number)\n";

    if ($number === 0) {
        return;
    }

    countdown($number - 1);

    echo "pop:  countdown($number)\n";
}

countdown(3);
```

```
push: countdown(3)
  push: countdown(2)
    push: countdown(1)
      push: countdown(0)
    pop:  countdown(1)
  pop:  countdown(2)
pop:  countdown(3)
```

Let op de volgorde: eerst bouwt de stack op tot `countdown(0)`, en pas daarna worden de frames één voor één gepopt. `countdown(3)` is pas klaar als `countdown(2)` klaar is, enzovoort.

Er zijn twee onderdelen die elke recursieve functie nodig heeft:

- Een **base case**: de situatie waarin de functie zichzelf niet meer aanroept. Hier stopt de recursie (`if ($number === 0) return;`).
- Een **recursieve stap** die dichter bij de base case komt (`$number - 1`).

Bij `factorial()` gebruiken we de return waarde van de diepere aanroep. `factorial(4)` wacht op `factorial(3)`, die wacht op `factorial(2)`, die wacht op `factorial(1)`. Pas als die `1` teruggeeft, kunnen de frames één voor één hun resultaat berekenen:

```
push: factorial(4)
push: factorial(3)
push: factorial(2)
push: factorial(1)
pop:  factorial(1) geeft 1
pop:  factorial(2) geeft 2
pop:  factorial(3) geeft 6
pop:  factorial(4) geeft 24
```

Zie `recursion.php`.

### Zonder base case

Wat gebeurt er als de base case ontbreekt?

```PHP
function countdown(int $number): void
{
    echo "countdown($number)\n";

    countdown($number - 1);
}

countdown(3);
```

Elke aanroep maakt een nieuw frame, maar geen enkel frame wordt ooit gepopt, omdat er nooit een `return` komt. De stack blijft dus groeien tot er een grens bereikt wordt. Wat je precies ziet hangt af van je instellingen. Dit gebeurde bij ons bij het uitvoeren van `infinite_recursion.php`:

- **Met Xdebug aan** stopt het script met `Xdebug has detected a possible infinite loop` na 512 frames. De devcontainer heeft ook Xdebug, maar de melding kan per versie anders zijn.
- **Zonder Xdebug** blijft het doorgaan tot het geheugen vol zit: `Allowed memory size exhausted`. Dat gebeurde bij ongeveer 330.000 frames met een geheugenlimiet van 64 MB.

In beide gevallen stopt het script abrupt. Een base case voorkomt dit. Controleer ook altijd dat de recursieve stap echt richting die base case gaat.

Zie `infinite_recursion.php`.

## Samengevat

| Wat | Waar | Wat gebeurt er bij een functie-aanroep | Wanneer verdwijnt het |
|---|---|---|---|
| Gewone variabele (`int`, `string`, ...) | Stack | De waarde wordt gekopieerd | Als het frame wordt gepopt |
| Object | Heap | De verwijzing wordt gekopieerd, het object wordt gedeeld | Als er geen verwijzing meer is, of aan het einde van het request |
| Kopie van een object | Heap | Alleen met `clone` | Zelfde als hierboven |

- Een functie die een object aanpast, past het object van de aanroeper aan. Wil je dat niet, gebruik dan `clone`.
- Elke functie-aanroep is een nieuw frame op de stack. Recursie zonder base case laat de stack groeien tot PHP stopt.
- PHP is request based: aan het einde van elk request verdwijnen alle variabelen en objecten.

## Voorbeeldbestanden

De voorbeelden bouwen op elkaar voort en gebruiken de bankrekening van gisteren. Je kunt ze uitvoeren met `php bestandsnaam.php` vanuit de map `example/stack_and_heap`.

1. [pass_by_value.php](./example/stack_and_heap/pass_by_value.php) laat zien dat een `int` als kopie aan een functie wordt meegegeven.
2. [pass_object.php](./example/stack_and_heap/pass_object.php) laat zien dat dezelfde functie met een object het origineel aanpast, en dat een nieuw object in de functie het origineel niet vervangt.
3. [shared_object.php](./example/stack_and_heap/shared_object.php) laat het gevolg zien als je ervan uitgaat dat een object onveranderd blijft.
4. [clone.php](./example/stack_and_heap/clone.php) lost dit op met `clone`.
5. [stack_frames.php](./example/stack_and_heap/stack_frames.php) toont push en pop van stack frames en de stack met `debug_print_backtrace()`.
6. [object_lifetime.php](./example/stack_and_heap/object_lifetime.php) laat met `__destruct()` zien wanneer een object op de heap wordt opgeruimd.
7. [request_counter.php](./example/stack_and_heap/request_counter.php) laat zien dat een object niet blijft bestaan tussen twee requests, in tegenstelling tot een sessie.
8. [recursion.php](./example/stack_and_heap/recursion.php) toont de stack bij een recursieve functie.
9. [infinite_recursion.php](./example/stack_and_heap/infinite_recursion.php) geeft met opzet een fout omdat de base case ontbreekt.

## Labs

In plaats van opgaven zijn er interactieve labs. Bij Lab 1 en Lab 4 voorspel je eerst wat er gebeurt en controleer je dat daarna. Lab 2 en Lab 3 laten het programma stap voor stap zien. De labs zijn gewone webpagina's, je hoeft niets te installeren: open [index.html](./example/stack_and_heap/lab/index.html) in je browser. Werkt de opmaak niet, start dan `php -S localhost:8090` in de map `example/stack_and_heap/lab`.

1. [copy_or_original.html](./example/stack_and_heap/lab/copy_or_original.html) is Lab 1. Vijf scenario's over kopie of origineel: een `int`, een object, een nieuw object in de functie, een tweede variabele en `clone`. Onder de code staat wat de constructor en de methods van `BankAccount` doen, en na elk antwoord zie je welke variabelen naar welk object op de heap wijzen. Met `php verify_lab1.php` controleer je dat de antwoorden in de pagina kloppen met wat PHP echt doet.
2. [step_debugger.html](./example/stack_and_heap/lab/step_debugger.html) is Lab 2. Een step debugger die dezelfde scenario's regel voor regel uitvoert, plus `factorial(3)` voor recursie. Je kunt het automatisch laten afspelen of zelf stap voor stap vooruit en terug gaan. Links zie je de code met de regel die nu wordt uitgevoerd (ook binnen de methods van `BankAccount`), rechts de stack met zijn frames en de heap met de objecten. Pijlen laten zien welke variabele naar welk object wijst. Onder de stack en de heap zie je ook de uitvoer van het script.
3. [lifetime_and_request.html](./example/stack_and_heap/lab/lifetime_and_request.html) is Lab 3. Dezelfde step debugger, nu voor de levensduur van objecten. Met `__destruct()` zie je precies wanneer een object van de heap verdwijnt: aan het einde van een functie, na `$kept = null` en aan het einde van het script. Het laatste scenario laat twee requests na elkaar zien: het object verdwijnt aan het einde van elk request, de sessie niet.
4. [bughunt.html](./example/stack_and_heap/lab/bughunt.html) is Lab 4. Drie scripts die iets anders doen dan bedoeld: een functie die het originele object aanpast, twee variabelen die hetzelfde object delen en een recursieve functie zonder base case. Je beantwoordt eerst een vraag, volgt daarna het programma stap voor stap en bekijkt met de knop Gecorrigeerd de oplossing. Met `php verify_lab4.php` controleer je dat de uitvoer in de pagina klopt met wat PHP echt doet.

## Diagrammen

De schema's uit deze les staan als draw.io bestanden in de map `diagrams`: [stack_frames.drawio](./diagrams/stack_frames.drawio), [stack_and_heap.drawio](./diagrams/stack_and_heap.drawio), [shared_object_and_clone.drawio](./diagrams/shared_object_and_clone.drawio) en [recursion.drawio](./diagrams/recursion.drawio).
