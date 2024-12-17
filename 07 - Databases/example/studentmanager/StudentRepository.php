<?php
class StudentRepository
{
    private QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(Student::class);
    }

    public function add(Student $student)
    {
        $this->queryBuilder->where(['id' => $student->getId()])
            ->insert($student->toArray());
    }

    public function get(int $id): Student
    {
        return $this->queryBuilder->select('*')->where(['id' => $id])
            ->get()[0];
    }

    public function getAll(): array
    {
        return $this->queryBuilder->where([])->select('*')->get();
    }

    public function getByTeacherId(): array
    {
        return $this->queryBuilder->select('*')->where(['teacher_id' => 1])->get();
    }
}
