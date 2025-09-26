<?php

/**
 * StudentRepository is responsible for handling database operations for the Student model.
 */
class StudentRepository
{
    private QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(Student::class);
    }

    /**
     * Adds a new student to the database.
     * @param Student $student The student to add.
     * @return int The ID of the newly created student.
     */
    public function add(Student $student): int
    {
        return $this->queryBuilder->insert($student->toArray());
    }

    /**
     * Retrieves a student from the database by ID.
     * @param int $id The ID of the student to retrieve.
     * @return Student The student with the specified ID.
     */
    public function get(int $id): Student
    {
        return $this->queryBuilder->select('*')->where(['id' => $id])
            ->get()[0];
    }

    /**
     * Retrieves all students from the database.
     * @return array An array of all students in the database.
     */
    public function getAll(): array
    {
        return $this->queryBuilder->where([])->select('*')->get();
    }

    /**
     * Retrieves all students by a specific teach id.
     * @return array
     */
    public function getByTeacherId(int $id): array
    {
        return $this->queryBuilder->select('*')->where(['teacher_id' => $id])->get();
    }
}
