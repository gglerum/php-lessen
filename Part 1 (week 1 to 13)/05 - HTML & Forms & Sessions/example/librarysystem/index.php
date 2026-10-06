<?php
require_once 'Book.php';
require_once 'BookController.php';
require_once 'Router.php';


session_start();

$router = new Router();
$router->processRoute();
