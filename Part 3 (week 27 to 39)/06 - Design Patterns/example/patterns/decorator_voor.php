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

// we willen alleen woorden van minimaal 5 letters, dus maken we een subclass
class LongArrayWords extends ArrayWords
{
    public function get(): array
    {
        return array_values(array_filter(parent::get(), fn(string $word) => strlen($word) >= 5));
    }
}

// en voor de database nog een, met precies dezelfde filter
class LongDatabaseWords extends DatabaseWords
{
    public function get(): array
    {
        return array_values(array_filter(parent::get(), fn(string $word) => strlen($word) >= 5));
    }
}

echo implode(', ', (new LongArrayWords())->get()) . "\n";
echo implode(', ', (new LongDatabaseWords())->get()) . "\n";

// voor elke nieuwe bron en elke nieuwe filter groeit het aantal classes: bij 2 bronnen en 2 filters zijn het er al 4
