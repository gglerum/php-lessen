# Opgaven inheritance

Probeer de opgaven eerst zelf te maken. De voorbeelden in [inheritance.md](./inheritance.md) helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Maak properties `private` of `protected` en test je classes steeds door objecten aan te maken en de methods aan te roepen.

### Opgave 1: Voertuigen

Maak een class `Vehicle` met een `protected` property `brand` (string) en een `protected` property `speed` (int, begint bij 0). De constructor krijgt alleen het merk. Maak de methods `accelerate(int $amount)`, `getSpeed(): int` en `describe(): string`. De laatste geeft bijvoorbeeld `Volvo rijdt 50 km/u` terug.

Maak daarna twee child classes:
- `Car` heeft er ook een aantal deuren (int) bij. De `describe()` van een `Car` geeft de tekst van `Vehicle` terug, met erachter ` en heeft 5 deuren`.
- `Bicycle` kan per keer maar 5 km/u optrekken, hoe groot het bedrag ook is dat aan `accelerate()` meegegeven wordt.

Maak een `Car("Volvo", 5)` en een `Bicycle("Gazelle")`, laat ze allebei 50 optrekken en toon van beide de `describe()`. De uitvoer is:

```
Volvo rijdt 50 km/u en heeft 5 deuren
Gazelle rijdt 5 km/u
```

**Hint:** `Car` heeft een eigen constructor nodig en roept daarin `parent::__construct()` aan. Voor `Bicycle` schrijf je `accelerate()` opnieuw en laat je de parent het eigenlijke werk doen met `parent::accelerate()`. De functie `min()` geeft het kleinste van twee getallen.

### Opgave 2: Medewerkers

Maak een class `Employee` met de `protected` properties `name` (string) en `monthlySalary` (int), en de methods `getName(): string` en `getYearlySalary(): int` (twaalf maanden salaris). Maak een class `Manager` die van `Employee` erft en daarnaast een `bonus` (int) heeft. Het jaarsalaris van een `Manager` is dat van een `Employee` plus de bonus.

Maak een `Employee("Sanne", 3000)` en een `Manager("Joris", 4000, 5000)` en toon van beide de naam en het jaarsalaris:

```
Sanne: 36000
Joris: 53000
```

Schrijf het jaarsalaris van `Manager` zonder `monthlySalary * 12` nogmaals te schrijven. Waarom is dat beter? Zet het antwoord in een comment.

**Hint:** Je overschrijft `getYearlySalary()` en gebruikt `parent::getYearlySalary()` binnen de method.

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

### Opgave 4: Vormen

Maak een abstracte class `Shape` met een `protected` property `name` (string) en een abstracte method `getArea(): float`. Geef `Shape` ook een gewone method `describe(): string` die de naam en de oppervlakte op twee decimalen teruggeeft. Maak twee child classes:
- `Rectangle` met breedte en hoogte.
- `Circle` met een straal. De oppervlakte is `M_PI * straal²`.

Toon van `new Rectangle(4, 5)` en `new Circle(3)` de `describe()`:

```
Rechthoek met oppervlakte 20
Cirkel met oppervlakte 28.27
```

Probeer ook `new Shape("Vorm")` en kijk welke foutmelding je krijgt. Schrijf daarna een tijdelijke class `Triangle extends Shape` zonder `getArea()` en kijk wat PHP zegt. Haal die class daarna weer weg.

**Hint:** In `describe()` roep je `$this->getArea()` aan. Dat werkt in de abstracte class, ook al staat de body van `getArea()` pas in de child. `round($getal, 2)` rondt af op twee decimalen.

### Opgave 5: Producten

Maak een abstracte class `Product` met `name` en `price` (float) en een abstracte method `getDescription(): string`. Maak twee child classes:
- `Book` heeft ook een auteur. De beschrijving is `Dune van Frank Herbert`.
- `Laptop` heeft ook een hoeveelheid geheugen in GB. De beschrijving is `ThinkPad met 16 GB geheugen`. De prijs van een laptop is inclusief 21% btw, dus `getPrice()` van `Laptop` geeft de prijs maal 1.21 terug.

Maak een `Book("Dune", 12.50, "Frank Herbert")` en een `Laptop("ThinkPad", 800.0, 16)` en toon van beide de beschrijving en de prijs op twee decimalen:

```
Dune van Frank Herbert: 12.50
ThinkPad met 16 GB geheugen: 968.00
```

Bedenk daarna een derde soort product en voeg dat toe, zonder iets aan `Product`, `Book` of `Laptop` te veranderen. Wat moet je in ieder geval schrijven?

**Hint:** Je overschrijft `getPrice()` in `Laptop` en roept `parent::getPrice()` aan voor de basisprijs. De functie `number_format($getal, 2)` zet een getal om naar tekst met twee decimalen.
