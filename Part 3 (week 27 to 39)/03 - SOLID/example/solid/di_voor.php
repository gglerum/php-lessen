<?php

declare(strict_types=1);

class Words
{
    public function get(): array
    {
        return ['apple', 'banana', 'cherry'];
    }
}

class LongestWordPicker
{
    private array $words;

    public function __construct()
    {
        // de class maakt zijn eigen woordenlijst aan, dus we kunnen er van buiten niets aan veranderen
        $words = new Words();
        $this->words = $words->get();
    }

    public function pick(): string
    {
        $longest = '';

        foreach ($this->words as $word) {
            if (strlen($word) > strlen($longest)) {
                $longest = $word;
            }
        }

        return $longest;
    }
}

// we kunnen deze class alleen gebruiken met precies deze woordenlijst
$picker = new LongestWordPicker();
echo $picker->pick() . "\n";
