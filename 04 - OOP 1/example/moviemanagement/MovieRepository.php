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
    public function add(Movie $movie): int
    {
        $this->movies[] = $movie;

        return $movie->getId();
    }

    /**
     * Removes movie from database
     * @param int $id of the movie to be removed
     * @return void
     */
    public function remove(int $id): void
    {
        for ($i = 0; $i < count($this->movies); $i++) {
            if ($this->movies[$i]->getId() === $id) {
                unset($this->movies[$i]);
                return;
            }
        }
    }

    /**
     * Retrieve all movies
     * @return array of all movies
     */
    public function getAll(): array
    {
        return $this->movies;
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
