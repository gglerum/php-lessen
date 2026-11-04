<?php

declare(strict_types=1);

// "misschien hebben we dit later nodig", dus we bouwen het alvast
class GameSettings
{
    private int $maxAttempts = 7;
    private string $language = 'nl';
    private string $theme = 'dark';
    private int $timeLimitInSeconds = 0;
    private bool $showHints = false;

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getTimeLimitInSeconds(): int
    {
        return $this->timeLimitInSeconds;
    }

    public function shouldShowHints(): bool
    {
        return $this->showHints;
    }
}

$settings = new GameSettings();

// van de hele class gebruiken we maar één waarde
echo "Je hebt " . $settings->getMaxAttempts() . " pogingen\n";
