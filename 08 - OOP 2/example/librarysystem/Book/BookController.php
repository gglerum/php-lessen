<?php

/**
 * This controller is responsible for handling all the user book actions
 */
class BookController
{
    /**
     * Shows the form to create a book
     *
     * @return void
     */
    public static function create(): void
    {
        include_once 'html/form.html';
    }

    /**
     * Stores the submitted form data in the database
     *
     * @return void
     */
    public static function store(): void
    {
        $id = Book::insert($_POST);
        header('location: /?type=book&action=show&id=' . $id);
    }

    /**
     * Shows a single book
     *
     * @return void
     */
    public static function show(): void
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $book = Book::load($_GET['id']);
        require_once 'html/show.html';
    }

    /**
     * Shows a list of the available books
     *
     * @return void
     */
    public static function index(): void
    {
        $books = Book::all();
        require_once 'html/index.html';
    }

    /**
     * Handles borrowing of the book by a customer
     *
     * @return void
     */
    public static function borrow(): void
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $book = Book::load($_GET['id']);
        if ($book->isAvailable()) {
            $book->borrowItem();
        }
        header('location: /?type=book&action=show&id=' . $book->id);
    }

    /**
     * Handles returning of the book by a customer
     *
     * @return void
     */
    public static function returnItem(): void
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $book = Book::load($_GET['id']);
        $book->returnItem();
        header('location: /?type=book&action=show&id=' . $book->id);
    }
}
