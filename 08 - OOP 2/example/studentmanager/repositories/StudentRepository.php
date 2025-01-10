<?php

/**
 * StudentRepository is responsible for handling exceptional database operations for the Student model,
 * not covered by the ItemRepository.
 */
class StudentRepository extends ItemRepository
{
    public function __construct()
    {
        parent::__construct(Student::class);
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
