In Python wordt een functie als volgt gedeclareerd:
```Python
def add(a, b):
    return a + b
```

Zoals we bij loopjes en if statements hebben gezien is de syntax in PHP net iets anders:
```PHP
function add($a, $b)
{
    return $a + $b;
}
```

Let op het verschil in syntax. In PHP gebruiken we `function` voor de declaratie in plaats van `def`. En maken we gebruik van `{` en `}` om de functie te openen en te sluiten.

## Parameters en argumenten

Een functie kan informatie nodig hebben om zijn werk te kunnen doen. Deze informatie geven we mee met parameters.
De namen tussen de haakjes bij de functie declaratie zijn de parameters:

```PHP
function add($firstNumber, $secondNumber)
{
    return $firstNumber + $secondNumber;
}
```

Wanneer we de functie gebruiken geven we de waarden mee. Deze waarden noemen we argumenten:

```PHP
$result = add(5, 10);
```

In dit voorbeeld zijn `$firstNumber` en `$secondNumber` de parameters. De getallen `5` en `10` zijn de argumenten.

## Een waarde teruggeven

Met `return` geeft een functie een waarde terug aan de code die de functie heeft aangeroepen. Die waarde kunnen we bijvoorbeeld opslaan in een variabele:

```PHP
$result = add(5, 10);
echo $result;
```

`echo` toont iets op het scherm. `return` geeft een waarde terug. Dit is dus niet hetzelfde. Een functie die zelf iets toont met `echo` geeft niet automatisch ook een waarde terug aan de code die de functie heeft aangeroepen.

## Variabelen binnen en buiten functies

Variabelen die buiten een functie zijn aangemaakt zijn niet vanzelf beschikbaar binnen die functie. Een functie krijgt informatie mee via parameters. Dit maakt duidelijk welke informatie een functie nodig heeft:

```PHP
function printName($name)
{
    echo $name;
}

$firstName = "John";
printName($firstName);
```

Met `global` kun je een variabele van buiten de functie beschikbaar maken. Dit kan, maar maakt het minder duidelijk welke informatie de functie nodig heeft. Gebruik daarom bij voorkeur parameters. In `function_scope.php` zie je wat er gebeurt wanneer een functie een variabele probeert te gebruiken die niet binnen de functie beschikbaar is.

## Een array meegeven aan een functie

Wanneer je meerdere gegevens bij elkaar hebt opgeslagen in een array, kun je de hele array als één argument meegeven:

```PHP
$person = [
    "firstName" => "John",
    "lastName" => "Doe"
];

function printShippingLabel($person)
{
    return $person["firstName"] . " " . $person["lastName"];
}

printShippingLabel($person);
```

In `function_array_parameter.php` wordt de array op deze manier meegegeven. Daar zie je ook een andere manier: met `...` kun je de waarden uit een array als losse argumenten meegeven aan een functie.

```PHP
printShippingLabelWithUseOfSpreadOperator(...$person2);
```

De keys van deze array moeten dan overeenkomen met de parameternamen van de functie. Deze manier heet argument unpacking en is wat lastiger. Voor de basis is het voldoende om te weten dat je een array ook gewoon als één argument aan een functie kunt meegeven.

## Types bij functies

Je kunt aangeven welk type een parameter moet hebben en welk type een functie teruggeeft:

```PHP
function add(int $firstNumber, int $secondNumber): int
{
    return $firstNumber + $secondNumber;
}
```

In dit voorbeeld geven de type declaraties aan dat de parameters en de return waarde het type `int` hebben. `: int` is het return type.

Met `declare(strict_types=1);` kun je in een bestand striktere typecontrole gebruiken voor functieaanroepen. Dit staat bovenaan in `strict_functions.php`. Het is een instelling voor het bestand en verandert niet hoe je de functie zelf aanroept.

In `strict_functions.php` zie je ook een parameter met `&`. Daarmee kan een functie de waarde van een variabele aanpassen. Dit is een extra onderwerp en voor de meeste functies is het duidelijker om een waarde terug te geven met `return`.

