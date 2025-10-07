<?php

namespace Hacklabfrl\Moviemanagement;

/**
 * MOVIE REPOSITORY CLASS - DATA MANAGEMENT AND STORAGE
 *
 * 🎯 LEARNING OBJECTIVES:
 * This class demonstrates the "Repository Pattern" - a fundamental design pattern
 * that separates data access logic from business logic.
 *
 * 1. **REPOSITORY PATTERN**: Centralizes data access and provides a clean interface
 * 2. **COLLECTION MANAGEMENT**: How to store and manipulate groups of objects
 * 3. **ARRAY OPERATIONS**: Adding, removing, and searching through collections
 * 4. **TYPE SAFETY**: Using type hints to ensure data integrity
 * 5. **NULL SAFETY**: Handling cases where requested data doesn't exist
 *
 * 🏗️ ARCHITECTURE ROLE:
 * The Repository acts as an in-memory database for our application.
 * In a real application, this would connect to a actual database,
 * but the interface would remain the same.
 *
 * 🔄 DESIGN PATTERN: REPOSITORY
 * - Encapsulates data access logic
 * - Provides a consistent interface for data operations
 * - Makes testing easier (can be replaced with a mock for tests)
 * - Allows switching data sources without changing other code
 */
class MovieRepository
{
    // 📚 PRIVATE DATA COLLECTION
    // This array stores all Movie objects. It's private because other classes
    // shouldn't access it directly - they should use the public methods.
    // This ensures data integrity and allows us to control how data is accessed.
    private array $movies = [];

    /**
     * ADD MOVIE TO COLLECTION
     *
     * 💾 STORAGE OPERATION:
     * This method demonstrates how to add objects to a collection safely.
     * It uses type hints to ensure only Movie objects can be added.
     *
     * 🔒 TYPE SAFETY:
     * The parameter type hint (Movie $movie) prevents adding wrong types.
     * PHP will throw an error if anything other than a Movie is passed.
     *
     * 🆔 RETURN VALUE:
     * Returns the ID of the added movie for confirmation/reference.
     * This is useful for logging or providing user feedback.
     *
     * @param Movie $movie The movie object to add to the repository
     * @return int The unique ID of the movie that was added
     */
    public function add(Movie $movie): int
    {
        // 📝 ARRAY APPEND OPERATION
        // The [] syntax adds an element to the end of an array
        // This is equivalent to: array_push($this->movies, $movie)
        $this->movies[] = $movie;

        // ✉️ RETURN CONFIRMATION
        // Return the movie's ID so the caller knows the operation succeeded
        // and can reference the movie later if needed
        return $movie->getId();
    }

    /**
     * REMOVE MOVIE FROM COLLECTION
     *
     * 🗑️ DELETION OPERATION:
     * This method demonstrates safe object removal from a collection.
     * It searches for the movie by ID and removes it if found.
     *
     * 🔍 SEARCH AND DESTROY PATTERN:
     * 1. Loop through the collection
     * 2. Find the item with matching ID
     * 3. Remove it using unset()
     * 4. Exit early to avoid unnecessary iterations
     *
     * ⚡ PERFORMANCE NOTE:
     * Uses early return for efficiency - once found and removed,
     * no need to continue checking the rest of the array.
     *
     * @param int $id The unique identifier of the movie to remove
     * @return void
     */
    public function remove(int $id): void
    {
        // 🔄 SEARCH LOOP
        // Use count() instead of foreach when you need the index for removal
        for ($i = 0; $i < count($this->movies); $i++) {
            // 🎯 MATCH FOUND
            // Compare the movie's ID with the target ID
            if ($this->movies[$i]->getId() === $id) {
                // 🗑️ REMOVE ELEMENT
                // unset() removes the element from the array
                // This doesn't reindex the array - gaps may exist
                unset($this->movies[$i]);

                // ⚡ EARLY EXIT
                // Found and removed - no need to continue searching
                return;
            }
        }
        // If we reach here, the movie with given ID was not found
        // In a real application, you might want to throw an exception or log this
    }

