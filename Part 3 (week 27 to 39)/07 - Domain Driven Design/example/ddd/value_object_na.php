<?php

declare(strict_types=1);

// een value object heeft geen id en verandert niet: twee ISBN's met dezelfde waarde zijn hetzelfde ISBN
class Isbn
{
    private readonly string $value;

    // een ongeldig ISBN kan niet bestaan, de controle gebeurt één keer bij het aanmaken
    public function __construct(string $value)
    {
        $digits = str_replace('-', '', $value);

        if (!$this->hasValidChecksum($digits)) {
            throw new InvalidArgumentException("Ongeldig ISBN: $value");
        }

        $this->value = $digits;
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }

    public function format(): string
    {
        return $this->value;
    }

    // een ISBN-13 is geldig als de gewogen som van de cijfers deelbaar is door 10
    private function hasValidChecksum(string $digits): bool
    {
        if (!preg_match('/^[0-9]{13}$/', $digits)) {
            return false;
        }

        $sum = 0;

        for ($i = 0; $i < 13; $i++) {
            $sum += (int)$digits[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return $sum % 10 === 0;
    }
}

function registerBook(string $title, Isbn $isbn): string
{
    // het ISBN is al gecontroleerd, hier hoeft niets meer te gebeuren
    return "$title is geregistreerd met ISBN " . $isbn->format();
}

echo registerBook('Misery', new Isbn('978-0-306-40615-7')) . "\n";

try {
    registerBook('Misery', new Isbn('12345'));
} catch (InvalidArgumentException $exception) {
    echo $exception->getMessage() . "\n";
}

// de titel meegeven als ISBN kan niet meer: PHP geeft een TypeError
// twee ISBN's met dezelfde waarde zijn gelijk, ook als het twee objecten zijn
echo (new Isbn('978-0-306-40615-7'))->equals(new Isbn('9780306406157')) ? "Gelijk\n" : "Verschillend\n";
