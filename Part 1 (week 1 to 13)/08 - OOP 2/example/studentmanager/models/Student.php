<?php

class Student extends Person
{
    public function __construct(
        private ?int $id,
        private string $name,
        private DateTimeImmutable|string $dateOfBirth,
        private string $phoneNumber,
        private string $email,
        private string $studentNumber
    ) {
        parent::__construct($id, $name, $dateOfBirth, $phoneNumber, $email);
    }

    public function getStudentNumber(): string
    {
        return $this->studentNumber;
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

    /**
     * Gets the overview text for the student.
     * @return string
     */
    public function getOverviewText(): string
    {
        return $this->getName() . " (" . $this->getStudentNumber() . ")";
    }
}
