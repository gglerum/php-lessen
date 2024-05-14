<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/StudentController.php';

switch ($_GET['page']) {
    case 'home':
        HomeController::render($_GET['name']);
        break;
    case 'student':
        switch ($_GET['action']) {
            case 'create':
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
        break;
}
