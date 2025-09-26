<?php

namespace Hacklabfrl\Hangman\Generator;

/**
 * Generates a random word from a given source.
 */
class RandomWordGenerator
{
    private $words;

    public function __construct(WordSource $source)
    {
        $this->words = $source->get();
    }

    /**
     * Gets a random word from a given source
     * @return string
     */
    public function get(): string
    {
        return $this->words[rand(0, count($this->words) - 1)];
    }
}