## Voorbeeldbestanden

De voorbeelden hieronder bouwen stap voor stap op. Begin met een eenvoudige functie en bekijk daarna hoe je informatie meegeeft, met scope omgaat en functies gebruikt in een groter programma.

1. [function.php](./example/function.php) laat zien hoe je herhaalde berekeningen met een functie kunt uitvoeren en met `return` een resultaat teruggeeft.
2. [function_scope.php](./example/function_scope.php) laat zien dat variabelen buiten een functie niet vanzelf binnen de functie beschikbaar zijn. Je ziet ook hoe je parameters meegeeft en wat `global` doet.
3. [function_array_parameter.php](./example/function_array_parameter.php) laat zien hoe je een array als parameter meegeeft. Ook wordt getoond hoe je met `...` de waarden uit een array als losse argumenten doorgeeft.
4. [strict_functions.php](./example/strict_functions.php) laat type declaraties en `declare(strict_types=1);` zien. Ook staat er een voorbeeld in van een parameter met `&`, waarmee een functie een variabele kan aanpassen.
5. [met_comments.php](./example/met_comments.php) is een procedurele versie van een raadspel. De codeblokken worden met comments toegelicht.
6. [zonder_comments.php](./example/zonder_comments.php) bevat hetzelfde raadspel, maar verdeelt de code over functies met beschrijvende namen.
7. [hangman_v2.php](./example/hangman_v2.php) is een uitgebreider voorbeeld waarin de onderdelen van Galgje over meerdere functies zijn verdeeld.

## Opgaven

Probeer de opgaven eerst zelf te maken. De voorbeelden hierboven helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

### Opgave 1: Hoogste temperatuur

Maak een functie `findHighestTemperature()` die een niet-lege array met gehele temperaturen meekrijgt en de hoogste temperatuur teruggeeft. Test de functie met minstens twee verschillende arrays.

**Hint:** Begin met de eerste temperatuur als hoogste. Loop daarna door de rest van de array en werk de hoogste temperatuur bij als je een hogere waarde vindt.

### Opgave 2: Geldige score

Maak een functie `isValidScore(int $score): bool` die teruggeeft of de score geldig is. Een geldige score ligt tussen 0 en 100, inclusief 0 en 100. Test ook een score lager dan 0 en een score hoger dan 100.

**Hint:** Gebruik `if` om de grenzen te controleren en laat de functie een `bool` teruggeven.

### Opgave 3: Beschikbare producten

Maak een functie `getAvailableProducts()` die een array met producten meekrijgt. Elk product is een associatieve array met de keys `"name"` en `"stock"`. Geef een nieuwe array terug met alleen de producten waarvan de voorraad groter is dan 0. Toon daarna de namen van de beschikbare producten.

**Hint:** Maak een lege array aan. Loop door de producten heen en voeg een product toe aan de nieuwe array wanneer de voorraad groter is dan 0.

### Opgave 4: Gemiddelde en resultaat

Maak een functie `calculateAverage(array $scores): float` die het gemiddelde van een niet-lege array met scores teruggeeft. Maak daarnaast een functie `getResult(float $average): string` die de tekst `"Passed"` of `"Not passed"` teruggeeft. Een gemiddelde van 55 of hoger is voldoende om te slagen. Test de functies met verschillende scores.

**Hint:** Deel de som van de scores door het aantal scores. Gebruik `int` voor de scores, `float` voor het gemiddelde en `string` voor het resultaat.

### Opgave 5: Rapport voor studenten

Maak een rapport voor meerdere studenten. Sla per student de naam en een niet-lege array met scores op. Maak een functie die voor één student een rapportregel teruggeeft met de naam, het gemiddelde en `"Passed"` of `"Not passed"`. Gebruik daarvoor de functies uit de vorige opgave. Gebruik daarna een loop om voor iedere student een rapportregel te tonen.

**Hint:** Gebruik de functie uit de vorige opgave om het resultaat te bepalen. Geef iedere functie alleen de gegevens mee die zij nodig heeft en laat haar een waarde teruggeven met `return`.
