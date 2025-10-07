<?php

namespace Hacklabfrl\Moviemanagement;

/**
 * MOVIE CONTROLLER CLASS - BUSINESS LOGIC COORDINATOR
 *
 * 🎯 LEARNING OBJECTIVES:
 * This class demonstrates the "Controller Pattern" from MVC (Model-View-Controller):
 *
 * 1. **DEPENDENCY INJECTION**: Receiving dependencies through the constructor
 * 2. **SEPARATION OF CONCERNS**: Controller coordinates but doesn't store data
 * 3. **DATA TRANSFORMATION**: Converting raw input into domain objects
 * 4. **BUSINESS LOGIC**: Processing and validating data before storage
 * 5. **SINGLE RESPONSIBILITY**: One clear job - handle movie operations
 *
 * 🏗️ ARCHITECTURE ROLE:
 * Controllers sit between the user interface and the data layer:
 * User Input → Controller → Repository → Data Storage
 *
 * 🔄 DESIGN PATTERN: CONTROLLER
 * - Receives user input (from forms, APIs, etc.)
 * - Validates and processes that input
 * - Coordinates with other objects to perform operations
 * - Returns results to the user interface
 */
class MovieController
{
    /**
     * DEPENDENCY INJECTION CONSTRUCTOR
     *
     * 🔧 CONSTRUCTOR INJECTION:
     * Instead of creating a MovieRepository inside this class,
     * we receive it as a dependency. This makes our code:
     * - More flexible (can inject different repositories)
     * - Easier to test (can inject mock repositories)
     * - Loosely coupled (doesn't depend on concrete implementation)
     *
     * 💡 PHP 8 CONSTRUCTOR PROMOTION:
     * The "private MovieRepository $movieRepository" syntax:
     * 1. Declares a private property
     * 2. Adds it as a constructor parameter
     * 3. Assigns the parameter to the property
     *
     * @param MovieRepository $movieRepository The repository for movie data operations
     */
    public function __construct(private MovieRepository $movieRepository) {}

    /**
     * STORE NEW MOVIE - DEMONSTRATING DATA PROCESSING PIPELINE
     *
     * 📥 CONTROLLER RESPONSIBILITY:
     * This method shows the typical controller workflow:
     * 1. Receive raw data (usually from forms or API requests)
     * 2. Validate and process the data
     * 3. Create domain objects (Movie)
     * 4. Delegate storage to the repository
     * 5. Return success/failure response
     *
     * 🔍 DATA TRANSFORMATION:
     * Notice how we transform an array of raw data into a proper Movie object.
     * This is where you'd typically add validation, sanitization, and business rules.
     *
     * 💡 ARRAY PARAMETER:
     * Using an array parameter makes this method flexible - it can handle
     * data from forms, JSON APIs, or any other source that provides key-value pairs.
     *
     * ⚠️ POTENTIAL IMPROVEMENTS:
     * - Add data validation (required fields, valid rating range, etc.)
     * - Add error handling (what if creation fails?)
     * - Return meaningful responses (success/error messages)
     * - Add logging for audit trails
     *
     * @param array $data Associative array containing movie data with keys: 'name', 'Director', 'rating'
     * @return void
     */
    public function store(array $data)
    {
        // 🏗️ OBJECT CONSTRUCTION FROM RAW DATA
        // Transform the raw array data into a proper Movie object
        // Note: Using 'Director' (capital D) to match the array key from the calling code
        $movie = new Movie($data['name'], $data['Director'], $data['rating']);

        // 💾 DELEGATE TO REPOSITORY
        // The controller doesn't handle storage directly - that's the repository's job!
        // This separation allows us to change storage mechanisms without affecting the controller
        $this->movieRepository->add($movie);

        // 📝 TODO: In a real application, you'd want to:
        // - Return the created movie's ID
        // - Handle potential errors (duplicate names, invalid data, etc.)
        // - Log the operation for audit purposes
        // - Send confirmation messages to the user
    }
}
