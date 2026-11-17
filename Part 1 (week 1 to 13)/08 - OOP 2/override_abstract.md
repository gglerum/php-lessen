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

### Wat mag je veranderen aan de method?

Tot nu toe was de overschreven method steeds precies gelijk aan die van de parent. Dat is niet verplicht, maar er zijn regels. De gedachte erachter is simpel: code die met een `Combatant` werkt, moet ook met een `Enemy` kunnen werken. Een `Enemy` mag dus nooit meer vragen of minder beloven dan een `Combatant`. Hoe dat precies werkt zie je volgende les bij polymorfisme. Eerst de regels:

| Onderdeel | Wat mag in de child? | Wat mag niet? |
|---|---|---|
| Naam | Dezelfde naam | Een andere naam is geen override, maar een nieuwe method |
| Parameters | Extra parameters, als ze optioneel zijn (met een standaardwaarde) | Een extra verplichte parameter, of minder parameters |
| Type van een parameter | Hetzelfde of ruimer (`Item` waar de parent `Potion` vraagt), of het type weglaten | Smaller (`HealingPotion` waar de parent `Potion` vraagt), of een type toevoegen waar de parent er geen had |
| Return type | Hetzelfde of smaller (`Potion` waar de parent `Item` teruggeeft), of een return type toevoegen | Ruimer, of het return type weglaten |
| Standaardwaarde van een parameter | Een andere waarde | |
| Zichtbaarheid | Hetzelfde of ruimer (`protected` wordt `public`) | Minder zichtbaar (`public` wordt `protected`) |
| `static` | Hetzelfde | Een gewone method `static` maken, of andersom |

Laten we dat bekijken met een paar items uit ons spel:

```PHP
class Item {}
class Potion extends Item {}

class Combatant
{
    public function use(Potion $potion): void { /* ... */ }

    public function dropLoot(): Item
    {
        return new Item();
    }

    public function rest(int $hours): void { /* ... */ }
}

class Enemy extends Combatant
{
    public function use(Item $item): void { /* ... */ }

    public function dropLoot(): Potion
    {
        return new Potion();
    }

    public function rest(int $hours, bool $deep = false): void { /* ... */ }
}
```

Dit is allemaal toegestaan. Kijk waarom:
- `use(Item $item)` accepteert meer dan de parent. Elke `Potion` is ook een `Item`, dus alles wat je aan een `Combatant` kon geven, kun je ook aan een `Enemy` geven. Dit heet **contravariantie**.
- `dropLoot(): Potion` belooft meer dan de parent. Wie een `Item` verwacht, krijgt nog steeds een `Item`, want een `Potion` is er een. Dit heet **covariantie**.
- `rest(8)` werkt nog steeds. De extra parameter is optioneel, dus wie de method aanroept zoals bij de parent merkt er niets van.

Draai je het om, dan gaat het mis. Een `Enemy` die alleen een `HealingPotion` accepteert terwijl de parent elke `Potion` accepteert, of een `rest(int $hours, bool $deep)` zonder standaardwaarde, geeft meteen een Fatal error:

```
Fatal error: Declaration of Enemy::rest(int $hours, bool $deep) must be compatible with Combatant::rest(int $hours)
```

PHP controleert dit zodra de class wordt ingelezen, ook als je de method nooit aanroept. De melding zegt dus: de declaratie van de child is niet compatibel met die van de parent.

Voor zichtbaarheid geldt hetzelfde. Een `protected` method mag in de child `public` worden, want dan kan er meer bij. Andersom niet: dan zou code die met de parent werkte ineens niet meer bij de method kunnen (`Access level to Enemy::describe() must be public (as in class Combatant)`).

Nog een paar dingen om te weten:
- **Private methods worden niet overschreven.** Een child kan een eigen method met dezelfde naam maken, maar dat is gewoon een andere method. De code in de parent blijft zijn eigen `private` method aanroepen. Daarom gelden bovenstaande regels er niet voor.
- **De constructor is uitgezonderd.** Een `Npc` mocht daarom een constructor met een extra parameter hebben (zie vorige les). Dit geldt niet voor een abstracte constructor: die moet de child wel volgen.
- **Properties** mag je ook opnieuw declareren, met strengere regels. Het type mag niet veranderen en een gewone property kan niet `readonly` worden (of andersom). De zichtbaarheid mag wel ruimer en je mag een andere standaardwaarde kiezen. Een `public int $damage` mag dus niet `public string $damage` worden.

