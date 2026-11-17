# Opgaven inheritance

Probeer de opgaven eerst zelf te maken. De voorbeelden in [inheritance.md](./inheritance.md) helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Maak properties `private` of `protected` en test je classes steeds door objecten aan te maken en de methods aan te roepen.

### Opgave 1: Voertuigen

Maak een class `Vehicle` met een `protected` property `brand` (string) en een `protected` property `speed` (int, begint bij 0). De constructor krijgt alleen het merk. Maak de methods `accelerate(int $amount)`, `getBrand(): string` en `getSpeed(): int`.

Maak daarna twee child classes:
- `Car` heeft er ook een aantal deuren (int) bij en een method `getDoors(): int`.
- `Bicycle` heeft niets extra's en is dus een lege class die alleen van `Vehicle` erft.

Maak een `Car("Volvo", 5)` en een `Bicycle("Gazelle")`, laat de auto 50 en de fiets 20 optrekken en toon van beide de gegevens. De uitvoer is:

```
Volvo: 50 km/u, 5 deuren
Gazelle: 20 km/u
```

**Hint:** `Car` heeft een eigen constructor nodig en roept daarin `parent::__construct()` aan. `Bicycle` heeft geen constructor nodig: die van `Vehicle` wordt geërfd.

### Opgave 2: Medewerkers

Maak een class `Employee` met de `protected` properties `name` (string) en `monthlySalary` (int), en de methods `getName(): string` en `getYearlySalary(): int` (twaalf maanden salaris). Maak een class `Manager` die van `Employee` erft en daarnaast een `bonus` (int) heeft, met een method `getBonus(): int`.

Maak een `Employee("Sanne", 3000)` en een `Manager("Joris", 4000, 5000)`:

```
Sanne verdient 36000 per jaar
Joris verdient 48000 per jaar en heeft een bonus van 5000
```

Let op dat het jaarsalaris van de manager hier nog zonder de bonus wordt berekend. Dat veranderen we in de volgende opgaven.

**Hint:** De constructor van `Manager` heeft drie parameters en geeft er twee door aan `parent::__construct()`.

### Opgave 3: Is een of heeft een?

Bepaal voor ieder paar of het inheritance (`is een`) of compositie (`heeft een`) is. Zet je antwoord met een korte reden in een comment bovenaan een bestand:

1. `Dog` en `Animal`
2. `Car` en `Engine`
3. `Teacher` en `Person`
4. `Library` en `Book`
5. `Order` en `OrderLine`
6. `Manager` en `Employee`

Werk daarna het tweede paar uit. Maak een class `Engine` met een `private` property `horsepower` (int) en een property die bijhoudt of de motor draait, met de methods `start()`, `stop()` en `isRunning(): bool`. Maak een class `Car` met een merk en een `Engine`. De `Engine` geef je mee aan de constructor van `Car`. Geef `Car` een method `start()` en een method `describe()`. Een `Car("Volvo", new Engine(150))` toont eerst `Volvo met 150 pk is uit` en na `start()` `Volvo met 150 pk is gestart`.

**Hint:** `Car` heeft zelf geen `isRunning()` nodig. Hij vraagt het aan zijn `Engine` en stuurt `start()` ook door naar de motor.

### Opgave 4: Een boom van drie lagen

Maak een class `Person` met een `protected` property `name` en een method `getName()`. Maak een class `Student` die van `Person` erft en een `school` heeft, met `getSchool()`. Maak een class `Intern` die van `Student` erft en een `company` heeft, met `getCompany()`. Elke class heeft een eigen constructor met de parameters van zijn parent erbij.

Maak een `Intern("Lisa", "HAN", "Philips")` en toon:

```
Lisa studeert aan HAN en loopt stage bij Philips
```

Zet in een comment in welke class `getName()` staat en waarom een `Intern` hem toch kan gebruiken. Teken daarna het diagram van deze drie classes zoals in `metinheritance.svg`.

**Hint:** De constructor van `Intern` heeft drie parameters, geeft er twee door aan de constructor van `Student`, en die geeft er weer een door aan `Person`.

Het overschrijven van methods en abstracte classes komen terug in [override_abstract_opgaven_1.md](./override_abstract_opgaven_1.md).
