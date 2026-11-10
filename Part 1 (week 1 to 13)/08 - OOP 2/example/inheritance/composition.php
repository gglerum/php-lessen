<?php

declare(strict_types=1);

// Een Player "is een" Combatant: dat is inheritance.
// Een Player "heeft een" Inventory: dat is compositie. Het object bevat een ander object.

class Inventory
{
    private int $maxItems;
    private array $items = [];

    public function __construct(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }

    public function add(string $item): void
    {
        if (count($this->items) >= $this->maxItems) {
            echo "Je rugzak is vol, " . $item . " blijft liggen\n";
            return;
        }

        $this->items[] = $item;
    }

    public function list(): string
    {
        return implode(", ", $this->items);
    }
}

class Combatant
{
    protected string $name;
    protected int $health;

    public function __construct(string $name, int $health)
    {
        $this->name = $name;
        $this->health = $health;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }
}

class Player extends Combatant
{
    private Inventory $inventory;

    // Net als bij Hangman geven we het Inventory object mee aan de constructor (dependency injection)
    public function __construct(string $name, int $health, Inventory $inventory)
    {
        parent::__construct($name, $health);
        $this->inventory = $inventory;
    }

    public function pickUp(string $item): void
    {
        // Player stuurt het werk door naar zijn Inventory
        $this->inventory->add($item);
    }

    public function showInventory(): string
    {
        return $this->getName() . " heeft: " . $this->inventory->list();
    }
}

class Enemy extends Combatant
{
}

// Een Dungeon is geen Enemy en geen lijst, maar heeft meerdere Enemy objecten
class Dungeon
{
    private string $name;
    private array $enemies = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addEnemy(Enemy $enemy): void
    {
        $this->enemies[] = $enemy;
    }

    public function describe(): string
    {
        $names = [];
        foreach ($this->enemies as $enemy) {
            $names[] = $enemy->getName();
        }

        return $this->name . " bevat: " . implode(", ", $names);
    }
}

$player = new Player("Aragorn", 100, new Inventory(2));
$player->pickUp("Zwaard");
$player->pickUp("Fakkel");
$player->pickUp("Touw");

echo $player->showInventory() . "\n";

$dungeon = new Dungeon("De Goblin Grot");
$dungeon->addEnemy(new Enemy("Goblin", 30));
$dungeon->addEnemy(new Enemy("Orc", 60));

echo $dungeon->describe() . "\n";

// Zo zou het er met inheritance uitzien, en dat klopt niet: een Player is geen Inventory.
// class Player extends Inventory { ... }
// Dan kan een Player ineens add() en list() gebruiken alsof het zelf een rugzak is.
