<?php
require_once 'Mentor.php';
require_once 'Student.php';

/**
 * Represents a classroom with students and a mentor.
 */
class Classroom
{
    private array $students = [];

    /**
     * Creates a new Classroom instance.
     *
     * @param string $name The name of the classroom.
     * @param int $year The year of the classroom.
     * @param Mentor $mentor The mentor of the classroom.
     */
    public function __construct(
        private string $name,
        private int $year,
        private Mentor $mentor
    ) {
    }

    /**
     * Gets the students in the classroom.
     *
     * @return array The array of students.
     */
    public function getStudents(): array
    {
        return $this->students;
    }

    /**
     * Gets the name of the classroom.
     *
     * @return string The name of the classroom.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the year of the classroom.
     *
     * @return int The year of the classroom.
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Gets the mentor of the classroom.
     *
     * @return Mentor The mentor of the classroom.
     */
    public function getMentor(): Mentor
    {
        return $this->mentor;
    }

    /**
     * Adds a student to the classroom.
     *
     * @param Student $student The student to add.
     * @return void
     */
    public function addStudent(Student $student): void
    {
        $students[] = $student;
    }

    /**
     * Adds multiple students to the classroom.
     *
     * @param array $students The array of students to add.
     * @return void
     */
    public function addStudents(array $students): void
    {
        $this->students = array_merge($this->students, $students);
    }
}