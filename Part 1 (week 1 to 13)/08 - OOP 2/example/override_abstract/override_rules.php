<?php

declare(strict_types=1);

// Wat mag een child veranderen aan de method van zijn parent? Alles wat hieronder staat werkt.
// De regels die in een comment staan geven een Fatal error, met de melding van PHP erachter.

class Item
{
}

class Potion extends Item
{
}

class Combatant
{
    protected int $health = 100;

    public function use(Potion $potion): void
    {
        echo "Combatant gebruikt een Potion\n";
    }

    public function dropLoot(): Item
    {
        return new Item();
    }

    public function rest(int $hours): void
    {
        echo "Combatant rust " . $hours . " uur\n";
    }

    protected function shout(): string
    {
        return "Aaaah!";
    }

    public function describe()
    {
        return "Combatant";
    }
}

class Enemy extends Combatant
{
    // Parametertype: ruimer mag. Elke Potion is ook een Item, dus alles wat de parent accepteert, accepteert Enemy ook.
    public function use(Item $item): void
    {
        echo "Enemy gebruikt een Item\n";
    }

    // Return type: smaller mag. Een Potion is een Item, dus wie een Item verwacht krijgt nog steeds een Item.
    public function dropLoot(): Potion
    {
        return new Potion();
    }

    // Extra parameters: alleen als ze optioneel zijn. Wie rest(8) aanroept, werkt nog steeds.
    public function rest(int $hours, bool $deep = false): void
    {
        echo "Enemy rust " . $hours . " uur" . ($deep ? " (diep)" : "") . "\n";
    }

    // Zichtbaarheid: ruimer mag. protected wordt public.
    public function shout(): string
    {
        return "Grrr!";
    }

    // Een return type toevoegen mag, als de parent er geen had.
    public function describe(): string
    {
        return "Enemy";
    }
}

$enemy = new Enemy();
$enemy->use(new Potion());
$enemy->use(new Item());
$enemy->rest(8);
$enemy->rest(8, true);
echo get_class($enemy->dropLoot()) . "\n";
echo $enemy->shout() . "\n";
echo $enemy->describe() . "\n";

// Dit mag niet:
//
// Een extra verplichte parameter.
//   Enemy::rest(int $hours, bool $deep)
//   Fatal error: Declaration of Enemy::rest(int $hours, bool $deep) must be compatible with Combatant::rest(int $hours)
//
// Minder parameters.
//   Enemy::rest()
//   Fatal error: Declaration of Enemy::rest() must be compatible with Combatant::rest(int $hours)
//
// Een smaller parametertype.
//   Enemy::use(HealingPotion $potion), een class die van Potion erft, terwijl de parent Potion accepteert
//   Fatal error: Declaration of Enemy::use(HealingPotion $potion) must be compatible with Combatant::use(Potion $potion)
//
// Een ruimer return type.
//   Enemy::dropLoot(): Item terwijl de parent Potion teruggeeft
//   Fatal error: Declaration of Enemy::dropLoot(): Item must be compatible with Combatant::dropLoot(): Potion
//
// Een return type weglaten als de parent er een heeft.
//   Fatal error: Declaration of Enemy::dropLoot() must be compatible with Combatant::dropLoot(): Item
//
// Minder zichtbaar.
//   protected function describe() terwijl de parent public heeft
//   Fatal error: Access level to Enemy::describe() must be public (as in class Combatant)
//
// Een niet-static method static maken.
//   Fatal error: Cannot make non static method Combatant::describe() static in class Enemy

// Ook properties kun je in een child opnieuw declareren, met strengere regels dan bij methods.
class Weapon
{
    protected int $damage = 5;
    public int $durability = 100;
}

class Sword extends Weapon
{
    // Een andere standaardwaarde mag, en ruimer zichtbaar ook (protected wordt public)
    public int $damage = 8;

    // Het type mag niet veranderen. public string $durability geeft:
    // Fatal error: Type of Sword::$durability must be int (as in class Weapon)
    public int $durability = 50;
}

$sword = new Sword();
echo $sword->damage . " en " . $sword->durability . "\n";
