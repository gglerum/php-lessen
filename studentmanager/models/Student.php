<?php
require_once 'Person.php';

class Student extends Person
{
    public function __construct(
        private int $id,
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
}
