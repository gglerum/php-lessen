<?php

/**
 * Class BookController is responsible for handling book related requests.
 */
class BookController
{

    /**
     * Shows list of books
     */
    public static function index(): void
    {
        $books = $_SESSION['books'] ?? [];

        include_once 'html/index.html';
    }

    /**
     * Gets the book and includes the HTML file. The $book variable is available in the included file.
     *
     * @param Book $book The book to show
     * @return void
     */
    public static function show(int $id): void
    {
        if (isset($_SESSION['books'][$id])) {
            $book = $_SESSION['books'][$id];

            include_once 'html/book.html';
        } else {
            print 'Book not found';
        }
    }

    /**
     * Shows book creation form.
     * @return void
     */
    public static function createBook(): void
    {
        include_once 'html/form.html';
    }

    /**
     * Stores the book in the session and redirects to the book details.
     * @return void
     */
    public static function store(): void
    {
        $data = $_POST;
        $book = new Book(
            title: $data['title'],
            author: $data['author'],
            isbn: $data['isbn'],
            genre: $data['genre'],
            ageRating: $data['ageRating'],
            pages: $data['pages'],
            publisher: $data['publisher'],
            publishedAt: DateTime::createFromFormat('Y-m-d', $data['publishedAt'])
        );

        //create books session array if it doesn't exist
        if (!isset($_SESSION['books'])) {
            $_SESSION['books'] = [];
        }

        //store book in session
        $id = count($_SESSION['books']) + 1;
        $_SESSION['books'][$id] = $book;

        //redirect to book details
        header('location: /book/' . $id);
    }
}
