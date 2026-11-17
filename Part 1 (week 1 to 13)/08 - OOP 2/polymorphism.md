# Polymorphism

## Vorige keer

Vorige keer hebben we gezien hoe een child een method van zijn parent overschrijft, en hoe een abstracte class afdwingt dat elke child een method schrijft. We gebruiken weer het rollenspel. Dit is waar we gebleven waren (ingekort):

```PHP
abstract class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    // constructor, takeDamage(), getName() en getHealth() zoals eerst

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    // ...

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}
```

Elke `Combatant` kan aanvallen, en elke child doet dat op zijn eigen manier. Ik zei toen al dat dit pas echt handig wordt in de volgende les. Dat is vandaag.

## Het probleem

Stel dat we een bericht willen tonen als iemand aanvalt. Zonder iets nieuws schrijven we een functie per soort:

```PHP
function announcePlayer(Player $player): void
{
    echo $player->getName() . " valt aan voor " . $player->attack() . " schade\n";
}

function announceNpc(Npc $npc): void
{
    echo $npc->getName() . " valt aan voor " . $npc->attack() . " schade\n";
}

function announceEnemy(Enemy $enemy): void
{
    echo $enemy->getName() . " valt aan voor " . $enemy->attack() . " schade\n";
}
```

Drie keer dezelfde code. Het kan ook met één functie die zelf kijkt wat voor soort `Combatant` het is:

```PHP
function announceAny(Combatant $combatant): void
{
    if ($combatant instanceof Player) {
        $damage = $combatant->attack();
    } elseif ($combatant instanceof Npc) {
        $damage = $combatant->attack();
    } elseif ($combatant instanceof Enemy) {
        $damage = $combatant->attack();
    } else {
        $damage = 0;
    }

    echo $combatant->getName() . " valt aan voor " . $damage . " schade\n";
}
```

Dit is niet veel beter. In elke tak staat dezelfde regel, en komt er een `Archer` bij, dan moet je deze functie aanpassen. Vergeet je dat, dan krijgt een `Archer` stilletjes `0` schade. Zie `without_polymorphism.php`.

## Een child op de plek van de parent

Het kan veel simpeler. Kijk nog eens naar de parameter van `announceAny()`: die heet `Combatant $combatant`. Een `Player` is een `Combatant`, een `Npc` ook en een `Enemy` ook. Daarom mag je ze alle drie meegeven:

```PHP
function announce(Combatant $combatant): void
{
    echo $combatant->getName() . " valt aan voor " . $combatant->attack() . " schade\n";
}

announce(new Player("Aragorn", 100, 5));
announce(new Npc("Barkeeper Bob", 40, 0));
announce(new Enemy("Goblin", 30, 2, "een dolk", 3));
```

```
Aragorn valt aan voor 20 schade
Barkeeper Bob valt aan voor 1 schade
Goblin valt aan voor 12 schade
```

Dat is **polymorphism**: een object van een child gebruiken op de plek waar de parent verwacht wordt, terwijl elke child zich op zijn eigen manier gedraagt. Het woord betekent "veel vormen": `$combatant` is hier steeds een andere vorm.

Let op wat er gebeurt bij `$combatant->attack()`. De functie weet niet welke soort hij krijgt, maar dat hoeft ook niet. PHP kijkt bij het uitvoeren naar het **object zelf**, niet naar het type dat bij de parameter staat, en voert de `attack()` van dat object uit. Bij de `Player` is dat die van `Player` en bij de `Enemy` die van `Enemy`.

Dit is ook waarom abstracte methods zo handig zijn. Omdat `attack()` in `Combatant` abstract is, weet PHP zeker dat elke child hem heeft. De functie kan hem dus altijd aanroepen.

Het werkt ook in een array en een loop, waar de soorten door elkaar kunnen staan:

```PHP
$combatants = [
    new Player("Aragorn", 100, 5),
    new Npc("Barkeeper Bob", 40, 0),
    new Enemy("Goblin", 30, 2, "een dolk", 3),
];

$total = 0;
foreach ($combatants as $combatant) {
    echo $combatant::class . ": " . $combatant->attack() . "\n";
    $total = $total + $combatant->attack();
}
echo "Samen: " . $total . "\n";
```

```
Player: 20
Npc: 1
Enemy: 12
Samen: 33
```

`$combatant::class` geeft de naam van de echte class van het object. Hier zie je dus dat elke keer een ander object aan de beurt is, terwijl de code in de loop er maar één keer staat.

Het mooiste is wat er gebeurt als er een nieuwe soort bij komt:

