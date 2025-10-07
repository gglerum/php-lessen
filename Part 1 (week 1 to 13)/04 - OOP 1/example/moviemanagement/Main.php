<?php

/**
 * MAIN APPLICATION CLASS - THE USER INTERFACE CONTROLLER
 *
 * 🎯 LEARNING OBJECTIVES:
 * This class demonstrates several important Object-Oriented Programming concepts:
 *
 * 1. **CLASS PROPERTIES**: How to store data that belongs to the class
 * 2. **ENCAPSULATION**: Using private properties to protect internal data
 * 3. **COMPOSITION**: This class "has-a" MovieRepository (composition relationship)
 * 4. **CONSTRUCTOR**: Setting up the object when it's created
 * 5. **ARRAY STRUCTURES**: Complex arrays that store both display text and method names
 *
 * 🏗️ ARCHITECTURE ROLE:
 * This class is responsible for:
 * - Managing the user interface (showing menus, getting input)
 * - Coordinating between user actions and the MovieRepository
 * - Handling the main application flow and menu logic
 *
 * 📝 NOTE: In a real application, this class would be split into smaller,
 * more focused classes (following Single Responsibility Principle).
 */
class Main
{
    // 🎬 PRIVATE PROPERTIES (ENCAPSULATION EXAMPLE)
    // These properties can only be accessed from within this class.
    // This protects our data from being changed unexpectedly from outside.

    /**
     * List of available directors for movie selection.
     * This is a simple array of strings - in a real app, this might come from a database.
     * @var array<string>
     */
    private array $directors = ["Steven Spielberg", "Martin Scorsese", "Quentin Tarantino"];

    /**
     * The MovieRepository object that handles all movie data operations.
     * This is an example of COMPOSITION - Main "has-a" MovieRepository.
     * @var MovieRepository
     */
    private MovieRepository $movieRepository;

    /**
     * Menu options structure - demonstrates complex array usage.
     * Each option is an associative array where:
     * - The key is the display text shown to the user
     * - The value is the method name to call when selected
     *
     * This pattern allows us to easily add new menu options without
     * changing the menu display logic.
     * @var array<array<string>>
     */
    private array $options = [
        ["Exit" => "exit"],
        ["Show all movies" => "showAllMovies"],
        ["Add a new movie" => "addMovie"]
    ];

    /**
     * CONSTRUCTOR - Setting Up the Object
     *
     * The constructor runs automatically when we create a new Main object.
     * Here we initialize any objects this class depends on.
     *
     * 🔧 DEPENDENCY CREATION:
     * We create the MovieRepository here. In more advanced applications,
     * this would be "injected" from outside (dependency injection).
     */
    public function __construct()
    {
        // Create our movie repository to handle all movie-related data operations
        $this->movieRepository = new MovieRepository();
    }

    /**
     * MAIN MENU DISPLAY AND USER INPUT HANDLING
     *
     * This method demonstrates several important programming concepts:
     * - Infinite loops with exit conditions
     * - Dynamic method calling
     * - Array manipulation with built-in functions
     *
     * 🔄 PROGRAM FLOW:
     * 1. Display welcome message and menu options
     * 2. Get user input and validate
     * 3. Call the appropriate method based on user choice
     * 4. Repeat until user chooses to exit
     *
     * @return void
     */
    public function showMainMenu()
    {
        // 🔄 INFINITE LOOP PATTERN
        // This creates a persistent menu that keeps running until the user exits.
        // The "while (true)" pattern is common in console applications.
        while (true) {
            // 📺 DISPLAY MENU HEADER
            echo "Welcome to the movie management system\n";
            echo "Please select an option:\n";

            // 🔢 DYNAMIC MENU GENERATION
            // Loop through our options array and display each choice.
            // $key is the numeric index (0, 1, 2...)
            // $value is the associative array containing display text and method name
            foreach ($this->options as $key => $value) {
                // key($value) gets the first key from the associative array
                // For ["Exit" => "exit"], key() returns "Exit"
                echo $key . ' - ' . key($value) . "\n";
            }

            // 📥 GET USER INPUT
            // readline() gets input from the console
            // (int) casts the string input to an integer
            $option = (int) readline("Option: ");

            // 🚪 EXIT CONDITION
            // If user chooses 0, exit the application gracefully
            if ($option === 0) {
                echo "Goodbye\n";
                return; // Exit the method (and the loop)
            }

            // 🎯 DYNAMIC METHOD CALLING
            // This is where the magic happens! We dynamically call methods based on user input.

            // reset() returns the first VALUE from the associative array
            // For ["Exit" => "exit"], reset() returns "exit"
            $function = reset($this->options[$option]);

            // Add some visual spacing
            echo "\n\n\n";

            // 🔮 VARIABLE FUNCTION CALLS
            // $this->$function() calls the method whose name is stored in $function
            // If $function = "showAllMovies", this calls $this->showAllMovies()
            // This is a powerful PHP feature that enables flexible, data-driven programming!
            $this->$function();
        }
    }

