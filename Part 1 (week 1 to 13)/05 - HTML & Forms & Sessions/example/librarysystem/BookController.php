<?php

/**
 * EDUCATIONAL EXAMPLE: MVC Controller for Book Management
 *
 * This is the "Controller" in the MVC (Model-View-Controller) pattern.
 * Controllers handle the business logic and coordinate between Models (data)
 * and Views (presentation).
 *
 * KEY WEB DEVELOPMENT CONCEPTS:
 *
 * 1. **Controller Responsibility**: Handle user requests and coordinate responses
 * 2. **Session Management**: Store data between page requests
 * 3. **Form Processing**: Handle GET (display) and POST (submit) requests
 * 4. **Template Integration**: Include HTML files with PHP variables
 * 5. **HTTP Redirects**: Guide user flow after form submissions
 *
 * EVOLUTION FROM MODULE 04:
 * - Module 04: Main class with console menus and direct user interaction
 * - Module 05: Controller with web requests and HTTP responses
 *
 * MVC PATTERN EXPLANATION:
 * - Model (Book.php): Represents data and business rules
 * - View (HTML files): Displays data to users
 * - Controller (this file): Handles user input and coordinates Model/View
 *
 * This separation makes the code more maintainable and testable.
 */
class BookController
{
    /**
     * HOMEPAGE: Display list of all books
     *
     * WHEN CALLED: GET / (user visits the homepage)
     *
     * MVC FLOW:
     * 1. Controller retrieves data from session (acting as Model layer)
     * 2. Controller passes data to View (HTML template)
     * 3. View renders HTML and sends to user's browser
     *
     * SESSION USAGE:
     * Unlike Module 04's global variables, web applications use sessions
     * to store data between requests. Each user gets their own session.
     */
    public static function index(): void
    {
        // Step 1: Retrieve books from session storage
        // $_SESSION is like a global variable that persists between page requests
        // The ?? [] means "use empty array if no books exist yet"
        $books = $_SESSION['books'] ?? [];

        // Step 2: Include the view template
        // The $books variable is now available inside the HTML file
        // This is how we pass data from Controller to View
        include_once 'html/index.html';
    }

    /**
     * BOOK DETAILS: Display complete information for a specific book
     *
     * WHEN CALLED: GET /book/1 (user clicks on a book link)
     *
     * URL PARAMETERS:
     * The router extracts the book ID from the URL and passes it here.
     * "/book/1" → $id = 1, "/book/2" → $id = 2, etc.
     *
     * ERROR HANDLING:
     * Unlike console apps, web apps must handle invalid requests gracefully.
     * We check if the requested book exists before trying to display it.
     *
     * @param int $id The book ID from the URL (/book/1, /book/2, etc.)
     */
    public static function show(int $id): void
    {
        // Step 1: Check if the requested book exists in our session
        if (isset($_SESSION['books'][$id])) {
            // Step 2: Retrieve the specific book object
            $book = $_SESSION['books'][$id];

            // Step 3: Pass book data to the view template
            // The $book variable is now available inside book.html
            include_once 'html/book.html';
        } else {
            // Step 4: Handle invalid book IDs gracefully
            // In a professional app, this would be a proper 404 error page
            print 'Book not found';
        }
    }

    /**
     * ADD BOOK FORM: Display the form for adding a new book
     *
     * WHEN CALLED: GET /book (user clicks "Add Book" link)
     *
     * FORM DISPLAY vs FORM PROCESSING:
     * This method shows the form (GET request).
     * The store() method processes the form submission (POST request).
     * Same URL (/book) but different HTTP methods = different functionality.
     */
    public static function createBook(): void
    {
        // Simply display the form - no data processing needed
        // The form will POST to the same URL when submitted
        include_once 'html/form.html';
    }

    /**
     * FORM PROCESSING: Handle book creation from form submission
     *
     * WHEN CALLED: POST /book (user submits the add book form)
     *
     * FORM DATA FLOW:
     * 1. User fills out form and clicks submit
     * 2. Browser sends POST request with form data in $_POST
     * 3. Controller creates Book object from form data
     * 4. Controller stores book in session
     * 5. Controller redirects user to view the new book
     *
     * SESSION STORAGE vs DATABASE:
     * In this educational example, we store books in the session.
     * In real applications, you'd save to a database instead.
     *
     * REDIRECT AFTER POST:
     * After processing a form, always redirect to prevent duplicate submissions
     * if the user refreshes the page. This is called "POST-Redirect-GET" pattern.
     */
    public static function store(): void
    {
        // Step 1: Retrieve form data from $_POST superglobal
        // $_POST contains all data from HTML form inputs
        $data = $_POST;

        // Step 2: Create a Book object from the form data
        // Using PHP 8.1 named parameters for clarity
        // DateTime::createFromFormat converts the HTML date input to a DateTime object
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

        // Step 3: Initialize session storage if it doesn't exist yet
        // This ensures we have an array to store books in
        if (!isset($_SESSION['books'])) {
            $_SESSION['books'] = [];
        }

        // Step 4: Generate a unique ID and store the book
        // In a real app, the database would generate IDs automatically
        $id = count($_SESSION['books']) + 1;
        $_SESSION['books'][$id] = $book;

        // Step 5: Redirect to the book details page
        // This implements the POST-Redirect-GET pattern
        // User sees the new book instead of the form submission confirmation
        header('location: /book/' . $id);
    }
}
