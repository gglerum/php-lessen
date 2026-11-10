<?php

declare(strict_types=1);

// Alles wat Player, Npc en Enemy gemeen hebben verhuizen we naar één parent class.
class Combatant
{
    // protected: de class zelf en alle classes die ervan erven mogen erbij, de buitenwereld niet
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

// extends: Player is een Combatant en krijgt alle properties en methods van Combatant erbij.
// De constructor hoeven we niet te herhalen: die wordt ook geërfd.
class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
    }

    public function getExperiencePoints(): int
    {
        return $this->experiencePoints;
    }

    public function describe(): string
    {
        // $this->name en $this->health staan niet in Player, maar wel in Combatant
        return $this->name . " heeft " . $this->health . " health en " . $this->experiencePoints . " ervaringspunten";
    }
}

$player = new Player("Aragorn", 100, 5);

// takeDamage() en getName() schreven we niet in Player, maar zijn er wel
$player->takeDamage(20);
$player->levelUp();

echo $player->getName() . " heeft nog " . $player->getHealth() . " health\n";
echo $player->describe() . "\n";

// protected is voor de buitenwereld hetzelfde als private. Dit geeft een Error:
// echo $player->health;

// Zou health in Combatant private zijn, dan kan ook Player er niet bij.
// $this->health in describe() geeft dan "Warning: Undefined property: Player::$health".
