<?php
include('./MovieRepository.php');
include('./Movie.php');

$movieRepos = new MovieRepository();
$movieId = $movieRepos->add("PHP needs improving", "Glenn Glerum", 5.5);
//$movieRepos->remove($movieId);

$movie = $movieRepos->getById($movieId);
echo $movie->getName() . "\n";

$movieRepos->update([
    'name' => 'Glenn is testing',
    'director' => 'Fritz',
    'rating' => 2.0
], $movieId);

echo $movie->getName();
