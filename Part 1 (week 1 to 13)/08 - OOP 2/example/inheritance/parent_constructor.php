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
}

class Npc extends Combatant
{
    private string $dialogue;

    // Npc heeft een extra argument nodig, dus Npc krijgt een eigen constructor.
    // Een eigen constructor vervangt die van de parent, de parent constructor wordt dan niet meer vanzelf aangeroepen.
    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        // parent:: roept de method aan zoals hij in Combatant staat. Zo hoeven we niets te kopiëren.
        parent::__construct($name, $health, $armorRating);

        // en dan alleen wat Npc zelf erbij heeft
        $this->dialogue = $dialogue;
    }

    public function talk(): string
    {
        return $this->dialogue;
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

    public function dropLoot(): string
    {
        return $this->loot;
    }
}

$npc = new Npc("Barkeeper Bob", 40, 0, "Welkom in de Rode Draak!");
$enemy = new Enemy("Goblin", 30, 2, "Een roestige dolk");

$enemy->takeDamage(20);

echo $npc->getName() . " zegt: " . $npc->talk() . "\n";
echo $enemy->getName() . " heeft nog " . $enemy->getHealth() . " health en laat achter: " . $enemy->dropLoot() . "\n";

// Vergeet je parent::__construct(), dan worden name, health en armorRating nooit gezet.
// Een getName() geeft dan een Error: "must not be accessed before initialization".
