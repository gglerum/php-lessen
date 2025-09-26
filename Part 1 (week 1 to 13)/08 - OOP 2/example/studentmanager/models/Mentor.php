<?php

/**
 * Represents a Mentor, a type of Person.
 */
class Mentor extends Person
{
    /**
     * Creates a new Mentor instance.
     *
     * @param string $name The name of the mentor.
     * @param DateTime $dateOfBirth The date of birth of the mentor.
     * @param string $phoneNumber The phone number of the mentor.
     * @param string $email The email address of the mentor.
     * @param string $employeeNumber The employee number of the mentor.
     */
    public function __construct(
        private ?int $id,
        private string $name,
        private DateTimeImmutable $dateOfBirth,
        private string $phoneNumber,
        private string $email,
        private string $employeeNumber
    ) {
        parent::__construct($id, $name, $dateOfBirth, $phoneNumber, $email);
    }

    /**
     * Gets the employee number of the mentor.
     *
     * @return string The employee number.
     */
    public function getEmployeeNumber(): string
    {
        return $this->employeeNumber;
    }

    /**
     * Gets the display name of the mentor.
     *
     * @return string The display name.
     */
    public function getDisplayName(): string
    {
        return parent::getDisplayName() . " " . $this->employeeNumber;
    }

    /**
     * Converts the mentor to an array.
     * @return array The array representation of the mentor.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'date_of_birth' => $this->getDateOfBirth(),
            'phone_number' => $this->getPhoneNumber(),
            'email' => $this->getEmail(),
            'employee_number' => $this->getEmployeeNumber()
        ];
    }

    /**
     * Gets the overview text for the mentor.
     * @return string The overview text.
     */
    public function getOverviewText(): string
    {
        return $this->getName() . " (" . $this->getEmployeeNumber() . ")";
    }
}
