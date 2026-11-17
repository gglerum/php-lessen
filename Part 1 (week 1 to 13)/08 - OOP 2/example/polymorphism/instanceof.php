<?php

declare(strict_types=1);

abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

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
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$player = new Player("Aragorn", 100, 5);
$enemy = new Enemy("Goblin", 30, 2, "een dolk", 3);

// instanceof geeft true als het object van die class is, van een class die ervan erft,
// en geeft anders false. Het geeft nooit een foutmelding.
var_dump($player instanceof Player);     // true
var_dump($player instanceof Combatant);  // true: een Player is ook een Combatant
var_dump($player instanceof Enemy);      // false: Player en Enemy zijn broers, geen van beide erft van de ander
var_dump($enemy instanceof Combatant);   // true
var_dump(null instanceof Combatant);     // false: null is geen object

// Met ! draai je het om. instanceof gaat voor !, dus haakjes zijn niet nodig.
var_dump(!$player instanceof Enemy);     // true

echo "\n";

// Een echte class en een parent zijn dus niet hetzelfde. ::class geeft de echte class.
echo $player::class . "\n";              // Player
echo Combatant::class . "\n";            // Combatant

echo "\n";

// Wanneer is instanceof handig? Als je iets wilt doen dat alleen een bepaalde child kan.
// getLoot() bestaat alleen in Enemy. Een Player zou een Error geven, dus we controleren eerst.
function showLoot(Combatant $combatant): void
{
    if ($combatant instanceof Enemy) {
        echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\n";
    } else {
        echo $combatant->getName() . " heeft niets bij zich\n";
    }
}

showLoot($enemy);
showLoot($player);

// Let op: gebruik instanceof niet om te kiezen WAT er gebeurt per soort (zoals in without_polymorphism.php).
// Dat is precies wat een method per child (zoals attack()) voor je oplost.
