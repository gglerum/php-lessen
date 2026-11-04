<?php

declare(strict_types=1);

// Config is nu een gewone class
class Config
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function get(string $key): int
    {
        return $this->values[$key];
    }
}

class Library
{
    private Config $config;

    // de afhankelijkheid staat in de constructor, dus je ziet meteen wat Library nodig heeft
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function lend(string $title): string
    {
        $days = $this->config->get('loan_days');

        return "$title is uitgeleend voor $days dagen";
    }
}

// we maken één Config aan en geven die door aan wie hem nodig heeft: er is nog steeds maar één,
// maar het is nu onze keuze en geen verplichting van de class zelf
$config = new Config(['loan_days' => 14]);
echo (new Library($config))->lend('Misery') . "\n";

// en nu kunnen we Library ook uitproberen met een andere uitleenduur
echo (new Library(new Config(['loan_days' => 7])))->lend('It') . "\n";
