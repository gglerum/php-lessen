<?php

/**
 * This class is used to mange the game status
 */
class Game
{
    /**
     * The current status of the game.
     *
     * @var GameStatus
     */
    private GameStatus $status;

    /**
     * The number of attempts remaining.
     *
     * @var int
     */
    private int $attempts = 7;

    /**
     * Initializes a new instance of the Game class.
     */
    public function __construct()
    {
        $this->status = GameStatus::IN_PROGRESS;
    }

    /**
     * Checks if the game is over.
     *
     * @return bool True if the game is over, false otherwise.
     */
    public function isGameOver(): bool
    {
        return $this->status !== GameStatus::IN_PROGRESS;
    }

    /**
     * Gets the current status of the game.
     *
     * @return GameStatus The current status of the game.
     */
    public function getStatus(): GameStatus
    {
        return $this->status;
    }

    /**
     * Sets the status of the game.
     *
     * @param GameStatus $status The new status of the game.
     * @return void
     */
    public function setStatus(GameStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Gets the number of attempts remaining.
     *
     * @return int The number of attempts remaining.
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * Decreases the number of attempts remaining by 1.
     * If the number of attempts reaches 0, the game status is set to "LOST".
     *
     * @return void
     */
    public function decreaseAttempts(): void
    {
        $this->attempts--;
        if ($this->attempts === 0) {
            $this->status = GameStatus::LOST;
        }
    }
}
