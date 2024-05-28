<?php

namespace Hacklabfrl\Hangman\Generator;

use Hacklabfrl\Hangman\Utils\DbManager;

/**
 * Class DbWords
 * Represents a word source that retrieves words from a database.
 */
class DbWords implements WordSource
{

    private array $words;

    /**
     * DbWords constructor.
     * Initializes the DbWords object and retrieves words from the database.
     */
    public function __construct()
    {
        //database result is returned as "column" => "value", so we need to get the values of the column
        $this->words = array_column(
            DbManager::getPdo()->executeQuery("SELECT word FROM words", []),
            "word"
        );
    }

    /**
     * Retrieves an array of words from the database.
     *
     * @return array The array of words.
     */
    public function get(): array
    {
        return $this->words;
    }
}
