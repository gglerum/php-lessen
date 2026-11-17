# Method overriding en abstracte classes

## Vorige keer

Vorige keer hebben we inheritance behandeld. Een child class erft de properties en methods van zijn parent met `extends`. Met `protected` kan de child bij de data van de parent, en met `parent::__construct()` start je de constructor van de parent vanuit de eigen constructor.

We gebruiken weer het rollenspel. Dit is waar we gebleven waren:

```PHP
class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function getHealth(): int
    {
        return $this->health;
    }
}

class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
    }
}
```

Een `Enemy` heeft alles van een `Combatant`, plus loot. Vandaag gaan we twee dingen doen die vorige keer nog niet kon: een child die een method van zijn parent anders laat werken, en een parent die afdwingt dat elke child bepaalde methods heeft.

## Een method overschrijven

Een child kan een method van de parent **overschrijven** (override). Je schrijft dan in de child een method met dezelfde naam en dezelfde parameters. Voor een object van de child wordt dan jouw versie gebruikt.

```PHP
class Combatant
{
    // ...

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
    }
}

class Npc extends Combatant
{
    // ...

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }
}
```

Een `Combatant` beschrijft zichzelf met naam en health. Een `Npc` heeft een eigen verhaal en vervangt de method helemaal.

Soms wil je de method van de parent niet vervangen maar uitbreiden. Dan gebruik je opnieuw `parent::`:

```PHP
class Enemy extends Combatant
{
    // ...

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }
}
```

Bij `describe()` geven we het resultaat van de parent terug met iets erachter. Bij `takeDamage()` laten we het rekenwerk aan de parent en voegen we er een controle aan toe. We kopiëren dus niets, en verandert de berekening in `Combatant`, dan verandert hij ook voor `Enemy`. Zie `override.php`.

## Abstracte classes

Kijk nog eens naar `Combatant`. Heeft het zin om een los `new Combatant(...)` te maken? Eigenlijk niet: in ons spel is iedereen een `Player`, `Npc` of `Enemy`. `Combatant` bestaat alleen om de gemeenschappelijke code te bewaren.

Met `abstract` leggen we dat vast. Een **abstracte class** kun je niet zelf aanmaken, je kunt er alleen van erven:

```PHP
abstract class Combatant
{
    // ... properties, constructor, takeDamage() en getters zoals eerst

    abstract public function attack(): int;
}
```

Er is nog iets nieuws: een **abstracte method**. Dit is een method zonder body, alleen de naam, de parameters en het return type, afgesloten met een `;`. Hiermee zeg je: "elke Combatant kan aanvallen, maar hoe dat gaat bepaalt de child zelf". Elke child moet de method dan schrijven:

```PHP
class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Enemy extends Combatant
{
    private int $threatLevel;

    // ...

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}
```

Een speler wordt sterker met ervaring, een vijand met zijn dreigingsniveau. Dit is ook wat de diagrammen laten zien: `Player` en `Enemy` hebben allebei een `attack()`, maar ze doen iets anders.

PHP controleert dit voor je:
- `new Combatant(...)` geeft `Cannot instantiate abstract class Combatant`.
- Een class die van `Combatant` erft maar `attack()` niet schrijft, geeft een Fatal error.

Het verschil met een gewone parent: bij een gewone method die je overschrijft mag de child kiezen. Bij een abstracte method **moet** de child hem schrijven. Zo weet je zeker dat elke `Combatant` die je tegenkomt een `attack()` heeft.

Een abstracte class mag ook gewone methods hebben. `takeDamage()` heeft gewoon een body en werkt voor iedereen hetzelfde. Zie `abstract_classes.php`.

Waarom dit zo handig is, zie je in de volgende les bij polymorfisme.

## Samengevat

| Begrip | Vraag die je stelt | Let op |
|---|---|---|
| Override | Moet de child iets anders doen dan de parent? | Dezelfde naam en parameters als in de parent |
| `parent::method()` | Wil ik het gedrag van de parent uitbreiden in plaats van vervangen? | Vergeet je het, dan wordt de method van de parent niet uitgevoerd |
| `abstract` class | Heeft deze class alleen zin als parent van andere classes? | Je kunt er geen object van maken met `new` |
| `abstract` method | Moet elke child dit zelf invullen? | Geen body, en elke child moet hem schrijven |

## Voorbeeldbestanden

De voorbeelden gebruiken het rollenspel uit de vorige les. Run ze met `php bestandsnaam.php` vanuit de map `example/override_abstract`.

1. [override.php](./example/override_abstract/override.php) overschrijft `describe()` en `takeDamage()`, met en zonder `parent::`.
2. [abstract_classes.php](./example/override_abstract/abstract_classes.php) maakt `Combatant` abstract met een abstracte `attack()`.

## Labs

Open [index.html](./example/override_abstract/lab/index.html) in je browser, je hoeft niets te installeren. Werkt de opmaak niet, start dan `php -S localhost:8090` in de map `example/override_abstract/lab`.

1. [objects.html](./example/override_abstract/lab/objects.html) is Lab 1. Een step debugger met drie scenario's: een method die wordt overschreven met `parent::`, vervangen of uitbreiden van `describe()` en een abstracte class met een `attack()` per child.
2. [predict_output.html](./example/override_abstract/lab/predict_output.html) is Lab 2. Zes scenario's waarbij je eerst voorspelt wat er gebeurt, zoals een method die vervangen of uitgebreid is, een abstracte class aanmaken en een class die de abstracte method niet schrijft.
3. [bughunt.html](./example/override_abstract/lab/bughunt.html) is Lab 3. Twee scripts die iets anders doen dan bedoeld: een override zonder `parent::takeDamage()` en een `new` op een abstracte class.

Met `php verify_lab1.php` tot en met `php verify_lab3.php` (in de map `example/override_abstract`) controleer je dat de antwoorden in de labs kloppen met wat PHP echt doet.

## Opgaven

De opgaven staan in [override_abstract_opgaven_1.md](./override_abstract_opgaven_1.md).
