<?php

declare(strict_types=1);

/*
EDUCATIONAL EXAMPLE: Web Application Testing Suite

This file demonstrates testing strategies for web applications, specifically
for the MVC (Model-View-Controller) architecture introduced in Module 05.

TESTING EVOLUTION FROM MODULE 04 TO MODULE 05:
- Module 04: Test individual methods and console output
- Module 05: Test HTTP requests, routing, sessions, and form processing

KEY WEB TESTING CONCEPTS:
1. Router Testing: Verify URL patterns match correct controllers
2. Controller Testing: Test HTTP request/response cycles
3. Session Testing: Verify state persistence between requests
4. Form Testing: Validate input processing and validation
5. Integration Testing: Test complete user workflows

These tests prepare you for professional web development testing practices.
*/

require_once '../Book.php';
require_once '../Router.php';
require_once '../BookController.php';

use PHPUnit\Framework\TestCase;

/**
 * Web Application Testing Suite
 * 
 * EDUCATIONAL PURPOSE:
 * This test suite demonstrates how to test web applications that use:
 * - MVC architecture
 * - Routing systems
 * - Session management
 * - Form processing
 * 
 * These patterns are fundamental to modern web development.
 */
final class WebApplicationTest extends TestCase
{
    private Router $router;
    private BookController $controller;

    /**
     * Setup method runs before each test
     * 
     * TESTING BEST PRACTICE:
     * Initialize fresh objects for each test to ensure test isolation.
     * This prevents tests from affecting each other.
     */
    protected function setUp(): void
    {
        // Start a clean session for each test
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear any existing session data
        $_SESSION = [];

        // Initialize our application components
        $this->router = new Router();
        $this->controller = new BookController();
    }

