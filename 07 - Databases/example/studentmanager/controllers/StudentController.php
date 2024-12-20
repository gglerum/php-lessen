<?php
require_once 'models/Student.php';
require_once 'services/PdoService.php';

/**
 * The StudentController class handles the logic for managing students.
 */
class StudentController
{
    private static StudentRepository $studentRepository = new StudentRepository();

    /**
     * Displays the form for creating a new student.
     */
    public static function create(): void
    {
        include_once 'html/student/create.html';
    }

    /**
     * Stores a new student in the database.
     */
    public static function store(): void
    {
        $lastId = static::$studentRepository->add(new Student(
            null,
            $_POST['name'],
            DateTime::createFromFormat('Y-m-d', $_POST['dob']),
            $_POST['phone'],
            $_POST['email'],
            random_int(1, 5000)
        ));

        header('Location: /?page=student&action=show&id=' . $lastId);
    }

    /**
     * Displays a list of all students.
     */
    public static function list(): void
    {
        $results = static::$studentRepository->getAll();

        $students = [];
        foreach ($results as $result) {
            $students[] = new Student(
                $result['id'],
                $result['name'],
                DateTime::createFromFormat('Y-m-d', $result['date_of_birth']),
                $result['phone_number'],
                $result['email'],
                $result['student_number']
            );
        }

        include_once 'html/student/list.html';
    }

    /**
     * Displays the details of a specific student.
     *
     * @param int $id The ID of the student to show.
     */
    public static function show(int $id): void
    {
        // Object is used in the view to display the student's details.
        $student = static::$studentRepository->get($id);

        include_once 'html/student/show.html';
    }
}
