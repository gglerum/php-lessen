<?php

declare(strict_types=1);

// Dit is het diagram "met inheritance": elke class heeft alleen wat er nog niet in zijn parent staat.

class Entity
{
    protected string $name;
    protected int $rank;

    public function __construct(string $name, int $rank)
    {
        $this->name = $name;
        $this->rank = $rank;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

// Een Participant doet mee aan het spel en gooit dus met een dobbelsteen
class Participant extends Entity
{
    protected int $dice;

    public function __construct(string $name, int $rank, int $dice)
    {
        parent::__construct($name, $rank);
        $this->dice = $dice;
    }

    public function rollDice(): int
    {
        return random_int(1, $this->dice);
    }
}

class DungeonMaster extends Participant
{
    private string $campaignNotes;

    public function __construct(string $name, int $rank, int $dice, string $campaignNotes)
    {
        parent::__construct($name, $rank, $dice);
        $this->campaignNotes = $campaignNotes;
    }

    public function triggerEvent(): string
    {
        return $this->name . " leest voor: " . $this->campaignNotes;
    }
}

class Combatant extends Participant
{
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $rank, int $dice, int $health, int $armorRating)
    {
        parent::__construct($name, $rank, $dice);
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

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
        $this->rank = $this->rank + 1;
    }

    public function getRank(): int
    {
        return $this->rank;
    }
}

class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $rank, int $dice, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $rank, $dice, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function talk(): string
    {
        return $this->dialogue;
    }
}

class Enemy extends Combatant
{
    private int $threatLevel;

    public function __construct(string $name, int $rank, int $dice, int $health, int $armorRating, int $threatLevel)
    {
        parent::__construct($name, $rank, $dice, $health, $armorRating);
        $this->threatLevel = $threatLevel;
    }

    public function getThreatLevel(): int
    {
        return $this->threatLevel;
    }
}

// De andere tak van de boom: plekken in plaats van personen
class Location extends Entity
{
    protected int $size;

    public function __construct(string $name, int $rank, int $size)
    {
        parent::__construct($name, $rank);
        $this->size = $size;
    }

    public function enter(): string
    {
        return "Je betreedt " . $this->name . " (grootte " . $this->size . ")";
    }
}

// Town en Dungeon hebben geen eigen properties in dit voorbeeld, dus ook geen eigen constructor nodig
class Town extends Location
{
    public function restParty(): string
    {
        return "Je rust uit in " . $this->name;
    }
}

class Dungeon extends Location
{
    public function triggerTrap(): string
    {
        return "Klik! Een val in " . $this->name;
    }
}

$player = new Player("Aragorn", 1, 20, 100, 5);
$enemy = new Enemy("Goblin", 1, 6, 30, 2, 3);
$npc = new Npc("Barkeeper Bob", 1, 4, 40, 0, "Welkom in de Rode Draak!");
$dungeonMaster = new DungeonMaster("Gandalf", 10, 20, "Een goblin wacht in de grot");
$town = new Town("Bree", 1, 50);
$dungeon = new Dungeon("De Goblin Grot", 2, 20);

// Player gebruikt methods uit vier verschillende classes
echo $player->getName() . "\n";                             // uit Entity
echo "Gegooid: " . $player->rollDice() . " (van 20)\n";     // uit Participant, een getal tussen 1 en 20
$player->takeDamage(15);                                    // uit Combatant
echo "Health: " . $player->getHealth() . "\n";
$player->levelUp();                                         // uit Player zelf
echo "Rank: " . $player->getRank() . "\n";

echo $npc->talk() . "\n";
echo "Dreiging van " . $enemy->getName() . ": " . $enemy->getThreatLevel() . "\n";
echo $dungeonMaster->triggerEvent() . "\n";

// Een Town heeft geen dobbelstenen en geen health: dat zit in de andere tak van de boom
echo $town->enter() . "\n";
echo $town->restParty() . "\n";
echo $dungeon->enter() . "\n";
echo $dungeon->triggerTrap() . "\n";
