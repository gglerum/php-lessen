<?php

/**
 * The StudentController class handles the logic for managing students.
 */
class StudentController
{
    private StudentRepository $studentRepository;

    public function __construct()
    {
        $this->studentRepository  = new StudentRepository();
    }

    /**
     * Displays the form for creating a new student.
     */
    public function create(): void
    {
        include_once 'html/student/create.html';
    }

    /**
     * Stores a new student in the database.
     */
    public function store(): void
    {
        $lastId = $this->studentRepository->add(new Student(
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
    public function list(): void
    {
        $students = $this->studentRepository->getAll();

        include_once 'html/student/list.html';
    }

    /**
     * Displays the details of a specific student.
     *
     * @param int $id The ID of the student to show.
     */
    public function show(int $id): void
    {
        // Object is used in the view to display the student's details.
        $student = $this->studentRepository->get($id);

        include_once 'html/student/show.html';
    }
}
