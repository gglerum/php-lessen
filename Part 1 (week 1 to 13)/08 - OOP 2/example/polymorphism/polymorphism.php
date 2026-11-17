<?php

declare(strict_types=1);

// Combatant, Player, Npc en Enemy zijn gelijk aan die van de vorige les (met een getLoot() voor Enemy).
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

// Het parameter type is de PARENT. Elke Player, Npc en Enemy is ook een Combatant, dus alle drie mogen erin.
function announce(Combatant $combatant): void
{
    echo $combatant->getName() . " valt aan voor " . $combatant->attack() . " schade\n";
}

announce(new Player("Aragorn", 100, 5));
announce(new Npc("Barkeeper Bob", 40, 0));
announce(new Enemy("Goblin", 30, 2, "een dolk", 3));

echo "\n";

// Ook in een array kunnen verschillende soorten naast elkaar staan.
// Bij elke aanroep van attack() kijkt PHP naar het object zelf, niet naar het type van de variabele.
$combatants = [
    new Player("Aragorn", 100, 5),
    new Npc("Barkeeper Bob", 40, 0),
    new Enemy("Goblin", 30, 2, "een dolk", 3),
];

$total = 0;
foreach ($combatants as $combatant) {
    // $combatant::class is de naam van de echte class van het object
    echo $combatant::class . ": " . $combatant->attack() . "\n";
    $total = $total + $combatant->attack();
}
echo "Samen: " . $total . "\n";

echo "\n";

// Een nieuwe soort Combatant erbij: schrijf een class die van Combatant erft.
// announce() kent Archer niet, maar hoeft ook niet te veranderen.
class Archer extends Combatant
{
    public function attack(): int
    {
        return 7;
    }
}

announce(new Archer("Legolas", 80, 3));
