<?php

namespace Hacklabfrl\Hangman\Generator;

/**
 * Class Words
 *
 * This class represents a collection of words used in a hangman game.
 */
class Words implements WordSource
{
    /**
     * @var array $words An array of words.
     */
    private $words = [
        "apple",
        "banana",
        "cherry",
        "date",
        "elderberry",
        "fig",
        "grape",
        "honeydew",
        "kiwi",
        "lemon",
        "mango",
        "nectarine",
        "orange",
        "pear",
        "quince",
        "raspberry",
        "strawberry",
        "tangerine",
        "ugli",
        "vanilla",
        "watermelon",
        "xigua",
        "yellow",
        "zucchini"
    ];

    /**
     * Get the array of words.
     *
     * @return array The array of words.
     */
    public function get(): array
    {
        return $this->words;
    }
}
