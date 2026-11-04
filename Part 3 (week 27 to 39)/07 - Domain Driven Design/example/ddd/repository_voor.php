<?php

declare(strict_types=1);

// het domein weet hoe en waar het wordt opgeslagen
class Library
{
    // de opslag zit in de class zelf, hier gesimuleerd met een static array
    private static array $storage = [];

    private array $loans = [];

    public function lend(string $title, string $member): void
    {
        $this->loans[] = [$title, $member];
    }

    // een Library die zichzelf opslaat en laadt is vast aan één manier van opslaan
    public function save(): void
    {
        self::$storage = $this->loans;
    }

    public static function load(): Library
    {
        $library = new Library();
        $library->loans = self::$storage;

        return $library;
    }

    public function count(): int
    {
        return count($this->loans);
    }
}

$library = new Library();
$library->lend('Misery', 'Anna');
$library->save();

echo Library::load()->count() . " uitlening(en) geladen\n";

// willen we een database in plaats van een array, dan moeten we de Library zelf aanpassen
