<?php

namespace Hacklabfrl\Moviemanagement;

/**
 * MOVIE ENTITY CLASS - REPRESENTING A REAL-WORLD OBJECT
 *
 * 🎯 LEARNING OBJECTIVES:
 * This class demonstrates several fundamental Object-Oriented Programming concepts:
 *
 * 1. **ENTITY/VALUE OBJECT**: Represents a "thing" in our domain (a movie)
 * 2. **ENCAPSULATION**: Private properties with controlled public access
 * 3. **STATIC PROPERTIES**: Class-level data shared by all instances
 * 4. **PHP 8 CONSTRUCTOR PROMOTION**: Modern PHP syntax for cleaner code
 * 5. **NAMESPACES**: Organizing code to avoid naming conflicts
 *
 * 🏗️ ARCHITECTURE ROLE:
 * Movie is a "data class" or "entity" - it represents a real-world concept
 * and encapsulates all the data and behavior related to that concept.
 *
 * 🔍 DESIGN PATTERN: ENTITY
 * This follows the "Entity" pattern - objects that have identity and represent
 * real-world concepts in your domain.
 */
class Movie
{
    // 🔢 STATIC PROPERTY - SHARED ACROSS ALL INSTANCES
    // Static properties belong to the CLASS, not to individual objects.
    // All Movie objects share this same counter to generate unique IDs.
    // Every time we create a new Movie, this number increases.
    private static int $idIncrement = 0;

    // 🆔 UNIQUE IDENTIFIER
    // Each movie needs a unique ID to distinguish it from other movies.
    // This is set automatically in the constructor using the static counter.
    private int $id;

    /**
     * PHP 8 CONSTRUCTOR PROPERTY PROMOTION
     *
     * This modern PHP syntax does THREE things in one line:
     * 1. Declares the property (private string $name)
     * 2. Adds it as a constructor parameter
     * 3. Assigns the parameter to the property ($this->name = $name)
     *
     * ⚡ OLD WAY (before PHP 8):
     * private string $name;
     * public function __construct(string $name) {
     *     $this->name = $name;
     * }
     *
     * ✨ NEW WAY (PHP 8+):
     * public function __construct(private string $name) {}
     *
     * @param string $name The movie title
     * @param string $director The director's name
     * @param float $rating The movie rating (0.0 to 10.0)
     */
    public function __construct(
        private string $name,
        private string $director,
        private float $rating
    ) {
        // 🆔 AUTOMATIC ID GENERATION
        // Pre-increment (++static::) increases the counter BEFORE using it
        // This ensures each movie gets a unique, incrementing ID: 1, 2, 3, etc.
        // static:: refers to the class this method is called on (Movie)
        $this->id = ++static::$idIncrement;
    }

    /**
     * GET UNIQUE IDENTIFIER
     *
     * 🔑 ACCESS CONTROL:
     * This is a "getter" method - it provides READ-only access to private data.
     * The ID is set once in the constructor and never changes (immutability).
     *
     * @return int The unique identifier for this movie
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * GET FORMATTED MOVIE NAME
     *
     * 🎯 ENCAPSULATION EXAMPLE:
     * Notice this method doesn't just return the raw name - it applies formatting!
     * This demonstrates that objects should control how their data is presented.
     * The internal storage ($this->name) can be different from the public interface.
     *
     * 💡 BUSINESS LOGIC:
     * Converting to uppercase might be a business requirement - maybe the system
     * always displays movie titles in caps. By putting this logic in the getter,
     * we ensure it's applied consistently everywhere.
     *
     * @return string The movie name in uppercase
     */
    public function getName(): string
    {
        // strtoupper() converts the string to all uppercase letters
        return strtoupper($this->name);
    }

    /**
     * GET FORMATTED DISPLAY TEXT
     *
     * 🎨 PRESENTATION LOGIC:
     * This method demonstrates how objects should know how to represent themselves.
     * Instead of having display logic scattered throughout the application,
     * the Movie object itself knows how to create its overview text.
     *
     * 📄 SINGLE RESPONSIBILITY:
     * Each object is responsible for its own presentation. If we want to change
     * how movies are displayed, we only need to modify this one method.
     *
     * 🔗 STRING CONCATENATION:
     * PHP uses the dot (.) operator to join strings together.
     * This creates a formatted string with movie details separated by dashes.
     *
     * @return string A formatted overview of the movie (name - director - rating)
     */
    public function getOverviewText(): string
    {
        // Concatenate the movie details with ' - ' separators
        // Uses the original $name (not uppercase) for display
        return $this->name . ' - ' . $this->director . ' - ' . $this->rating;
    }
}
