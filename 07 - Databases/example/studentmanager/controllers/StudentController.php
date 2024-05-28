<?php
require_once 'models/Student.php';
require_once 'services/PdoService.php';

/**
 * The StudentController class handles the logic for managing students.
 */
class StudentController
{
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
        $lastId = PdoService::getInstance()->insert(
            "INSERT INTO students(name, date_of_birth, email, phone_number, student_number) VALUES (?, ?, ?, ?, ?)",
            [
                $_POST['name'],
                $_POST['dob'],
                $_POST['email'],
                $_POST['phone'],
                random_int(1, 5000)
            ]
        );

        header('Location: /?page=student&action=show&id=' . $lastId);
    }

    /**
     * Displays a list of all students.
     */
    public static function list(): void
    {
        $results = PdoService::getInstance()->fetchAll('students');

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
        $pdo = PdoService::getInstance();
        $result = $pdo->fetch($id, 'students');

        $student = new Student(
            $result['id'],
            $result['name'],
            DateTime::createFromFormat('Y-m-d', $result['date_of_birth']),
            $result['phone_number'],
            $result['email'],
            $result['student_number']
        );

        include_once 'html/student/show.html';
    }
}
