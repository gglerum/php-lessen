<?php

declare(strict_types=1);

// de bibliothecaris zegt: "een lid leent een boek"
// in de code staat: een User neemt een Item mee, en dat noemen we een record

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

class User
{
    private string $name;
    private array $records = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function takeItem(Item $item): void
    {
        $this->records[] = $item;
    }

    public function countRecords(): int
    {
        return count($this->records);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

$user = new User('Anna');
$user->takeItem(new Item('Misery'));

echo $user->getName() . ' heeft ' . $user->countRecords() . " record(s)\n";

// wie de code leest moet elk woord vertalen: User is een lid, Item is een boek, takeItem is lenen
// en met elke vertaalstap kan een misverstand ontstaan
