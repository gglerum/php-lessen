<?php
require_once 'Book.php';
require_once 'Main.php';
$main = new Main();

session_start();

if ($_POST) {
    $_SESSION['book'] = $main->createBook($_POST);
    header('location: index.php');
}
if (isset($_GET['action']) == 'replace' || !isset($_SESSION['book'])) {
    include_once 'html/form.html';
} else if (isset($_SESSION['book'])) {
    $main->showBook($_SESSION['book']);
}