    /**
     * Cleanup after each test
     * 
     * IMPORTANT:
     * Always clean up global state (sessions, superglobals) after tests
     */
    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
    }

    /**
     * Test 1: Book Model Creation and Properties
     * 
     * CONCEPT: Unit testing for the data model
     * This test verifies that our Book class works correctly with web form data.
     */
    public function testCanCreateBookFromFormData(): void
    {
        // Simulate form submission data (what comes from $_POST)
        $formData = [
            'title' => 'The Hobbit',
            'author' => 'J.R.R. Tolkien',
            'isbn' => '978-0-395-07122-1',
            'genre' => 'Fantasy',
            'ageRating' => 'PG-13',
            'pages' => '310',  // Note: Form data comes as strings
            'publisher' => 'Houghton Mifflin',
            'publishedAt' => '1937-09-21'  // HTML date format
        ];

        // Create book object (same as in BookController::store())
        $publishedDate = DateTime::createFromFormat('Y-m-d', $formData['publishedAt']);

        $book = new Book(
            title: $formData['title'],
            author: $formData['author'],
            isbn: $formData['isbn'],
            genre: $formData['genre'],
            ageRating: $formData['ageRating'],
            pages: (int) $formData['pages'],
            publisher: $formData['publisher'],
            publishedAt: $publishedDate
        );

        // Verify the book was created correctly
        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('The Hobbit', $book->title);
        $this->assertEquals('J.R.R. Tolkien', $book->author);
        $this->assertEquals(310, $book->pages);
        $this->assertEquals(1937, $book->getYearPublished());
    }

    /**
     * Test 2: Session Storage and Retrieval
     * 
     * CONCEPT: Testing web application state management
     * Web apps use sessions to store data between HTTP requests.
     */
    public function testCanStoreAndRetrieveBooksFromSession(): void
    {
        // Create a test book
        $book = new Book(
            title: 'Test Book',
            author: 'Test Author',
            isbn: '123-456-789',
            genre: 'Test',
            ageRating: 'G',
            pages: 100,
            publisher: 'Test Publisher',
            publishedAt: new DateTime('2024-01-01')
        );

        // Store in session (like BookController::store() does)
        if (!isset($_SESSION['books'])) {
            $_SESSION['books'] = [];
        }
        $_SESSION['books'][] = $book;

        // Verify we can retrieve it
        $this->assertCount(1, $_SESSION['books']);
        $this->assertEquals('Test Book', $_SESSION['books'][0]->title);

        // Test that we can access by index (like BookController::show() does)
        $bookId = 0;
        $retrievedBook = $_SESSION['books'][$bookId];
        $this->assertEquals($book->title, $retrievedBook->title);
    }

    /**
     * Test 3: Router Pattern Matching
     * 
     * CONCEPT: Testing URL routing logic
     * The router maps URLs to controller actions.
     */
    public function testRouterCanMatchStaticRoutes(): void
    {
        // Test homepage route
        $this->assertTrue($this->router->matchRoute('/', '/'));

        // Test book creation route
        $this->assertTrue($this->router->matchRoute('/book', '/book'));

        // Test non-matching routes
        $this->assertFalse($this->router->matchRoute('/book', '/'));
        $this->assertFalse($this->router->matchRoute('/', '/book'));
    }

    /**
     * Test 4: Dynamic Route Parameters
     * 
     * CONCEPT: Testing parameterized URLs
     * URLs like /book/123 need to extract the ID parameter.
     */
    public function testRouterCanMatchDynamicRoutes(): void
    {
        // Test book detail route with ID parameter
        $this->assertTrue($this->router->matchRoute('/book/:id', '/book/123'));
        $this->assertTrue($this->router->matchRoute('/book/:id', '/book/0'));
        $this->assertTrue($this->router->matchRoute('/book/:id', '/book/999'));

        // Test routes that shouldn't match
        $this->assertFalse($this->router->matchRoute('/book/:id', '/book'));
        $this->assertFalse($this->router->matchRoute('/book/:id', '/book/123/edit'));
        $this->assertFalse($this->router->matchRoute('/book/:id', '/author/123'));
    }

    /**
     * Test 5: Form Validation Simulation
     * 
     * CONCEPT: Testing user input validation
     * Web applications must validate form data before processing.
     */
    public function testFormValidationRequiredFields(): void
    {
        // Simulate incomplete form data (missing required fields)
        $incompleteData = [
            'title' => '',  // Empty title
            'author' => 'Test Author',
            // Missing other fields
        ];

        // Validation logic (similar to what BookController::store() should do)
        $errors = [];

        if (empty($incompleteData['title'])) {
            $errors[] = 'Title is required';
        }

        if (!isset($incompleteData['isbn']) || empty($incompleteData['isbn'])) {
            $errors[] = 'ISBN is required';
        }

        // Verify validation catches the errors
        $this->assertNotEmpty($errors);
        $this->assertContains('Title is required', $errors);
        $this->assertContains('ISBN is required', $errors);
    }

    /**
     * Test 6: HTTP Method Validation
     * 
     * CONCEPT: Testing proper HTTP method usage
     * GET requests show forms, POST requests process them.
     */
    public function testHttpMethodValidation(): void
    {
        // Simulate GET request to /book (should show form)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('GET', $_SERVER['REQUEST_METHOD']);

        // Simulate POST request to /book (should process form)
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('POST', $_SERVER['REQUEST_METHOD']);

        // In a real application, the router would check this
        $isGetRequest = $_SERVER['REQUEST_METHOD'] === 'GET';
        $isPostRequest = $_SERVER['REQUEST_METHOD'] === 'POST';

        $this->assertFalse($isGetRequest);
        $this->assertTrue($isPostRequest);
    }

    /**
     * Test 7: Complete User Workflow Integration Test
     * 
     * CONCEPT: End-to-end testing
     * Test the complete flow from form submission to data storage.
     */
    public function testCompleteBookCreationWorkflow(): void
    {
        // Step 1: Start with empty book collection
        $this->assertEmpty($_SESSION['books'] ?? []);

        // Step 2: Simulate form submission
        $_POST = [
            'title' => 'Integration Test Book',
            'author' => 'Test Author',
            'isbn' => '999-888-777',
            'genre' => 'Testing',
            'ageRating' => 'G',
            'pages' => '200',
            'publisher' => 'Test Publishers',
            'publishedAt' => '2024-01-15'
        ];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Step 3: Process the form (simulate BookController::store())
        if (!empty($_POST['title']) && !empty($_POST['author'])) {
            $publishedDate = DateTime::createFromFormat('Y-m-d', $_POST['publishedAt']);

            $book = new Book(
                title: $_POST['title'],
                author: $_POST['author'],
                isbn: $_POST['isbn'],
                genre: $_POST['genre'],
                ageRating: $_POST['ageRating'],
                pages: (int) $_POST['pages'],
                publisher: $_POST['publisher'],
                publishedAt: $publishedDate
            );

            if (!isset($_SESSION['books'])) {
                $_SESSION['books'] = [];
            }
            $_SESSION['books'][] = $book;
        }

        // Step 4: Verify the complete workflow succeeded
        $this->assertCount(1, $_SESSION['books']);
        $this->assertEquals('Integration Test Book', $_SESSION['books'][0]->title);
        $this->assertEquals(2024, $_SESSION['books'][0]->getYearPublished());

        // Step 5: Test that we can retrieve the book by ID
        $bookId = 0;
        $this->assertArrayHasKey($bookId, $_SESSION['books']);
        $this->assertEquals('Integration Test Book', $_SESSION['books'][$bookId]->title);
    }

    /**
     * Test 8: Error Handling for Invalid Data
     * 
     * CONCEPT: Testing application robustness
     * Applications must handle invalid input gracefully.
     */
    public function testErrorHandlingForInvalidBookId(): void
    {
        // Create one book in session
        $_SESSION['books'] = [new Book(
            title: 'Only Book',
            author: 'Only Author',
            isbn: '111-222-333',
            genre: 'Solo',
            ageRating: 'G',
            pages: 150,
            publisher: 'Solo Publisher',
            publishedAt: new DateTime('2024-01-01')
        )];

        // Test valid ID
        $this->assertArrayHasKey(0, $_SESSION['books']);

        // Test invalid IDs (what BookController::show() should handle)
        $this->assertArrayNotHasKey(1, $_SESSION['books']);
        $this->assertArrayNotHasKey(-1, $_SESSION['books']);
        $this->assertArrayNotHasKey(999, $_SESSION['books']);

        // In a real application, invalid IDs should trigger 404 errors
        $invalidId = 999;
        $bookExists = isset($_SESSION['books'][$invalidId]);
        $this->assertFalse($bookExists);
    }
}

/*
PROFESSIONAL TESTING ENHANCEMENTS:

For production applications, you would also test:

1. Security:
   - XSS prevention: Test HTML escaping
   - CSRF protection: Test token validation
   - Input sanitization: Test malicious input handling

2. Performance:
   - Session size limits
   - Memory usage under load
   - Response time benchmarks

3. Accessibility:
   - Screen reader compatibility
   - Keyboard navigation
   - Form label associations

4. Browser Compatibility:
   - JavaScript functionality
   - CSS rendering
   - Form submission behavior

5. User Experience:
   - Error message clarity
   - Navigation flow logic
   - Mobile responsiveness

EXAMPLE ADVANCED TEST:

public function testXSSPrevention(): void
{
    $maliciousInput = '<script>alert("XSS")</script>';
    $_POST['title'] = $maliciousInput;
    
    // Process form...
    
    // Verify output is escaped
    $this->assertStringNotContains('<script>', $escapedOutput);
    $this->assertStringContains('&lt;script&gt;', $escapedOutput);
}

These tests demonstrate the evolution from simple console application testing
to comprehensive web application testing strategies.
*/
