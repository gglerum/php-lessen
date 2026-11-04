<?php

declare(strict_types=1);

class Member
{
    private string $name;
    private string $city;

    public function __construct(string $name, string $city)
    {
        $this->name = $name;
        $this->city = $city;
    }

    public function moveTo(string $city): void
    {
        $this->city = $city;
    }
}

// twee verschillende mensen met dezelfde naam en woonplaats
$annaFromUtrecht = new Member('Anna', 'Utrecht');
$otherAnna = new Member('Anna', 'Utrecht');

// PHP vergelijkt de waarden van de properties, dus ze zijn "gelijk"
echo $annaFromUtrecht == $otherAnna ? "Gelijk\n" : "Verschillend\n";

// en dezelfde persoon die verhuist is ineens niet meer gelijk aan zichzelf
$before = clone $annaFromUtrecht;
$annaFromUtrecht->moveTo('Amsterdam');

echo $annaFromUtrecht == $before ? "Gelijk\n" : "Verschillend\n";

// zonder identiteit weten we niet wie wie is
