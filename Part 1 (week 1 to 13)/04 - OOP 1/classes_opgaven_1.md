# Opgaven classes

Probeer de opgaven eerst zelf te maken. De voorbeelden in [classes.md](./classes.md) helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Test je class steeds door objecten aan te maken en de methods aan te roepen.

### Opgave 1: Auto

Maak een class `Car` met de properties `brand` (string) en `speed` (int). De snelheid begint bij 0. Maak de methods `accelerate(int $amount)` en `brake(int $amount)`. Maak twee auto's aan, laat ze verschillend optrekken en toon van beide de merknaam en de snelheid.

**Hint:** Maak voor nu alle properties `public`. Een method past de property aan met `$this->speed`.

### Opgave 2: Rechthoek

Maak een class `Rectangle` met een constructor die de breedte en de hoogte (beide `int`) meekrijgt. Maak de methods `getArea(): int` en `getPerimeter(): int` die de oppervlakte en de omtrek teruggeven. Maak minstens twee rechthoeken aan en toon van beide de oppervlakte en de omtrek.

**Hint:** Sla de breedte en hoogte op in properties in de constructor. De methods kunnen daarna via `$this` bij de waarden.

### Opgave 3: Teller

Maak een class `Counter` met een `private` property `count` die bij 0 begint. Maak de methods `increment()`, `reset()` en `getCount(): int`. Roep de methods aan en toon de waarde. Probeer daarna `$counter->count = 100;` en kijk welke foutmelding je krijgt. Schrijf in een comment op waarom dit juist handig is.

**Hint:** Een `private` property kun je alleen binnen de class gebruiken, dus de `echo` hoort met `getCount()` te werken.

### Opgave 4: Thermostaat

Maak een class `Thermostat` met een `readonly` property `room` (string) en een `private` property `temperature` (int). Beide krijgen hun waarde in de constructor. Maak de methods `increase(int $degrees)`, `decrease(int $degrees)` en `getTemperature(): int`. De temperatuur mag nooit lager zijn dan 5 en nooit hoger dan 30. Zou een aanpassing hierbuiten vallen, dan toont de method een melding en blijft de temperatuur gelijk.

Maak twee thermostaten voor twee verschillende kamers en laat zien dat ze los van elkaar werken. Probeer ook `$thermostat->room = "Keuken";` en kijk welke foutmelding je krijgt.

**Hint:** Bereken eerst de nieuwe temperatuur en controleer met `if` of die tussen de grenzen valt, voordat je de property aanpast.

### Opgave 5: Scorekaart

Bij de opgaven over functies heb je met een array scores het gemiddelde en het resultaat uitgerekend. Maak nu een class `ScoreCard` die de scores van één student bijhoudt, zodat de scores en de functies bij elkaar staan.

- De constructor krijgt de naam van de student (`string`, `readonly`).
- De scores bewaar je in een `private` array.
- `addScore(int $score)` voegt een score toe, maar alleen als die tussen 0 en 100 ligt (inclusief). Anders toont de method een melding.
- `getAverage(): float` geeft het gemiddelde terug. Zijn er nog geen scores, dan is het gemiddelde 0.
- `getResult(): string` geeft `"Passed"` terug bij een gemiddelde van 55 of hoger, anders `"Not passed"`.

Maak twee `ScoreCard` objecten, voeg scores toe (ook een ongeldige score) en toon voor beide de naam, het gemiddelde en het resultaat.

**Hint:** In een method voeg je een waarde toe aan de array met `$this->scores[] = $score;`. Gebruik `count()` en `array_sum()` voor het gemiddelde.
