<?php
require_once 'models/Mentor.php';
require_once 'services/PdoService.php';

/**
 * The MentorController class handles the logic for managing mentors.
 */
class MentorController
{
    private readonly ItemRepository $mentorRepository;

    public function __construct(ItemRepository $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }

    /**
     * Displays the form for creating a new mentor.
     */
    public function create(): void
    {
        include_once 'html/mentor/create.html';
    }

    /**
     * Stores a new mentor in the database.
     */
    public function store(): void
    {
        $lastId = static::$mentorRepository->add(new Mentor(
            null,
            $_POST['name'],
            DateTimeImmutable::createFromFormat('Y-m-d', $_POST['dob']),
            $_POST['phone'],
            $_POST['email'],
            random_int(1, 5000)
        ));

        header('Location: /?page=mentor&action=show&id=' . $lastId);
    }

    /**
     * Displays a list of all mentors.
     */
    public function list(): void
    {
        $mentors = $this->mentorRepository->getAll();

        include_once 'html/mentor/list.html';
    }

    /**
     * Displays the details of a specific mentor.
     *
     * @param int $id The ID of the mentor to show.
     */
    public function show(int $id): void
    {
        // Object is used in the view to display the mentor's details.
        $mentor = $this->mentorRepository->get($id);

        include_once 'html/mentor/show.html';
    }
}
