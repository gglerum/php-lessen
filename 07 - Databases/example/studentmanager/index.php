<?php
require_once 'controllers/HomeController.php';
require_once 'controllers/StudentController.php';
require_once 'repositories/StudentRepository.php';
require_once 'services/QueryBuilder.php';
require_once 'models/Student.php';
require_once 'models/Mentor.php';
require_once 'models/Student.php';
require_once 'Person.php';
/*
The index its only job is to be an entry point. It will receive the request in the form of a query parameter
called 'page'. The value of this parameter will determine which controller to call.
*/
$page = $_GET['page'] ?? 'home';
switch ($page) {
    case 'home':
        HomeController::render();
        break;
    case 'student':
        switch (@$_GET['action']) {
            case 'create':
                StudentController::create();
                break;
            case 'store':
                StudentController::store();
                break;
            case 'show':
                StudentController::show($_GET['id']);
                break;
            default:
                StudentController::list();
                break;
        }
        break;
}
