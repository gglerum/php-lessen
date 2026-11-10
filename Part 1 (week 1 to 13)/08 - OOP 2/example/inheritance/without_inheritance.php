<?php

declare(strict_types=1);

// Drie classes uit de wereld van ons rollenspel. Kijk goed wat ze gemeen hebben.

class Player
{
    private string $name;
    private int $health;
    private int $armorRating;
    private int $experiencePoints = 0;

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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
    }

    public function getExperiencePoints(): int
    {
        return $this->experiencePoints;
    }
}

class Npc
{
    private string $name;
    private int $health;
    private int $armorRating;
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
        $this->dialogue = $dialogue;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function talk(): string
    {
        return $this->dialogue;
    }
}

class Enemy
{
    private string $name;
    private int $health;
    private int $armorRating;
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
        $this->loot = $loot;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function dropLoot(): string
    {
        return $this->loot;
    }
}

$player = new Player("Aragorn", 100, 5);
$npc = new Npc("Barkeeper Bob", 40, 0, "Welkom in de Rode Draak!");
$enemy = new Enemy("Goblin", 30, 2, "Een roestige dolk");

$player->takeDamage(20);
$npc->takeDamage(20);
$enemy->takeDamage(20);

echo $player->getName() . " heeft nog " . $player->getHealth() . " health\n";
echo $npc->getName() . " heeft nog " . $npc->getHealth() . " health\n";
echo $enemy->getName() . " heeft nog " . $enemy->getHealth() . " health\n";

// Name, health, armorRating, getName(), getHealth() en takeDamage() staan nu drie keer in onze code.
// Moet takeDamage() anders werken? Dan pas je het op drie plekken aan, en vergeet je er een...