    /**
     * DISPLAY ALL MOVIES - DEMONSTRATING OBJECT COLLABORATION
     *
     * This method shows how objects work together in OOP:
     * - Main class coordinates the display
     * - MovieRepository provides the data
     * - Each Movie object formats its own display text
     *
     * 🔗 OBJECT COLLABORATION PATTERN:
     * Main -> MovieRepository -> Movie objects
     *
     * @return void
     */
    public function showAllMovies()
    {
        // 📊 DELEGATE TO REPOSITORY
        // We don't store movies directly in Main - that's MovieRepository's job!
        // This is the "Single Responsibility Principle" - each class has one clear job.
        $movies = $this->movieRepository->getAll();

        // 🔍 HANDLE EMPTY STATE
        // Good user experience means handling edge cases gracefully.
        // What if there are no movies? Don't just show nothing - tell the user!
        if (empty($movies)) {
            echo "No movies found\n\n\n";
            return; // Exit early - no point continuing if there's nothing to show
        }

        // 🎬 DISPLAY EACH MOVIE
        // Loop through all movies and let each one format its own display text.
        // This follows the principle: "Ask objects to do things, don't do things to objects"
        foreach ($movies as $movie) {
            // Each Movie object knows how to display itself via getOverviewText()
            // This keeps formatting logic where it belongs - in the Movie class
            echo $movie->getOverviewText() . "\n";
        }

        // Add visual spacing for better user experience
        echo "\n\n\n";
    }

    /**
     * ADD NEW MOVIE - DEMONSTRATING OBJECT CREATION AND COORDINATION
     *
     * This method shows the complete flow of creating and storing a new object:
     * 1. Gather data from user input
     * 2. Create a new object with that data
     * 3. Store the object using another class (delegation)
     *
     * 🏗️ OBJECT CREATION PATTERN:
     * Collect Data -> Create Object -> Store Object -> Confirm Success
     *
     * @return void
     */
    public function addMovie(): void
    {
        echo "Adding a movie\n";

        // 📝 GATHER USER INPUT
        // Each piece of data is collected separately and validated by the input methods
        $name = readline("Enter movie title: ");

        // 🎯 METHOD DELEGATION
        // We delegate director selection to a specialized method.
        // This keeps this method focused and makes director selection reusable.
        $director = $this->askForDirector();

        // 🔢 TYPE CASTING
        // readline() always returns a string, so we cast to float for ratings
        $rating = (float) readline("Enter movie rating: ");

        // 🏗️ OBJECT CONSTRUCTION
        // Create a new Movie object with the collected data.
        // The Movie constructor will handle any initialization logic.
        $movie = new Movie($name, $director, $rating);

        // 💾 DELEGATE STORAGE
        // We don't handle storage ourselves - that's MovieRepository's responsibility!
        // This separation of concerns makes code easier to maintain and test.
        $this->movieRepository->add($movie);

        // ✅ USER FEEDBACK
        // Always provide feedback so users know their action succeeded
        echo "Movie added successfully\n\n\n";
    }

    /**
     * DIRECTOR SELECTION - DEMONSTRATING INPUT VALIDATION AND ARRAY HANDLING
     *
     * This private method handles the complex logic of director selection.
     * It's private because it's an internal helper - other classes don't need to call it.
     *
     * 🔒 WHY PRIVATE?
     * - Only used internally by this class
     * - Implementation detail that might change
     * - Keeps the public interface clean and focused
     *
     * 🎯 PATTERN: CONTROLLED SELECTION
     * Instead of free-text input, we provide a controlled list of options.
     * This prevents data inconsistency and typos.
     *
     * @return string The selected director's name
     */
    private function askForDirector(): string
    {
        echo "Select a director:\n";

        // 📋 DISPLAY OPTIONS WITH INDICES
        // foreach with array indices gives us both the position and the value
        foreach ($this->directors as $index => $director) {
            echo "$index - $director\n";
        }

        // 📥 GET USER SELECTION
        // We use the array index as the selection mechanism
        $index = (int) readline("Enter movie director: ");

        return $this->directors[$index];
    }
}
