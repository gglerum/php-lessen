<?php

class Student extends Person
{
    public function __construct(
        private ?int $id,
        string $name,
        DateTime|string $dateOfBirth,
        string $phoneNumber,
        string $email,
        private string $studentNumber
    ) {
        parent::__construct($name, $dateOfBirth, $phoneNumber, $email);
    }

    public function getStudentNumber(): string
    {
        return $this->studentNumber;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function fromArray($data): Student
    {
        return new Student(
            $data['id'],
            $data['name'],
            DateTime::createFromFormat('Y-m-d', $data['date_of_birth']),
            $data['phone_number'],
            $data['email'],
            $data['student_number']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'date_of_birth' => $this->getDateOfBirth(),
            'phone_number' => $this->getPhoneNumber(),
            'email' => $this->getEmail(),
            'student_number' => $this->getStudentNumber()
        ];
    }
}
