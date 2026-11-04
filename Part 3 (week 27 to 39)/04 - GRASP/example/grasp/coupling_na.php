<?php

declare(strict_types=1);

class Address
{
    private string $city;

    public function __construct(string $city)
    {
        $this->city = $city;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}

class Member
{
    private string $name;
    private Address $address;

    public function __construct(string $name, Address $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Member weet zelf waar hij woont, wie hem gebruikt hoeft Address niet te kennen
    public function getCity(): string
    {
        return $this->address->getCity();
    }
}

class Library
{
    private array $members;

    public function __construct(array $members)
    {
        $this->members = $members;
    }

    public function getFirstMember(): Member
    {
        return $this->members[0];
    }
}

$library = new Library([new Member('Anna', new Address('Utrecht'))]);

// dit script kent nu alleen Library en Member, hoe Member zijn woonplaats bijhoudt is zijn eigen zaak
$member = $library->getFirstMember();
echo $member->getName() . ' woont in ' . $member->getCity() . "\n";
