<?php

/**
 * EDUCATIONAL EXAMPLE: Book Model Class (Data Representation)
 *
 * This is the "Model" in the MVC (Model-View-Controller) pattern.
 * Models represent data and encapsulate business rules.
 *
 * KEY OBJECT-ORIENTED CONCEPTS FROM MODULE 04:
 *
 * 1. **Encapsulation**: Data is bundled with methods that operate on it
 * 2. **Constructor Property Promotion**: Modern PHP syntax for cleaner code
 * 3. **Readonly Properties**: Data integrity and immutable objects
 * 4. **Business Logic Methods**: Calculated properties and data formatting
 *
 * EVOLUTION FROM MODULE 04:
 * - Module 04: Simple Book class with basic getters/setters
 * - Module 05: Readonly properties with calculated methods for web display
 *
 * WEB APPLICATION BENEFITS:
 * - Data validation happens in one place
 * - Consistent formatting across all web pages
 * - Easy to test business logic separately from web interface
 * - Ready for database integration in future modules
 *
 * This class represents a "Data Transfer Object" (DTO) - a simple container
 * for moving data between different parts of the system, like from the database
 * to the view templates.
 */
class Book
{
    /**
     * MODERN PHP CONSTRUCTOR: Property Promotion with Readonly
     *
     * This syntax combines several concepts:
     * 1. **Property Declaration**: Defines the class properties
     * 2. **Constructor Parameters**: Accepts data when creating objects
     * 3. **Property Assignment**: Automatically assigns parameters to properties
     * 4. **Readonly Enforcement**: Properties can't be changed after creation
     *
     * READONLY BENEFITS:
     * - Data integrity: Once created, book data can't be accidentally modified
     * - Predictable behavior: Methods always return the same values
     * - Thread safety: Multiple users can access the same book safely
     * - Immutable design: Follows functional programming principles
     *
     * PUBLIC READONLY vs PRIVATE + GETTERS:
     * Modern PHP allows public readonly properties, eliminating the need
     * for simple getter methods like getTitle(), getAuthor(), etc.
     * The properties can be accessed directly but not modified.
     *
     * @param string $title Book title for display in web pages
     * @param string $author Author name for listing and search functionality
     * @param string $isbn International Standard Book Number for uniqueness
     * @param string $genre Category for filtering and organization
     * @param string $ageRating Content appropriateness indicator
     * @param int $pages Number of pages for book details
     * @param string $publisher Publishing company information
     * @param DateTime $publishedAt Publication date for sorting and display
     */
    public function __construct(
        public readonly string $title,
        public readonly string $author,
        public readonly string $isbn,
        public readonly string $genre,
        public readonly string $ageRating,
        public readonly int $pages,
        public readonly string $publisher,
        public readonly DateTime $publishedAt,
    ) {
        /* 
         * Constructor body is empty because property promotion
         * automatically handles the assignment:
         * $this->title = $title;
         * $this->author = $author;
         * // etc...
         */
    }

    /**
     * BUSINESS LOGIC METHOD: Calculate publication year for display
     *
     * This demonstrates how Models can contain business logic
     * beyond simple data storage. Instead of formatting dates
     * in every template, we centralize the logic here.
     *
     * WEB DISPLAY BENEFITS:
     * - Consistent date formatting across all web pages
     * - Easy to change format in one place
     * - Testable logic separate from HTML templates
     * - Reusable in different contexts (lists, details, search results)
     *
     * EXAMPLE USAGE IN TEMPLATES:
     * <p>Published: <?= $book->getYearPublished() ?></p>
     * Outputs: Published: 2023
     *
     * @return string The publication year as a 4-digit string
     */
    public function getYearPublished(): string
    {
        return $this->publishedAt->format('Y');
    }

    /**
     * FUTURE EXPANSION EXAMPLES:
     *
     * As your application grows, you might add methods like:
     *
     * public function getFormattedPublishDate(): string
     * {
     *     return $this->publishedAt->format('F j, Y'); // "January 1, 2023"
     * }
     *
     * public function isNewRelease(): bool
     * {
     *     $oneYearAgo = new DateTime('-1 year');
     *     return $this->publishedAt > $oneYearAgo;
     * }
     *
     * public function getPageCount(): string
     * {
     *     return number_format($this->pages) . ' pages';
     * }
     *
     * public function isAppropriateForAge(int $userAge): bool
     * {
     *     // Business logic for age rating validation
     * }
     */
}
