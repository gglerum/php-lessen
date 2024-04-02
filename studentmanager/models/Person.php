<?php

/**
 * Represents a person.
 */
class Person
{
    /**
     * Creates a new instance of the Person class.
     *
     * @param string $name The name of the person.
     * @param DateTime $dateOfBirth The date of birth of the person.
     * @param string $phoneNumber The phone number of the person.
     * @param string $email The email address of the person.
     */
    public function __construct(
        private string $name,
        private DateTime $dateOfBirth,
        private string $phoneNumber,
        private string $email,
    ) {
    }

    /**
     * Gets the name of the person.
     *
     * @return string The name of the person.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the date of birth of the person.
     *
     * @return DateTime The date of birth of the person.
     */
    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth->format('Y-m-d');
    }

    /**
     * Gets the phone number of the person.
     *
     * @return string The phone number of the person.
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Gets the email address of the person.
     *
     * @return string The email address of the person.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Retrieves the display name of the person.
     *
     * @return string The display name of the person.
     */
    public function getDisplayName()
    {
        return $this->name;
    }
}
