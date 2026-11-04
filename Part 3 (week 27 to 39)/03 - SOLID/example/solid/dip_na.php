<?php

declare(strict_types=1);

// de picker vraagt om een afspraak (de interface), niet om een concrete class
interface WordSource
{
    public function get(): array;
}

class ArrayWords implements WordSource
{
    public function get(): array
    {
        return ['apple', 'banana', 'cherry'];
    }
}

// in hangman_v4 doet deze class een query op de database, hier simuleren we dat met een array
class DatabaseWords implements WordSource
{
    public function get(): array
    {
        $rowsFromDatabase = [['word' => 'kiwi'], ['word' => 'watermelon']];

        return array_column($rowsFromDatabase, 'word');
    }
}

class LongestWordPicker
{
    private WordSource $source;

    public function __construct(WordSource $source)
    {
        $this->source = $source;
    }

    public function pick(): string
    {
        $longest = '';

        foreach ($this->source->get() as $word) {
            if (strlen($word) > strlen($longest)) {
                $longest = $word;
            }
        }

        return $longest;
    }
}

// de picker is niet aangepast, alleen degene die hem aanmaakt kiest een andere bron
echo (new LongestWordPicker(new ArrayWords()))->pick() . "\n";
echo (new LongestWordPicker(new DatabaseWords()))->pick() . "\n";
