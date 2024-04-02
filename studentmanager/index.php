<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/StudentController.php';

switch ($_GET['page']) {
    case '':
        HomeController::render($_GET['name']);
        break;
    case 'add':
        StudentController::create();
        break;
    case 'store':
        StudentController::store();
        break;
    case 'show':
        StudentController::show($_GET['id']);
        break;
    case 'list':
        StudentController::list();
        break;
}
