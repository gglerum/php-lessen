<?php

declare(strict_types=1);

// Polymorphism werkt ook bij wat een method teruggeeft.
class Item
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class Potion extends Item
{
    public function heal(): int
    {
        return 25;
    }
}

class Combatant
{
    // Elke Combatant laat bij zijn dood een Item achter
    public function dropLoot(): Item
    {
        return new Item("niets");
    }
}

class Enemy extends Combatant
{
    // Een override mag een smaller return type hebben (covariantie, zie vorige les).
    // Wie een Item verwacht krijgt nog steeds een Item, want een Potion is er een.
    public function dropLoot(): Potion
    {
        return new Potion("een helingsdrank");
    }
}

// collect() weet alleen dat dropLoot() een Item teruggeeft
function collect(Combatant $combatant): void
{
    $item = $combatant->dropLoot();
    echo "Je vindt " . $item->getName() . " (" . $item::class . ")\n";
}

collect(new Combatant());
collect(new Enemy());

// Op een Enemy weet PHP dat dropLoot() een Potion teruggeeft, dus heal() mag hier wel.
// In collect() kon dat niet: daar is het resultaat alleen als Item bekend.
$loot = (new Enemy())->dropLoot();
echo "Deze drank geneest " . $loot->heal() . " health\n";
