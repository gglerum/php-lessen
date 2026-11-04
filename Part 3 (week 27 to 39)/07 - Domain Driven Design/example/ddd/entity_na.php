<?php

declare(strict_types=1);

// een entity heeft een identiteit (id) die nooit verandert
class Member
{
    private int $id;
    private string $name;
    private string $city;

    public function __construct(int $id, string $name, string $city)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }

    public function moveTo(string $city): void
    {
        $this->city = $city;
    }

    // twee leden zijn dezelfde persoon als hun id gelijk is, ook al is de rest anders
    public function equals(Member $other): bool
    {
        return $this->id === $other->id;
    }
}

$annaFromUtrecht = new Member(1, 'Anna', 'Utrecht');
$otherAnna = new Member(2, 'Anna', 'Utrecht');

// zelfde naam en woonplaats, maar twee verschillende mensen
echo $annaFromUtrecht->equals($otherAnna) ? "Gelijk\n" : "Verschillend\n";

// dezelfde persoon die verhuist blijft dezelfde persoon
$before = clone $annaFromUtrecht;
$annaFromUtrecht->moveTo('Amsterdam');

echo $annaFromUtrecht->equals($before) ? "Gelijk\n" : "Verschillend\n";
