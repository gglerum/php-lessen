<?php
require_once 'movie/Movie.php';
require_once 'dao/DAO.php';
require_once 'dao/MysqlDAO.php';
require_once 'movie/MovieRepository.php';
require_once 'service/PdoService.php';

$moviesRepro = new MovieRepository(new MysqlDAO());
$movieId1 = $moviesRepro->add(new Movie("Is dit te volgen", "Glenn"));
$movieId2 = $moviesRepro->add(new Movie("Is dit te volgen 2", "Glenn"));

echo $movieId2;

$moviesRepro->remove($movieId1);
