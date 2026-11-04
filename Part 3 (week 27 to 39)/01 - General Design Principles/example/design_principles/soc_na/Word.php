<?php

declare(strict_types=1);

// deze class bevat alleen de spellogica: geen echo, geen fgets() en geen $_GET
class Word
{
    private string $word;
    private array $guessedLetters = [];

    public function __construct(string $word)
    {
        $this->word = $word;
    }

    // geeft terug of de letter in het woord zit, de aanroeper beslist wat er daarna getoond wordt
    public function guessLetter(string $letter): bool
    {
        $this->guessedLetters[] = $letter;

        return strpos($this->word, $letter) !== false;
    }

    public function getDisplayWord(): string
    {
        $display = '';

        for ($i = 0; $i < strlen($this->word); $i++) {
            $display .= in_array($this->word[$i], $this->guessedLetters) ? $this->word[$i] : '_';
        }

        return $display;
    }

    public function isGuessed(): bool
    {
        return $this->getDisplayWord() === $this->word;
    }
}
