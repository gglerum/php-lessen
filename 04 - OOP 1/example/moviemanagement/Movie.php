<?php

/**
 * Is a movie entity
 */
class Movie
{
    private static int $idIncrement = 0;
    private int $id;

    public function __construct(
        private string $name,
        private string $director,
        private float $rating
    ) {
        $this->id = ++static::$idIncrement;
    }

    /**
     * @return int id of the movie
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the name of the movie
     * @return string
     */
    public function getName(): string
    {
        return strtoupper($this->name);
    }

    /**
     * Get the director of the movie
     * @return string
     */
    public function getOverviewText(): string
    {
        return $this->name . ' - ' . $this->director . ' - ' . $this->rating;
    }
}
