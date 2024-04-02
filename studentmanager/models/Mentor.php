<?php
require_once 'Person.php';

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
        string $name,
        DateTime $dateOfBirth,
        string $phoneNumber,
        string $email,
        private string $employeeNumber
    ) {
        parent::__construct($name, $dateOfBirth, $phoneNumber, $email);
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
}