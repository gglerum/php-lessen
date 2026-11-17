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

    // final: de regels voor schade liggen vast. Geen enkele child mag deze method overschrijven.
    final public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function getHealth(): int
    {
        return $this->health;
    }
}

class Enemy extends Combatant
{
    // Deze method mag wel overschreven worden, want hij is niet final
    public function getHealth(): int
    {
        return parent::getHealth();
    }

    // Dit geeft een Fatal error: "Cannot override final method Combatant::takeDamage()"
    // public function takeDamage(int $damage): void
    // {
    // }
}

// final voor een class: van Dragon kun je niet meer erven
final class Dragon extends Enemy
{
}

// Dit geeft een Fatal error: "Class Elder cannot extend final class Dragon"
// class Elder extends Dragon
// {
// }

$dragon = new Dragon("Smaug", 500, 20);
$dragon->takeDamage(70);

echo $dragon->getHealth() . "\n";
