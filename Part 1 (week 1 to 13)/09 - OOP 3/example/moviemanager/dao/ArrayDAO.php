<?php
class ArrayDAO implements DAO
{
    private array $movies = [];

    public function add(Movie $movie): int
    {
        $movies[] = $movie;

        return $movie->id;
    }

    public function remove(int $id): void
    {
        $this->movies = array_filter($this->movies, fn($movie) => $movie->id !== $id);
    }
}
