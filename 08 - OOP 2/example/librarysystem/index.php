<?php
require_once 'Book/Book.php';
require_once 'Main.php';
require_once 'Book/BookController.php';
require_once 'HomeController.php';
$main = new Main();

// session_start();

// if ($_POST) {
//     $_SESSION['book'] = $main->createBook($_POST);
//     header('location: index.php');
// }
// if (isset($_GET['action']) == 'replace' || !isset($_SESSION['book'])) {
//     include_once 'html/form.html';
// } else if (isset($_SESSION['book'])) {
//     $main->showBook($_SESSION['book']);
// }

$routes = [
    'book' => [
        'create' => BookController::class . '::create',
        'store' => BookController::class . '::store',
        'show' => BookController::class . '::show',
        'index' => BookController::class . '::index',
        'default' => BookController::class . '::index',
    ],
    'home' => [
        'default' => HomeController::class . '::index',
    ],
];

$type = "home";
$action = "default";
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$routes[$type][$action]();
