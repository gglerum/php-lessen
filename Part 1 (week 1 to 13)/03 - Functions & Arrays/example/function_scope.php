<?php

// variabelen buiten functie scope

// persoon 1
$firstName = "John";
$lastName = "Doe";
$street = "Main Street";
$houseNumber = "123";
$city = "New York";
$zipCode = "10001";
$country = "USA";

// persoon 2
$firstName2 = "Jane";
$lastName2 = "Smith";
$street2 = "Second Street";
$houseNumber = "456";
$city2 = "Los Angeles";
$zipCode2 = "90001";
$country2 = "USA";


// functie aanroep met parameters
function printShippingLabel($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country)
{
    return $firstName . " " . $lastName . "\n" .
        $street . " " . $houseNumber . "\n" .
        $city . " " . $zipCode . "\n" .
        $country . "\n";
}

// Deze methode probeert de verzendlabel te printen zonder parameters, wat zal resulteren in een fout.
// de variabelen zijn niet beschikbaar binnen deze functie zonder global.
function printShippingLabelWithoutParameters()
{
    return printShippingLabel($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country);
}

function printShippingLabelWithoutParametersWithGlobal()
{
    global $firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country;
    return printShippingLabel($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country);
}

// functie aanroep zonder parameters
function printMenu($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country)
{
    echo "1. Print Shipping Label\n";
    echo "2. Print Shipping Label for Person 2\n";
    echo "3. Print Shipping Label Without Parameters\n";
    echo "q. Quit\n";

    $input = trim(readline("Choose an option: "));

    echo match ($input) {
        "1" => printShippingLabel($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country),
        // Deze variabelen zijn  niet global en niet beschikbaar binnen deze functie.
        // Deze regel zal een fout veroorzaken
        "2" => printShippingLabel($firstName2, $lastName2, $street2, $houseNumber, $city2, $zipCode2, $country2),
        "3" => printShippingLabelWithoutParameters(),
        "q" => die("Exiting...\n"),
        default => "Invalid option\n",
    };
}

while (true) {
    printMenu($firstName, $lastName, $street, $houseNumber, $city, $zipCode, $country);
}
