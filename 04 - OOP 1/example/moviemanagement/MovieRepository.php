<?php

/**
 * This class is responsible for managing the movies in the system.
 */
class MovieRepository
{
    private array $movies = [];

    /**
     * Adds movie to repository
     * @return int id of the movie that was added
     */
    public function add(string $name, string $director, float $rating): int
    {
        $movie = new Movie($name, $director, $rating);
        $this->movies[$movie->getId()] = $movie;

        return $movie->getId();
    }

    /**
     * Removes movie from database
     * @param int $id of the movie to be removed
     * @return void
     */
    public function remove(int $id): void
    {
        $movie = $this->getById($id);
        unset($this->movies[$movie->getId()]);
    }
    /**
     * Update the movie
     * @param array $data contains new movie data
     * @param int $id of the movie
     * @return void
     */
    public function update(array $data, int $id): void
    {
        $movie = $this->getById($id);
        $movie->update($data);
    }

    /**
     * Retrieve movie by id
     * @param int $id of the movie we want to retrieve
     * @return Movie|null returns null if movie was not found
     */
    public function getById(int $id): Movie | null
    {
        foreach ($this->movies as $index => $movie) {
            if ($movie->getId() === $id) {
                return $movie;
            }
        }
        return null;
    }
}
