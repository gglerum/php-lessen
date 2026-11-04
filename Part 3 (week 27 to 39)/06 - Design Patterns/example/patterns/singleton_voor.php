<?php

declare(strict_types=1);

// een Singleton: er bestaat maar één Config, en iedereen haalt hem zelf op
class Config
{
    private static ?Config $instance = null;

    private array $values = ['loan_days' => 14];

    // de private constructor zorgt dat niemand zelf een Config kan aanmaken
    private function __construct()
    {
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function get(string $key): int
    {
        return $this->values[$key];
    }
}

class Library
{
    public function lend(string $title): string
    {
        // de afhankelijkheid zit verstopt in de method: aan de constructor zie je niet dat Library een Config nodig heeft
        $days = Config::getInstance()->get('loan_days');

        return "$title is uitgeleend voor $days dagen";
    }
}

echo (new Library())->lend('Misery') . "\n";

// we kunnen niet testen wat er gebeurt met een uitleenduur van 7 dagen,
// want Library kiest zelf welke Config hij gebruikt
