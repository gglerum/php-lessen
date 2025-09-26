<?php
/**
 * Represents a word in the Hangman game.
 */
class Word
{
    private string $word;
    private array $guessedLetters = [];

    /**
     * Constructs a new Word object.
     *
     * @param string $word The word to be guessed.
     */
    public function __construct(string $word)
    {
        $this->word = $word;
    }

    /**
     * Gets the word to be guessed.
     *
     * @return string The word.
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * Gets the display word with guessed letters revealed and unguessed letters replaced with underscores.
     *
     * @return string The display word.
     */
    public function getDisplayWord(): string
    {
        $displayWord = '';
        foreach (str_split($this->word) as $letter) {
            $displayWord .= in_array($letter, $this->guessedLetters) ? $letter : '_';
        }

        return $displayWord;
    }

    /**
     * Guesses a letter in the word.
     *
     * @param string $letter The letter to guess.
     * @return bool True if the letter is in the word, false otherwise.
     */
    public function guessLetter(string $letter): bool
    {
        if (!in_array($letter, $this->guessedLetters)) {
            $this->guessedLetters[] = $letter;
        }
        return str_contains($this->word, $letter);
    }

    /**
     * Guesses the entire word.
     *
     * @param string $word The word to guess.
     * @return bool True if the guessed word is correct, false otherwise.
     */
    public function guessWord(string $word): bool
    {
        return $this->word === $word;
    }

    /**
     * Checks if the word has been completely guessed.
     *
     * @return bool True if the word has been completely guessed, false otherwise.
     */
    public function hasWordBeenGuessed(): bool
    {
        return $this->word === $this->getDisplayWord();
    }
}
