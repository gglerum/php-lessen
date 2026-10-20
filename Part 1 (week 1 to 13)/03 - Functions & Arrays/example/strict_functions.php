<?php

declare(strict_types=1);
// variabelen buiten functie scope

// persoon 1
$person1 = [
    "firstName" => "John",
    "lastName" => "Doe",
    "street" => "Main Street",
    "houseNumber" => "123",
    "city" => "New York",
    "zipCode" => "10001",
    "country" => "USA"
];

// persoon 2
$person2 = [
    "firstName" => "Jane",
    "lastName" => "Smith",
    "street" => "Second Street",
    "houseNumber" => 456,
    "city" => "Los Angeles",
    "zipCode" => "90001",
    "country" => "USA"
];

// functie aanroep met parameters
function printShippingLabel(array $person): string
{
    return $person["firstName"] . " " . $person["lastName"] . "\n" .
        $person["street"] . " " . $person["houseNumber"] . "\n" .
        $person["city"] . " " . $person["zipCode"] . "\n" .
        $person["country"] . "\n";
}

function printShippingLabelWithUseOfSpreadOperator(string $firstName, string $lastName, string $street, int $houseNumber, string $city, string $zipCode, string $country): string
{
    return $firstName . " " . $lastName . "\n" .
        $street . " " . $houseNumber . "\n" .
        $city . " " . $zipCode . "\n" .
        $country . "\n";
}

// functie aanroep zonder parameters
function printMenu(): void
{
    global $person1, $person2;
    echo "1. Print Shipping Label for person 1\n";
    echo "2. Print Shipping Label for person 2\n";
    echo "q. Quit\n";

    $input = trim(readline("Choose an option: "));

    echo match ($input) {
        "1" => printShippingLabel($person1),
        "2" => printShippingLabelWithUseOfSpreadOperator(...$person2),
        "q" => die("Exiting...\n"),
        default => "Invalid option\n",
    };
}

while (true) {
    printMenu();
}
