<?php

namespace Hacklabfrl\Hangman;

use Hacklabfrl\Hangman\Generator\RandomWordGenerator;

/**
 * The GameService class handles the game logic and provides methods to interact with the game.
 */
class GameService
{
    private Game $game;
    private Word $word;
    private DrawnHangman $drawnHangman;

    /**
     * Constructs a new GameService object.
     *
     * @param RandomWordGenerator $randomWordGenerator The random word generator used to generate the word for the game.
     */
    public function __construct(RandomWordGenerator $randomWordGenerator)
    {
        $this->game = new Game();
        $this->word = new Word($randomWordGenerator->get());
        $this->drawnHangman = new DrawnHangman();
    }

    /**
     * Checks if the game is over.
     *
     * @return bool True if the game is over, false otherwise.
     */
    public function isGameOver(): bool
    {
        return $this->game->isGameOver();
    }

    /**
     * Processes the user input.
     *
     * @param string $input The user input.
     * @return bool True if the user input was valid, false otherwise.
     */
    public function userInput(string $input): bool
    {
        $hasWordBeenGuessed = $this->word->guessWord($input);
        $result = $this->word->guessLetter($input) || $hasWordBeenGuessed;
        if ($result === false) {
            $this->game->decreaseAttempts();
            return false;
        }
        if ($this->word->hasWordBeenGuessed() || $hasWordBeenGuessed) {
            $this->game->setStatus(GameStatus::WON);
        }
        return true;
    }

    /**
     * Gets the display word.
     *
     * @return string The display word.
     */
    public function getDisplayWord(): string
    {
        // if the game is still running show the display word, otherwise show the full word
        return $this->game->getStatus() == GameStatus::IN_PROGRESS ? $this->word->getDisplayWord() : $this->word->getWord();
    }

    /**
     * Gets the number of attempts remaining.
     *
     * @return int The number of attempts remaining.
     */
    public function getAttempts(): int
    {
        return $this->game->getAttempts();
    }

    /**
     * Gets the drawn hangman based on the number of attempts.
     *
     * @return string The drawn hangman.
     */
    public function getDrawnHangman(): string
    {
        return $this->drawnHangman->get($this->game->getAttempts());
    }

    /**
     * Gets the current game status.
     *
     * @return GameStatus The current game status.
     */
    public function getStatus(): GameStatus
    {
        return $this->game->getStatus();
    }
}
