<?php

declare(strict_types=1);

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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
    }
}

class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    // Override, versie 1: dezelfde naam en parameters als in Combatant, dus deze method vervangt de method van de parent.
    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
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

    // Override, versie 2: we vervangen niet alles, we breiden uit.
    // Met parent::describe() gebruiken we de method van Combatant en voegen we er iets aan toe.
    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    // Ook takeDamage() kunnen we uitbreiden. Het rekenwerk laten we aan de parent.
    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }
}

$combatant = new Combatant("Een of andere vechter", 50, 0);
$npc = new Npc("Barkeeper Bob", 40, 0, "Welkom in de Rode Draak!");
$enemy = new Enemy("Goblin", 30, 2, "een roestige dolk");

echo $combatant->describe() . "\n";
echo $npc->describe() . "\n";
echo $enemy->describe() . "\n";

$enemy->takeDamage(10);
echo $enemy->describe() . "\n";

$enemy->takeDamage(50);
