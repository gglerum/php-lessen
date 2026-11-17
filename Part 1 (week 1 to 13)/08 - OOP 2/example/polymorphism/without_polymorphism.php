<?php

declare(strict_types=1);

// Dit voorbeeld laat zien hoe het eruitziet als je een parent NIET als type gebruikt.
// Combatant, Player, Npc en Enemy zijn verder gelijk aan die van de vorige les.
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

    public function getName(): string
    {
        return $this->name;
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

// Zonder polymorphism: één functie per soort Combatant, met steeds dezelfde code
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

announcePlayer(new Player("Aragorn", 100, 5));
announceNpc(new Npc("Barkeeper Bob", 40, 0));
announceEnemy(new Enemy("Goblin", 30, 2, 3));

// Of één functie die zelf kijkt wat voor soort Combatant het is.
// Elke keer dat er een soort bijkomt (Archer, Dragon, ...), moet deze functie worden aangepast.
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

announceAny(new Enemy("Orc", 50, 3, 5));
