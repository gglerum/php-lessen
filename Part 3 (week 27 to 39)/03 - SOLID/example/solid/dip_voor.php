<?php

declare(strict_types=1);

class ArrayWords
{
    public function get(): array
    {
        return ['apple', 'banana', 'cherry'];
    }
}

class LongestWordPicker
{
    // de picker is afhankelijk van de concrete class ArrayWords
    private ArrayWords $source;

    public function __construct(ArrayWords $source)
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

$picker = new LongestWordPicker(new ArrayWords());
echo $picker->pick() . "\n";

// willen we woorden uit een database gebruiken, dan moeten we de picker zelf aanpassen
// of een kopie van de picker maken die om een DatabaseWords vraagt
