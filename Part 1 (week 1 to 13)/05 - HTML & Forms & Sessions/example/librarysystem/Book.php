<?php

/**
 * Class that represents a book in the library system
 * the class has properties that are public and readonly, which means they can be accessed from outside the class
 * without the need of getters, but they can't be modified from outside the class.
 * 
 * Classes like these are ideal for transferring data between different parts of the system, like from the database to the view.
 */
class Book
{
    /**
     * We use property promotion so we do not have to declare the properties and set them in the body
     * of the constructor
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
    ) { /* property promotion through constructor */
    }

    /**
     * Returns the year of publication
     * @return string
     */
    public function getYearPublished(): string
    {
        return $this->publishedAt->format('Y');
    }
}
