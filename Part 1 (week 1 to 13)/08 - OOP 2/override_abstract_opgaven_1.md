# Opgaven method overriding en abstracte classes

Probeer de opgaven eerst zelf te maken. De voorbeelden in [override_abstract.md](./override_abstract.md) helpen je met de syntax, maar de opdrachten gebruiken andere voorbeelden en vragen je om de onderdelen zelf te combineren. Bij iedere opgave staat een kleine hint.

Maak voor iedere opgave een apart bestand en gebruik `declare(strict_types=1);` bovenaan. Opgave 1 en 2 bouwen voort op opgave 1 en 2 van [inheritance_opgaven_1.md](./inheritance_opgaven_1.md): kopieer je classes van daar.

### Opgave 1: Voertuigen

Geef `Vehicle` een method `describe(): string` die bijvoorbeeld `Volvo rijdt 50 km/u` teruggeeft. Pas daarna twee child classes aan:
- `Car` overschrijft `describe()`. De tekst van `Vehicle` wordt teruggegeven met erachter ` en heeft 5 deuren`.
- `Bicycle` kan per keer maar 5 km/u optrekken, hoe groot het bedrag ook is dat aan `accelerate()` meegegeven wordt.

Maak weer een `Car("Volvo", 5)` en een `Bicycle("Gazelle")`, laat ze allebei 50 optrekken en toon van beide de `describe()`. De uitvoer is:

```
Volvo rijdt 50 km/u en heeft 5 deuren
Gazelle rijdt 5 km/u
```

**Hint:** In `Car` gebruik je `parent::describe()`. Voor `Bicycle` schrijf je `accelerate()` opnieuw en laat je de parent het eigenlijke werk doen met `parent::accelerate()`. De functie `min()` geeft het kleinste van twee getallen.

### Opgave 2: Medewerkers

Een manager krijgt zijn bonus bij het jaarsalaris. Overschrijf `getYearlySalary()` in `Manager`, zodat het jaarsalaris van `Manager("Joris", 4000, 5000)` nu 53000 is. Het jaarsalaris van `Employee("Sanne", 3000)` blijft 36000:

```
Sanne: 36000
Joris: 53000
```

Schrijf het jaarsalaris van `Manager` zonder `monthlySalary * 12` nogmaals te schrijven. Waarom is dat beter? Zet het antwoord in een comment.

**Hint:** Je overschrijft `getYearlySalary()` en gebruikt `parent::getYearlySalary()` binnen de method.

### Opgave 3: Vormen

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

### Opgave 4: Producten

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

### Opgave 5: Mag dit?

Gegeven deze parent:

```PHP
class Weapon
{
    public function hit(int $force): int
    {
        return $force;
    }
}
```

Hieronder staan zeven manieren om `hit()` in een child `Sword extends Weapon` te schrijven:

1. `public function hit(int $force, int $bonus): int`
2. `public function hit(int $force, int $bonus = 0): int`
3. `public function hit(int|float $force): int`
4. `public function hit(int $force): string`
5. `protected function hit(int $force): int`
6. `public function hit($force): int`
7. `public function hit(int $force): ?int`

Bepaal eerst bij elke versie of PHP hem toestaat en schrijf in een comment waarom wel of niet. Maak daarna voor elke versie een apart bestand met `Weapon` en `Sword` en kijk of je gelijk had. Bij de versies die niet mogen, schrijf je de melding van PHP in de comment.

**Hint:** Vraag jezelf bij elke versie af: kan code die met een `Weapon` werkt ook met deze `Sword` werken? Denk aan wat je aan `hit()` meegeeft en wat je terugkrijgt.

### Opgave 6: Final en abstract samen

Maak een abstracte class `Account` met een `protected` property `balance` (int, begint bij 0) en een `final public` method `deposit(int $amount): void` die het bedrag bij het saldo optelt. Maak daarnaast een abstracte method `protected function getFee(): int`, de kosten die bij een opname horen, en een gewone method `withdraw(int $amount): void` die `$amount` plus `getFee()` van het saldo afhaalt.

Maak twee child classes:
- `CheckingAccount` heeft kosten van 1.
- `SavingsAccount` heeft kosten van 0, maar maakt `getFee()` `public` (dat mag).

Toon het saldo van beide rekeningen na een storting van 100 en een opname van 30. Je krijgt `69` en `70`. Probeer daarna in `SavingsAccount` ook `deposit()` te overschrijven en schrijf de melding van PHP in een comment. Leg in een comment uit waarom `deposit()` hier `final` is en `getFee()` abstract.

**Hint:** Een rekening heeft nog geen method om het saldo te tonen. Schrijf een `getBalance(): int` in `Account` (zonder `final`). Voor de storting en opname maak je de objecten zelf aan: `Account` is abstract.
