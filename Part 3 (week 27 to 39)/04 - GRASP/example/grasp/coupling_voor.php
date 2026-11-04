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

    public function getAddress(): Address
    {
        return $this->address;
    }
}

class Library
{
    private array $members;

    public function __construct(array $members)
    {
        $this->members = $members;
    }

    public function getMembers(): array
    {
        return $this->members;
    }
}

$library = new Library([new Member('Anna', new Address('Utrecht'))]);

// dit script kent Library, Member en Address, en hoe ze met elkaar verbonden zijn
// verandert Member zijn Address (bijvoorbeeld in een lijst met meerdere adressen), dan breekt deze regel
echo $library->getMembers()[0]->getName() . ' woont in ' . $library->getMembers()[0]->getAddress()->getCity() . "\n";