```PHP
class Archer extends Combatant
{
    public function attack(): int
    {
        return 7;
    }
}

announce(new Archer("Legolas", 80, 3));
```

`announce()` hoeft niet te veranderen. De functie kende `Archer` niet eens toen hij werd geschreven. Zie `polymorphism.php`.

### Wat zegt PHP hierover?

De PHP documentatie heeft geen eigen hoofdstuk over polymorphism. Wat we hierboven gebruiken staat bij de type declarations: een class naam als type accepteert een object van die class, of van een class die ervan erft. De regels voor een goede override (signature, return type en zichtbaarheid) uit de vorige les zijn de voorwaarde die ervoor zorgt dat dit veilig is. De documentatie noemt dat het Liskov Substitution Principle: een child moet overal kunnen staan waar de parent verwacht wordt. Zie ook hoe het in de praktijk misgaat in lab 3.

## De grenzen

Een parameter met het type `Combatant` accepteert dus niet alles.

### Wat is geen Combatant?

Een object dat niet van `Combatant` erft, wordt geweigerd. PHP controleert dat op het moment dat je de functie aanroept:

```PHP
class Potion {}

announce(new Potion());
```

```
TypeError: announce(): Argument #1 ($combatant) must be of type Combatant, Potion given
```

Dit is een `TypeError`. Dat is een soort `Error`, dus je vangt hem op met `catch (TypeError $error)` of `catch (Error $error)`.

### Wat mag je aanroepen?

Binnen `announce()` weet PHP alleen zeker dat je een `Combatant` hebt. Een method die alleen in een child staat, zoals `getLoot()` van `Enemy`, is niet gegarandeerd aanwezig:

```PHP
function showLoot(Combatant $combatant): void
{
    echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\n";
}

showLoot(new Enemy("Goblin", 30, 2, "een dolk", 3));   // werkt
showLoot(new Player("Aragorn", 100, 5));               // Error
```

```
Error: Call to undefined method Player::getLoot()
```

PHP controleert dit pas als de regel wordt uitgevoerd. Bij de `Enemy` gaat het dus goed, en pas bij de `Player` krijg je de fout. Hoe je dit oplost zie je zo, bij `instanceof`.

### Mag het ook leeg zijn?

Een parameter die een `Combatant` of niets mag zijn, schrijf je met een vraagteken:

```PHP
function describeTarget(?Combatant $target): string
{
    if ($target === null) {
        return "Er is geen doelwit";
    }

    return "Doelwit: " . $target->getName();
}
```

`?Combatant` betekent "een `Combatant` of `null`". Het is hetzelfde als `Combatant|null`. Let op: `Combatant $target = null` kon vroeger ook, maar is vanaf PHP 8.4 deprecated. Gebruik het vraagteken. Zie `type_limits.php`.

## instanceof

In de vorige voorbeelden gebruikten we al `instanceof`. Het vraagt: "is dit object van deze class, of van een class die ervan erft?" Het antwoord is `true` of `false`, nooit een fout:

```PHP
$player = new Player("Aragorn", 100, 5);

var_dump($player instanceof Player);     // true
var_dump($player instanceof Combatant);  // true: een Player is ook een Combatant
var_dump($player instanceof Enemy);      // false: Player en Enemy zijn broers
var_dump(null instanceof Combatant);     // false: null is geen object
```

Een `Player` en een `Enemy` hebben allebei `Combatant` als parent, maar ze erven niet van elkaar. Daarom geeft de derde regel `false`. Met `!` draai je het om (`!$player instanceof Enemy`): `instanceof` gaat voor `!`, dus je hebt geen haakjes nodig.

Wil je de echte class weten, dan gebruik je `::class` zoals hierboven (`$player::class` is `"Player"`). `instanceof` is dus niet hetzelfde als "is exact deze class".

Nu de oplossing voor `getLoot()`: controleer eerst of het object een `Enemy` is.

```PHP
function showLoot(Combatant $combatant): void
{
    if ($combatant instanceof Enemy) {
        echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\n";
    } else {
        echo $combatant->getName() . " heeft niets bij zich\n";
    }
}
```

Dit is een goed gebruik van `instanceof`: je wilt iets doen dat alleen een `Enemy` kan.

Gebruik `instanceof` dus niet om te bepalen **wat er per soort gebeurt**, zoals in `announceAny()` aan het begin. Dat lost een method per child zoals `attack()` veel beter op. Staan er in jouw code lange `if ... instanceof ... elseif ... instanceof` ketens? Dan mist er meestal een method in de parent. Zie `instanceof.php`.

## Polymorphism bij return types

In de vorige les zagen we dat een override een smaller return type mag hebben. Dat is ook polymorphism, maar dan bij wat een method teruggeeft:

