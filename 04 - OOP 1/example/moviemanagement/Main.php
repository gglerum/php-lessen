<?php

/**
 * Main class of our application, currently responsible for too many things
 */
class Main
{
    private array $directors = ["Steven Spielberg", "Martin Scorsese", "Quentin Tarantino"];

    private MovieRepository $movieRepository;

    /**
     * Options for the main menu, each option is an array with the text to show and the function to call
     */
    private array $options = [
        ["Exit" => "exit"],
        ["Show all movies" => "showAllMovies"],
        ["Add a new movie" => "addMovie"]
    ];

    public function __construct()
    {
        $this->movieRepository = new MovieRepository();
    }

    /**
     * Shows the main menu and handles the user input
     * @return void
     */
    public function showMainMenu()
    {
        while (true) {
            echo "Welcome to the movie management system\n";
            echo "Please select an option:\n";
            foreach ($this->options as $key => $value) {
                echo $key . ' - ' . key($value) . "\n";
            }
            $option = (int) readline("Option: ");

            if ($option === 0) {
                echo "Goodbye\n";
                return;
            }
            //reset returns the first value of the internal array of the option
            $function = reset($this->options[$option]);

            echo "\n\n\n";
            $this->$function();
        }
    }

    /**
     * Shows all movies in the repository
     * @return void
     */
    public function showAllMovies()
    {
        $movies = $this->movieRepository->getAll();
        if (empty($movies)) {
            echo "No movies found\n\n\n";
            return;
        }

        foreach ($movies as $movie) {
            echo $movie->getOverviewText() . "\n";
        }

        echo "\n\n\n";
    }

    /**
     * Adds a new movie to the repository
     * @return void
     */
    public function addMovie(): void
    {
        echo "Adding a movie\n";

        $name = readline("Enter movie title: ");
        $director = $this->askForDirector();
        $rating = (float) readline("Enter movie rating: ");

        $movie = new Movie($name, $director, $rating);
        $this->movieRepository->add($movie);

        echo "Movie added successfully\n\n\n";
    }

    /**
     * Asks the user to select a director from the list
     * @return string
     */
    private function askForDirector(): string
    {
        echo "Select a director:\n";
        foreach ($this->directors as $index => $director) {
            echo "$index - $director\n";
        }
        $index = (int) readline("Enter movie director: ");

        return $this->directors[$index];
    }
}
