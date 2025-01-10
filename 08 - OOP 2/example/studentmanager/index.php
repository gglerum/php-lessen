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

// Create instances of the repositories
$studentRepository = new StudentRepository();
$classroomRepository = new ItemRepository(Classroom::class);
$mentorRepository = new ItemRepository(Mentor::class);

// Create instances of the controllers
$studentController = new StudentController($studentRepository);
$classroomController = new ClassroomController($classroomRepository, $studentRepository, $mentorRepository);
$mentorRepository = new MentorController($mentorRepository);

$page = $_GET['page'] ?? 'home';
switch ($page) {
    case 'home':
        HomeController::render();
        break;
    case 'student':
        switch (@$_GET['action']) {
            case 'create':
                $studentController->create();
                break;
            case 'store':
                $studentController->store();
                break;
            case 'show':
                $studentController->show($_GET['id']);
                break;
            default:
                $studentController->list();
                break;
        }
        break;

    case 'mentor':
        switch (@$_GET['action']) {
            case 'create':
                $mentorController->create();
                break;
            case 'store':
                $mentorController->store();
                break;
            case 'show':
                $mentorController->show($_GET['id']);
                break;
            default:
                $mentorController->list();
                break;
        }
        break;

    case 'classroom':
        switch (@$_GET['action']) {
            case 'create':
                $classroomController->create();
                break;
            case 'store':
                $classroomController->store();
                break;
            case 'show':
                $classroomController->show($_GET['id']);
                break;
            default:
                $classroomController->list();
                break;
        }
        break;
}
