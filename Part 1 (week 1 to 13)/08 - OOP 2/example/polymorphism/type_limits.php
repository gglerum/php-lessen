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

class Potion
{
}

function announce(Combatant $combatant): void
{
    echo $combatant->getName() . " valt aan voor " . $combatant->attack() . " schade\n";
}

// Wat mag er in een parameter van het type Combatant? Alles wat van Combatant erft.
announce(new Enemy("Goblin", 30, 2, "een dolk", 3));

// Een Potion is geen Combatant. PHP controleert dit op het moment van aanroepen.
try {
    announce(new Potion());
} catch (TypeError $error) {
    echo "TypeError: " . $error->getMessage() . "\n";
}

echo "\n";

// Binnen showLoot() weet PHP alleen zeker dat het om een Combatant gaat, dus gebruik je alleen
// wat Combatant heeft. getLoot() staat alleen in Enemy. Bij een Player bestaat hij niet.
function showLoot(Combatant $combatant): void
{
    echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\n";
}

showLoot(new Enemy("Goblin", 30, 2, "een dolk", 3));

try {
    showLoot(new Player("Aragorn", 100, 5));
} catch (Error $error) {
    echo "Error: " . $error->getMessage() . "\n";
}

echo "\n";

// Mag er ook niets in? Zet dan een vraagteken voor het type: ?Combatant is "een Combatant of null".
function describeTarget(?Combatant $target): string
{
    if ($target === null) {
        return "Er is geen doelwit";
    }

    return "Doelwit: " . $target->getName();
}

echo describeTarget(new Npc("Barkeeper Bob", 40, 0)) . "\n";
echo describeTarget(null) . "\n";
