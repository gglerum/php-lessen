<?php

use PHPUnit\Framework\TestCase;
use Hacklabfrl\Moviemanagement\Movie;
use Hacklabfrl\Moviemanagement\MovieRepository;

class MovieRepositoryTest extends TestCase
{

    public function testAddMovieThroughMovieRepository()
    {
        $movie = new Movie('The Godfather', 'Francis Ford Coppola', 9.2);
        $movieRepository = new MovieRepository();
        $movieRepository->add($movie);

        $this->assertEquals(1, $movie->getId(), "Movie was not created as expected");
        $result = $movieRepository->getById(1);

        $this->assertNotNull($result, "Movie was not found in repository");
        $this->assertEquals($result->getId(), 3, "Movie had not expected id");
    }
}