    /**
     * RETRIEVE ALL MOVIES
     *
     * 📊 COLLECTION ACCESS:
     * This method provides read-only access to the entire movie collection.
     * It's the safest way to expose internal data to external classes.
     *
     * 🔒 ENCAPSULATION PRINCIPLE:
     * Notice we return the array directly, not a copy. In a production app,
     * you might want to return a copy to prevent external modification:
     * return array_values($this->movies); // Removes gaps and reindexes
     *
     * 📋 RETURN TYPE:
     * Returns array of Movie objects. The calling code can then iterate
     * through these objects and call their methods.
     *
     * @return array<Movie> All movies in the repository
     */
    public function getAll(): array
    {
        // 📎 SIMPLE GETTER
        // Return the internal array containing all Movie objects
        // This allows other classes to display or process all movies
        return $this->movies;
    }

    /**
     * RETRIEVE MOVIE BY UNIQUE IDENTIFIER
     *
     * 🔍 SEARCH OPERATION:
     * This method demonstrates object lookup by unique identifier.
     * It shows how to safely handle cases where the requested item doesn't exist.
     *
     * 🚫 NULL SAFETY:
     * Returns null if not found instead of throwing an error.
     * The calling code must check for null before using the result.
     *
     * 🔗 UNION TYPES (PHP 8+):
     * The return type "Movie | null" means this method can return
     * either a Movie object OR null (if not found).
     *
     * @param int $id The unique identifier of the movie to find
     * @return Movie|null The movie object if found, null if not found
     */
    public function getById(int $id): Movie | null
    {
        // 🔄 SEARCH THROUGH COLLECTION
        // Loop through all movies looking for a matching ID
        foreach ($this->movies as $movie) {
            // 🎯 MATCH CHECK
            // Use strict comparison (===) to ensure exact match
            if ($movie->getId() === $id) {
                // ✅ FOUND - RETURN IMMEDIATELY
                return $movie;
            }
        }

        // 🚫 NOT FOUND
        // If we've checked all movies and found no match, return null
        // The calling code should check: if ($movie !== null) { ... }
        return null;
    }

    /**
     * RETRIEVE MOVIE BY TITLE - DEMONSTRATING FUNCTIONAL PROGRAMMING
     *
     * 🎯 ADVANCED TECHNIQUE:
     * This method uses array_filter() with an anonymous function (closure)
     * to find movies by title. This is a more functional programming approach.
     *
     * 📝 FUNCTIONAL VS IMPERATIVE:
     * - Imperative: Write loops manually (like getById above)
     * - Functional: Use built-in functions that express intent clearly
     *
     * ⚠️ POTENTIAL BUG:
     * This code assumes a result exists ([0]) without checking!
     * In production, you should verify the array isn't empty first.
     *
     * 🔗 CLOSURE/ANONYMOUS FUNCTION:
     * function ($movie) use ($title) creates a function that:
     * - Takes a $movie parameter
     * - "Captures" the $title variable from the parent scope
     * - Returns true/false for each movie
     *
     * @param string $title The title to search for
     * @return Movie|null The movie if found, null if not found
     */
    public function getByTitle(string $title): Movie|null
    {
        // 📡 FUNCTIONAL FILTERING
        // array_filter() creates a new array containing only elements
        // where the callback function returns true

        $filteredMovies = array_filter($this->movies, function ($movie) use ($title) {
            // 🔍 COMPARISON LOGIC
            // This anonymous function is called for each movie
            // It compares the movie's title with our search term
            // Note: This calls getTitle() which may not exist - should be getName()
            return $movie->getName() === strtoupper($title); // Compare with uppercase
        });

        // 📊 RETURN FIRST MATCH
        // array_filter returns an array, but we want just one movie
        // Check if any results were found before accessing [0]
        if (empty($filteredMovies)) {
            return null;
        }

        // Return the first (and likely only) match
        return array_values($filteredMovies)[0];
    }
}
