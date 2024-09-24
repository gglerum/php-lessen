<?php

/**
 * Is a movie entity
 */
class Movie
{
    private int $id;

    public function __construct(
        private string $name,
        private string $director,
        private float $rating
    ) {
        $this->id = random_int(1, 100);
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
     * Update the movie with data
     * @param array $data associative array with new movie data ['name', 'director', 'rating']
     * @return void
     */
    public function update(array $data): void
    {
        $this->name = $data['name'];
        $this->director = $data['director'];
        $this->rating = $data['rating'];
    }
}
