<?php

declare(strict_types=1);

// het ISBN is gewoon een string, en iedereen die het gebruikt moet zelf controleren of het klopt
function registerBook(string $title, string $isbn): string
{
    // de controle staat hier ...
    if (strlen(str_replace('-', '', $isbn)) !== 13) {
        return 'Ongeldig ISBN';
    }

    return "$title is geregistreerd met ISBN $isbn";
}

function printLabel(string $isbn): string
{
    // ... en hier zou dezelfde controle opnieuw moeten staan, maar die is vergeten
    return "ISBN: $isbn";
}

echo registerBook('Misery', '978-0-306-40615-7') . "\n";
echo registerBook('Misery', '12345') . "\n";

// een string is een string, dus per ongeluk de titel meegeven wordt niet opgemerkt
echo printLabel('Misery') . "\n";