Zie `override_rules.php`.

### Overschrijven verbieden met final

Soms wil je juist niet dat een child een method aanpast. Bijvoorbeeld de regels voor schade: elke `Combatant` moet dezelfde berekening gebruiken, anders ontstaan er verrassingen. Met `final` verbied je het overschrijven:

```PHP
class Combatant
{
    // ...

    final public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }
}

class Enemy extends Combatant
{
    public function takeDamage(int $damage): void { /* ... */ }
}
```

PHP geeft een Fatal error: `Cannot override final method Combatant::takeDamage()`.

Ook een hele class kan `final` zijn. Dan kan niemand er nog van erven:

```PHP
final class Dragon extends Enemy
{
}

class Elder extends Dragon {}   // Fatal error: Class Elder cannot extend final class Dragon
```

Een `final` class kan zelf wel van een andere class erven, zoals `Dragon` van `Enemy`. Heeft een `final` class methods die `final` zijn, dan is dat dubbelop. Een `private` method `final` maken heeft geen zin (behalve bij de constructor): die wordt toch niet overschreven en PHP geeft een waarschuwing. Zie `final_keyword.php`.

Gebruik `final` niet standaard. Een `final` method is lastig aan te passen voor anderen. Het is bedoeld voor code waar je zeker weet dat een child hem nooit anders mag laten werken.

### Controleren dat je echt overschrijft

Stel dat je in `Enemy` per ongeluk `descibe()` schrijft in plaats van `describe()`. PHP geeft geen foutmelding: het is gewoon een nieuwe method. Je override doet dan niets en je merkt het pas als de uitvoer niet klopt.

Vanaf PHP 8.3 kun je dit laten controleren met het attribuut `#[\Override]`:

```PHP
class Enemy extends Combatant
{
    #[\Override]
    public function descibe(): string
    {
        return "Enemy";
    }
}
```

Bestaat er geen method met die naam in een parent, dan geeft PHP een Fatal error: `Enemy::descibe() has #[\Override] attribute, but no matching parent method exists`. Dit attribuut bestaat sinds PHP 8.3 voor methods en werkt sinds PHP 8.5 ook voor properties. We gebruiken de laatste PHP versie als uitgangspunt. Draai je een oudere versie, dan wordt het attribuut genegeerd en krijg je de controle niet. Het is een goede gewoonte om `#[\Override]` bij elke override te zetten.

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

### De regels van abstracte classes en methods

Een paar regels om te onthouden:
- **Een class met een abstracte method moet zelf abstract zijn.** Anders geeft PHP `Class A declares abstract method f() and must therefore be declared abstract`. Andersom hoeft het niet: een abstracte class zonder abstracte methods mag, bijvoorbeeld om te voorkomen dat iemand er een object van maakt.
- **Een abstracte method is `public` of `protected`.** Abstract en `private` samen kan niet, want een `private` method kan de child nooit schrijven.
- **De implementatie in de child volgt dezelfde regels als bij een override.** Dus dezelfde parameters (extra parameters alleen als ze optioneel zijn), een return type dat gelijk of smaller is en een zichtbaarheid die gelijk of ruimer is. Een `protected` abstracte method mag in de child dus `public` worden. Past de implementatie niet, dan geeft PHP dezelfde melding als hierboven: `must be compatible with`.
- **Een abstracte method hoeft alleen de verplichte parameters te noemen.** De child mag er optionele aan toevoegen, zoals `attack(int $baseDamage, bool $critical = false)`.
- **De constructor is hier geen uitzondering.** Een abstracte `__construct()` moet de child wel volgen, anders geeft PHP `must be compatible with`.
- **Een abstracte class mag de verplichting doorgeven.** Een class die van een abstracte class erft en niet alle abstracte methods schrijft, moet zelf ook `abstract` zijn. De verplichting gaat dan naar zijn children.

```PHP
abstract class Combatant
{
    abstract protected function getAttackBonus(): int;

    abstract public function attack(int $baseDamage): int;
}

// Boss schrijft attack(), maar niet getAttackBonus(), dus Boss blijft abstract
abstract class Boss extends Combatant
{
    public function attack(int $baseDamage): int
    {
        return $baseDamage + $this->getAttackBonus() * 2;
    }
}

class Dragon extends Boss
{
    protected function getAttackBonus(): int
    {
        return 5;
    }
}
```

