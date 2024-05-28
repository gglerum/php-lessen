<?php
require_once 'DBEntity.php';
/**
 * Class that represents a book in the library system
 * the class has properties that are public and readonly, which means they can be accessed from outside the class
 * without the need of getters, but they can't be modified from outside the class.
 * 
 * Classes like these are ideal for transferring data between different parts of the system, like from the database to the view.
 */
class Book extends DBEntity
{
    protected static string $table = 'books';

    /**
     * We use property promotion so we do not have to declare the properties and set them in the body
     * of the constructor
     */
    public function __construct(
        public readonly int $id,
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

    /**
     * Represents a builder for creating instances of the Book class.
     *
     * This builder allows you to set various properties of a book and then build an instance of the Book class.
     * The builder pattern is used to simplify the creation of complex objects with many optional parameters.
     *
     * Example usage:
     * $book = Book::builder()
     *     ->title('The Great Gatsby')
     *     ->author('F. Scott Fitzgerald')
     *     ->isbn('978-3-16-148410-0')
     *     ->genre('Fiction')
     *     ->ageRating('PG-13')
     *     ->pages(218)
     *     ->publisher('Scribner')
     *     ->publishedAt(new DateTime('1925-04-10'))
     *     ->build();
     *
     * @package librarysystem
     * @subpackage Book
     */
    /**
     * Represents a builder class for creating Book objects.
     *
     * This class provides a fluent interface for setting the properties of a Book object
     * and then building the object using the `build` method.
     *
     * @return \Closure A closure that returns a new instance of the builder class.
     */
    public static function builder()
    {
        return new class
        {
            private int $id = 0;
            private string $title;
            private string $author;
            private string $isbn;
            private string $genre;
            private string $ageRating = 'PG';
            private int $pages;
            private string $publisher;
            private DateTime $publishedAt;

            public function id(int $id): self
            {
                $this->id = $id;
                return $this;
            }

            public function title(string $title): self
            {
                $this->title = $title;
                return $this;
            }

            public function author(string $author): self
            {
                $this->author = $author;
                return $this;
            }

            public function isbn(string $isbn): self
            {
                $this->isbn = $isbn;
                return $this;
            }

            public function genre(string $genre): self
            {
                $this->genre = $genre;
                return $this;
            }

            public function age_rating(string $ageRating): self
            {
                $this->ageRating = $ageRating;
                return $this;
            }

            public function pages(int $pages): self
            {
                $this->pages = $pages;
                return $this;
            }

            public function publisher(string $publisher): self
            {
                $this->publisher = $publisher;
                return $this;
            }

            public function published_at(string $dateTime): self
            {
                $this->publishedAt = DateTime::createFromFormat('Y-m-d h:m:i', $dateTime);
                return $this;
            }

            public function build(): Book
            {
                return new Book(
                    $this->id,
                    $this->title,
                    $this->author,
                    $this->isbn,
                    $this->genre,
                    $this->ageRating,
                    $this->pages,
                    $this->publisher,
                    $this->publishedAt
                );
            }
        };
    }
}
