<?php

namespace Hacklabfrl\Moviemanagement;

class MovieController
{

    public function __construct(private MovieRepository $movieRepository) {}

    public function store(array $data)
    {
        $movie = new Movie($data['name'], $data['Director'], $data['rating']);
        $this->movieRepository->add($movie);
    }
}
