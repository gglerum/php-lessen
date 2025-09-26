<?php
require_once 'models/Classroom.php';
require_once 'services/PdoService.php';

/**
 * The ClassroomController class handles the logic for managing classrooms.
 */
class ClassroomController
{
    private readonly ItemRepository $classroomRepository;
    private readonly ItemRepository $studentRepository;
    private readonly ItemRepository $mentorRepository;

    public function __construct(ItemRepository $classroomRepository, ItemRepository $studentRepository, ItemRepository $mentorRepository)
    {
        $this->classroomRepository = $classroomRepository;
        $this->studentRepository = $studentRepository;
        $this->mentorRepository = $mentorRepository;
    }

    /**
     * Displays the form for creating a new classroom.
     */
    public function create(): void
    {
        $mentors = $this->mentorRepository->getAll();
        include_once 'html/classroom/create.html';
    }

    /**
     * Stores a new classroom in the database.
     */
    public function store(): void
    {
        $lastId = $this->classroomRepository->add(new Classroom(
            null,
            $_POST['name'],
            $_POST['year'],
            $_POST['mentor_id'],
            $this->studentRepository->get($_POST['student_ids'])
        ));

        header('Location: /?page=classroom&action=show&id=' . $lastId);
    }

    /**
     * Displays a list of all classrooms.
     */
    public function list(): void
    {
        $classrooms = $this->classroomRepository->getAll();

        include_once 'html/classroom/list.html';
    }

    /**
     * Displays the details of a specific classroom.
     *
     * @param int $id The ID of the classroom to show.
     */
    public function show(int $id): void
    {
        // Object is used in the view to display the classroom's details.
        $classroom = $this->classroomRepository->get($id);

        include_once 'html/classroom/show.html';
    }
}
