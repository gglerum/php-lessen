<?php

declare(strict_types=1);

abstract class Combatant
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // Een abstracte method is public of protected. Abstract en private samen kan niet.
    abstract protected function getAttackBonus(): int;

    // Een abstracte method met een verplichte parameter
    abstract public function attack(int $baseDamage): int;

    public function getName(): string
    {
        return $this->name;
    }
}

class Player extends Combatant
{
    // De abstracte method was protected, de implementatie mag public zijn (ruimer).
    public function getAttackBonus(): int
    {
        return 3;
    }

    // De child mag extra parameters toevoegen, zolang ze optioneel zijn
    public function attack(int $baseDamage, bool $critical = false): int
    {
        $damage = $baseDamage + $this->getAttackBonus();

        return $critical ? $damage * 2 : $damage;
    }
}

// Een abstracte class mag een abstracte method ook doorgeven aan zijn eigen children.
// Boss schrijft attack(), maar getAttackBonus() laat hij open: Boss is dus zelf nog abstract.
abstract class Boss extends Combatant
{
    public function attack(int $baseDamage): int
    {
        return $baseDamage + $this->getAttackBonus() * 2;
    }
}

class Dragon extends Boss
{
    protected function getAttackBonus(): int
    {
        return 5;
    }
}

$player = new Player("Aragorn");
$dragon = new Dragon("Smaug");

echo $player->getName() . ": " . $player->attack(10) . " en kritiek " . $player->attack(10, true) . "\n";
echo $dragon->getName() . ": " . $dragon->attack(10) . "\n";

// Een abstracte class mag ook zonder abstracte methods, om te voorkomen dat iemand er een object van maakt.
// Andersom kan niet: een class met een abstracte method moet zelf abstract zijn.
// class Merchant extends Combatant {}
//   Fatal error: Class Merchant contains 2 abstract methods and must therefore be declared abstract or implement the remaining methods (Combatant::getAttackBonus, Combatant::attack)

// De implementatie moet bij de abstracte method passen. Dit geeft een Fatal error:
// class Cheater extends Combatant {
//     protected function getAttackBonus(): int { return 1; }
//     public function attack(string $baseDamage): int { return 1; }
// }
//   Fatal error: Declaration of Cheater::attack(string $baseDamage): int must be compatible with Combatant::attack(int $baseDamage): int
