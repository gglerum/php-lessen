<?php

declare(strict_types=1);

// abstract: van Combatant kun je geen object maken, je kunt er alleen van erven.
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

    // Een gewone method: werkt voor iedereen hetzelfde, dus staat hij hier met een body
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

    // Een abstracte method: elke Combatant kan aanvallen, maar hoe verschilt per soort.
    // Er staat geen body, alleen de naam, de parameters en het return type.
    // Elke class die erft moet deze method zelf schrijven, anders geeft PHP een Fatal error.
    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        // een speler wordt sterker met ervaring
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        // een dorpeling kan zich nauwelijks verweren
        return 1;
    }
}

class Enemy extends Combatant
{
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->threatLevel = $threatLevel;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$player = new Player("Aragorn", 100, 5);
$npc = new Npc("Barkeeper Bob", 40, 0);
$enemy = new Enemy("Goblin", 30, 2, 3);

echo $player->getName() . " valt aan voor " . $player->attack() . " schade\n";
echo $npc->getName() . " valt aan voor " . $npc->attack() . " schade\n";
echo $enemy->getName() . " valt aan voor " . $enemy->attack() . " schade\n";

// takeDamage() komt van de abstracte parent en werkt voor alle drie
$player->takeDamage($enemy->attack());
echo $player->getName() . " heeft nog " . $player->getHealth() . " health\n";

// Dit geeft een Error: "Cannot instantiate abstract class Combatant"
// $combatant = new Combatant("Onbekend", 10, 0);

// En dit geeft een Fatal error: "Class Merchant contains 1 abstract method and must therefore be declared abstract or implement the remaining method (Combatant::attack)"
// class Merchant extends Combatant {}