Alleen `Dragon` is een echte class en kun je met `new` aanmaken. Zie `abstract_rules.php`.

Waarom dit zo handig is, zie je in de volgende les bij polymorfisme.

## Wat we nu niet behandelen

Een paar onderwerpen uit de PHP documentatie die hier aansluiten, maar bewust voor later zijn:
- **Property hooks, asymmetrische zichtbaarheid (`private(set)`) en abstracte properties.** Dit zijn mogelijkheden van PHP 8.4 en nieuwer. Een property hook vervangt de getters en setters uit OOP 1 door code die direct bij de property staat, en een child kan zo'n hook overschrijven met `parent::$naam::get()`. Dat is een onderwerp op zich, dat pas zin heeft als de basis van getters en overerving er goed in zit. Dat komt in OOP 3 aan bod.
- **Interfaces.** Een interface is net als een abstracte class een contract voor methods, maar dan zonder code. Dit komt in OOP 3.
- **Constanten in classes**, inclusief `final` constanten (PHP 8.1), horen bij OOP 3.
- **Traits**, een andere manier om code te hergebruiken, horen ook bij OOP 3.

## Samengevat

| Begrip | Vraag die je stelt | Let op |
|---|---|---|
| Override | Moet de child iets anders doen dan de parent? | Dezelfde naam, en een signature die compatibel is met die van de parent |
| Signature van een override | Vraagt of belooft de child hetzelfde of beter? | Parametertypes ruimer, return type smaller, extra parameters optioneel, zichtbaarheid ruimer |
| `parent::method()` | Wil ik het gedrag van de parent uitbreiden in plaats van vervangen? | Vergeet je het, dan wordt de method van de parent niet uitgevoerd |
| `final` | Mag geen enkele child dit anders doen? | Gebruik het niet standaard, het maakt aanpassen lastig |
| `abstract` class | Heeft deze class alleen zin als parent van andere classes? | Je kunt er geen object van maken met `new` |
| `abstract` method | Moet elke child dit zelf invullen? | Geen body, `public` of `protected`, en elke child moet hem schrijven met een compatibele signature |

## Voorbeeldbestanden

De voorbeelden gebruiken het rollenspel uit de vorige les. Run ze met `php bestandsnaam.php` vanuit de map `example/override_abstract`.

1. [override.php](./example/override_abstract/override.php) overschrijft `describe()` en `takeDamage()`, met en zonder `parent::`.
2. [override_rules.php](./example/override_abstract/override_rules.php) laat zien wat je aan een override mag veranderen (parameters, return type, zichtbaarheid) en wat niet, met de foutmeldingen van PHP.
3. [final_keyword.php](./example/override_abstract/final_keyword.php) verbiedt het overschrijven van een method en het erven van een class met `final`.
4. [abstract_classes.php](./example/override_abstract/abstract_classes.php) maakt `Combatant` abstract met een abstracte `attack()`.
5. [abstract_rules.php](./example/override_abstract/abstract_rules.php) laat de regels van abstracte methods zien: zichtbaarheid, optionele parameters en een abstracte class die de verplichting doorgeeft.

## Labs

Open [index.html](./example/override_abstract/lab/index.html) in je browser, je hoeft niets te installeren. Werkt de opmaak niet, start dan `php -S localhost:8090` in de map `example/override_abstract/lab`.

1. [objects.html](./example/override_abstract/lab/objects.html) is Lab 1. Een step debugger met drie scenario's: een method die wordt overschreven met `parent::`, vervangen of uitbreiden van `describe()` en een abstracte class met een `attack()` per child.
2. [predict_output.html](./example/override_abstract/lab/predict_output.html) is Lab 2. Acht scenario's waarbij je eerst voorspelt wat er gebeurt, zoals een method die vervangen of uitgebreid is, een override met een andere signature, een abstracte class aanmaken en een class die de abstracte method niet schrijft.
3. [bughunt.html](./example/override_abstract/lab/bughunt.html) is Lab 3. Twee scripts die iets anders doen dan bedoeld: een override zonder `parent::takeDamage()` en een `new` op een abstracte class.

Met `php verify_lab1.php` tot en met `php verify_lab3.php` (in de map `example/override_abstract`) controleer je dat de antwoorden in de labs kloppen met wat PHP echt doet.

## Opgaven

De opgaven staan in [override_abstract_opgaven_1.md](./override_abstract_opgaven_1.md).