```PHP
class Combatant
{
    public function dropLoot(): Item
    {
        return new Item("niets");
    }
}

class Enemy extends Combatant
{
    public function dropLoot(): Potion
    {
        return new Potion("een helingsdrank");
    }
}

function collect(Combatant $combatant): void
{
    $item = $combatant->dropLoot();
    echo "Je vindt " . $item->getName() . " (" . $item::class . ")\n";
}

collect(new Combatant());
collect(new Enemy());
```

```
Je vindt niets (Item)
Je vindt een helingsdrank (Potion)
```

`collect()` verwacht een `Item` van `dropLoot()` en krijgt bij een `Enemy` een `Potion`. Dat is geen probleem, want een `Potion` is een `Item`. Dit is de covariantie uit de vorige les, nu in gebruik. Zie `return_types.php`.

## Wat we nu niet behandelen

- **Interfaces.** Alles wat we vandaag deden werkt omdat de classes een gemeenschappelijke parent hebben. Een interface is een contract voor methods zonder code, waarmee ook classes die geen parent delen op elkaars plek kunnen staan. Dat komt in OOP 3.
- **Intersection types** (`A&B`) en DNF types. Die combineren class types, en hebben interfaces nodig om echt nuttig te zijn.
- **Property hooks en abstracte properties** (PHP 8.4), zie het lesplan van OOP 3.

## Samengevat

| Begrip | Vraag die je stelt | Let op |
|---|---|---|
| Polymorphism | Kan deze code met elke soort `Combatant` werken zonder het soort te kennen? | Het type van de parameter is de parent, het gedrag komt van het object zelf |
| Type declaration met een class | Welke objecten accepteert deze parameter? | De class zelf en elke class die ervan erft, anders een `TypeError` |
| Alleen wat de parent heeft | Staat deze method in de parent? | Een method die alleen in een child staat kan een `Error` geven |
| `instanceof` | Is dit object van deze class, of van een child ervan? | Handig voor iets dat alleen een bepaalde child kan, niet om per soort ander gedrag te kiezen |
| `?Combatant` | Mag er ook niets zijn? | `Combatant $x = null` is vanaf PHP 8.4 deprecated |
| Covariant return type | Mag een override iets specifiekers teruggeven? | Alleen een smaller type, nooit ruimer |

## Voorbeeldbestanden

De voorbeelden gebruiken het rollenspel uit de vorige lessen. Run ze met `php bestandsnaam.php` vanuit de map `example/polymorphism`.

1. [without_polymorphism.php](./example/polymorphism/without_polymorphism.php) laat zien hoe het eruit ziet met een functie per soort en met `instanceof` en `elseif`.
2. [polymorphism.php](./example/polymorphism/polymorphism.php) gebruikt `Combatant` als parameter type en in een array, en voegt een `Archer` toe zonder iets te veranderen.
3. [type_limits.php](./example/polymorphism/type_limits.php) laat zien wat PHP weigert: een object van een andere class, een method die alleen een child heeft en `null` bij `?Combatant`.
4. [instanceof.php](./example/polymorphism/instanceof.php) gebruikt `instanceof` en `::class` en laat zien wanneer het wel en niet handig is.
5. [return_types.php](./example/polymorphism/return_types.php) laat zien dat een override een smaller return type mag hebben.

## Labs

Open [index.html](./example/polymorphism/lab/index.html) in je browser, je hoeft niets te installeren. Werkt de opmaak niet, start dan `php -S localhost:8090` in de map `example/polymorphism/lab`.

1. [objects.html](./example/polymorphism/lab/objects.html) is Lab 1. Een step debugger met drie scenario's: één functie die drie soorten `Combatant` aankan, twee `Combatant` objecten die elkaar aanvallen en `instanceof`.
2. [predict_output.html](./example/polymorphism/lab/predict_output.html) is Lab 2. Acht scenario's waarbij je eerst voorspelt wat er gebeurt, zoals welke `attack()` draait, een object van een andere class, een method die alleen een child heeft en `instanceof` bij broers.
3. [bughunt.html](./example/polymorphism/lab/bughunt.html) is Lab 3. Twee scripts die iets anders doen dan bedoeld: een object dat geen `Combatant` is en een method die alleen een `Enemy` heeft.

Met `php verify_lab1.php` tot en met `php verify_lab3.php` (in de map `example/polymorphism`) controleer je dat de antwoorden in de labs kloppen met wat PHP echt doet.

## Opgaven

De opgaven staan in [polymorphism_opgaven_1.md](./polymorphism_opgaven_1.md).
