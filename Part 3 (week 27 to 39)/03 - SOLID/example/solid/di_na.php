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

    // de woordenlijst krijgen we mee van buiten: dit heet dependency injection
    public function __construct(Words $words)
    {
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

// degene die de class gebruikt bepaalt welke woordenlijst wordt gebruikt
$picker = new LongestWordPicker(new Words());
echo $picker->pick() . "\n";

// hier is dit nog niet erg handig: we kunnen alleen de class Words meegeven.
// In dip_na.php zie je hoe een interface ervoor zorgt dat het ook met andere bronnen werkt
