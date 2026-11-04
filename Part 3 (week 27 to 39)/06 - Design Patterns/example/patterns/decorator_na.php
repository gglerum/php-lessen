<?php

declare(strict_types=1);

interface WordSource
{
    public function get(): array;
}

class ArrayWords implements WordSource
{
    public function get(): array
    {
        return ['fig', 'apple', 'kiwi', 'banana', 'date'];
    }
}

class DatabaseWords implements WordSource
{
    public function get(): array
    {
        return ['pear', 'watermelon', 'lime', 'cherry'];
    }
}

// de decorator is zelf een WordSource en pakt een andere WordSource in
class LongWordsOnly implements WordSource
{
    private WordSource $source;

    public function __construct(WordSource $source)
    {
        $this->source = $source;
    }

    public function get(): array
    {
        return array_values(array_filter($this->source->get(), fn(string $word) => strlen($word) >= 5));
    }
}

// de decorator werkt met elke bron, we hebben maar één extra class nodig
echo implode(', ', (new LongWordsOnly(new ArrayWords()))->get()) . "\n";
echo implode(', ', (new LongWordsOnly(new DatabaseWords()))->get()) . "\n";

// en omdat een decorator ook een WordSource is, kun je ze stapelen
echo implode(', ', (new LongWordsOnly(new LongWordsOnly(new ArrayWords())))->get()) . "\n";
