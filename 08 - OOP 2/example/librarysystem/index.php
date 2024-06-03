<?php
require_once 'Data/DBEntity.php';
require_once 'Borrow/helpers/BorrowHelper.php';
require_once 'Book/Book.php';
require_once 'Magazine/Magazine.php';
require_once 'Book/BookController.php';
require_once 'Magazine/MagazineController.php';
require_once 'HomeController.php';

$routes = [
    'book' => [
        'create' => BookController::class . '::create',
        'store' => BookController::class . '::store',
        'show' => BookController::class . '::show',
        'index' => BookController::class . '::index',
        'default' => BookController::class . '::index',
        'borrow' => BookController::class . '::borrow',
        'return' => BookController::class . '::returnItem',
    ],
    'magazine' => [
        'create' => MagazineController::class . '::create',
        'store' => MagazineController::class . '::store',
        'show' => MagazineController::class . '::show',
        'index' => MagazineController::class . '::index',
        'default' => MagazineController::class . '::index',
    ],
    'home' => [
        'default' => HomeController::class . '::index',
    ],
];

$type = isset($_GET['type']) ? $_GET['type'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'default';

$routes[$type][$action]();
