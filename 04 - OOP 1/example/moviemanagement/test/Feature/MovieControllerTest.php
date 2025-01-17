<?php

use PHPUnit\Framework\TestCase;
use Hacklabfrl\Moviemanagement\MovieRepository;
use Hacklabfrl\Moviemanagement\MovieController;

class MovieControllerTest extends TestCase
{

    public function testAddMovieThroughMovieController()
    {
        $mr = new MovieRepository();
        $controller = new MovieController($mr);
        $controller->store(array('name' => 'The Dark Knight', 'Director' => 'Christopher Nollan', 2.0));

        $result = $mr->getByTitle('The Dark Knight');
        $this->assertNotNull($result);
    }
}
