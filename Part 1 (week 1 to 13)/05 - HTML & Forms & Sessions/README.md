# Web Applications: Bridging Objects with User Interfaces

Outstanding work mastering Object-Oriented Programming! Now you'll discover how to transform your organized classes into interactive web applications. This isn't just about adding HTML—it's about connecting user interfaces with your carefully structured objects using professional web development patterns.

## 🎯 What You'll Learn

By the end of this module, you'll understand:

- **HTML fundamentals** - Structure and semantic markup for professional web pages
- **Form handling** - Collecting and processing user input securely
- **Sessions and state management** - Maintaining user data across page requests
- **MVC architecture** - The industry-standard pattern for organizing web applications
- **Basic routing** - Directing user requests to the right code
- **Form validation** - Ensuring data quality and security

**Advanced topics** (foundation for later modules):
- Complex routing patterns and middleware
- Advanced security measures and CSRF protection
- Database integration with form data
- Professional templating systems

Don't worry if web development feels different from console applications—we'll connect your OOP knowledge to web interfaces step by step!

**Testing Approach Evolution**:
```php
// Module 4: Console application testing
class BookTest extends PHPUnit\Framework\TestCase {
    public function testBookCreation() {
        $book = new Book("1984", "George Orwell");
        $this->assertEquals("1984", $book->getTitle());
    }
}

// Module 5: Same logic, web context
class BookControllerTest extends PHPUnit\Framework\TestCase {
    public function testFormProcessing() {
        $_POST = ['title' => '1984', 'author' => 'George Orwell'];
        
        BookController::store(); // Processes form data
        
        $this->assertContains('1984', $_SESSION['books']);
    }
}
```

**What This Shows**:
- **Your testing skills transfer directly** to web development
- **Business logic testing remains unchanged** (Book class tests)
- **Additional web-layer testing** for forms, sessions, and routing
- **Same PHPUnit knowledge** applies in new contexts

**Testing Levels in Web Applications**:
- **Unit Tests**: Your Book and validation logic (unchanged from Module 4)
- **Integration Tests**: Testing form processing and object creation together  
- **Web-Specific Tests**: Validating form handling, session management, and routingser data across page requests
- **MVC architecture** - The industry-standard pattern for organizing web applications
- **Basic routing** - Directing user requests to the right code
- **Form validation** - Ensuring data quality and security

**Advanced topics** (foundation for later modules):
- Complex routing patterns and middleware
- Advanced security measures and CSRF protection
- Database integration with form data
- Professional templating systems

Don't worry if web development feels different from console applications—we'll connect your OOP knowledge to web interfaces step by step!

## 🌉 Bridge: From Console Objects to Web Applications

Coming from Module 04 (OOP 1), you've mastered organizing code into classes and objects. Web development is the next evolutionary step—instead of interacting through console input/output, you'll create **HTML interfaces** that users can interact with through their browsers.

### 🧠 The Mental Shift: From Console Commands to Web Requests

**In Module 04, you thought in console interactions:**
```php
// CONSOLE APPROACH (Module 04 style)
$bookController = new BookController($repository);

echo "Enter book title: ";
$title = trim(fgets(STDIN));

echo "Enter author: ";
$author = trim(fgets(STDIN));

$book = new Book($title, $author);
$bookController->addBook($book);

echo "Book added successfully!\n";
```

**In Module 05, you'll think in web interactions:**
```php
// WEB APPROACH (Module 05 style)
// User submits HTML form, Router calls BookController::store()
public static function store(): void
{
    // Form data automatically available in $_POST
    $data = $_POST;
    
    // Create Book with modern constructor property promotion
    $book = new Book(
        title: $data['title'],
        author: $data['author'],
        isbn: $data['isbn'],
        genre: $data['genre'],
        ageRating: $data['ageRating'],
        pages: (int)$data['pages'],
        publisher: $data['publisher'],
        publishedAt: DateTime::createFromFormat('Y-m-d', $data['publishedAt'])
    );
    
    // Store in session and redirect
    $_SESSION['books'][] = $book;
    header('Location: /books');
}
```

### 🔄 Key Conceptual Shifts

| **Module 04 Concept** | **Module 05 Evolution** | **Why This Helps** |
|----------------------|-------------------------|-------------------|
| **Console input/output** | **HTML forms and pages** | Visual, user-friendly interfaces |
| **Single script execution** | **Multiple page requests** | Stateful web applications |
| **Direct object creation** | **Form data processing** | User-driven object creation |
| **Echo statements** | **HTML templates** | Professional presentation |
| **Immediate feedback** | **Session-based messaging** | Persistent user experience |
| **Repository pattern** | **Session storage** | Simplified data persistence for learning |
| **PHPUnit testing** | **Web-specific testing** | Testing user interactions and workflows |
| **require_once files** | **Autoloading and routing** | Professional web application structure |

### 🔗 Your Module 4 Knowledge Still Applies!

**Everything you learned in Module 4 transfers directly to web development:**

**✅ Classes and Objects**: Your Book class works unchanged in web applications
```php
// Same class from Module 4 works in Module 5!
$book = new Book($title, $author, $isbn, $genre, $ageRating, $pages, $publisher, $publishedAt);
// Now created from form data instead of console input
```

**✅ Encapsulation**: Private properties and public methods work the same way
```php
// Module 4: Console validation
if (empty($title)) { echo "Title required\n"; }

// Module 5: Web validation  
if (empty($title)) { $errors[] = "Title required"; }
// Same logic, different presentation
```

**✅ File Organization**: Multiple files become even more important
```php
// Module 4: Console entry point
require_once 'Book.php';
require_once 'BookRepository.php';
$main = new Main();
$main->run();

// Module 5: Web entry point  
require_once 'Book.php';
require_once 'BookController.php';
require_once 'Router.php';
$router = new Router();
$router->processRoute();
```

**✅ Constructor Patterns**: Evolution from Module 4 to modern PHP
```php
// Module 4 style (still works!)
public function __construct($title, $author) {
    $this->title = $title;
    $this->author = $author;
}

// Module 5 evolution (readonly properties)
public function __construct(
    public readonly string $title,
    public readonly string $author,
) {}
// Same functionality, more concise syntax
```

### 📝 Essential Web Development Vocabulary

Before diving into the examples, learn these core terms:

**🌐 HTML**: HyperText Markup Language - the structure of web pages
- Creates the visual layout and form elements users interact with
- Semantic markup that gives meaning to content

**📝 Form**: HTML element that collects user input
- Text fields, buttons, dropdowns, checkboxes
- Sends data to PHP scripts for processing

**🔄 Request-Response Cycle**: How web applications work
- User submits form (request) → PHP processes data → Server sends HTML (response)
- Each page load is a new request

**🗂️ Session**: Server-side storage that persists across page requests
- Remembers user data between different pages
- Essential for login systems and shopping carts

**🛣️ Routing**: Directing URLs to specific PHP code
- `/books` goes to book listing code
- `/books/add` goes to book creation code
- Clean, professional URL structure

**🏗️ MVC (Model-View-Controller)**: Professional application architecture
- **Model**: Your classes (Book) - the same classes from Module 4!
- **View**: HTML templates - what users see (replaces console echo statements)
- **Controller**: PHP scripts that connect Models and Views (evolution of your Main class)

### 🎯 Your First Web Form (Start Here!)

Let's examine how the actual library system handles book creation. **Don't worry about understanding everything at once** - just follow along step by step:

```html
<!-- Simplified version of the actual form.html -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>
    <h1>Add a New Book</h1>
    
    <!-- Form sends data to BookController::store() via routing -->
    <form method="POST" action="/book">
        <div>
            <label for="title">Title:</label>
            <input name="title" required>
        </div>
        
        <div>
            <label for="author">Author:</label>
            <input name="author" required>
        </div>
        
        <div>
            <label for="isbn">ISBN:</label>
            <input name="isbn" required>
        </div>
        
        <div>
            <label for="genre">Genre:</label>
            <input name="genre" required>
        </div>
        
        <div>
            <label for="publishedAt">Published Date:</label>
            <input type="date" name="publishedAt" required>
        </div>
        
        <button type="submit">Add Book</button>
    </form>
</body>
</html>
```

```php
<?php
// From BookController::store() - Handles form submission
class BookController
{
    public static function store(): void
    {
        // Get form data from $_POST superglobal
        $data = $_POST;
        
        // Create Book object using modern PHP constructor
        $book = new Book(
            title: $data['title'],
            author: $data['author'],
            isbn: $data['isbn'],
            genre: $data['genre'],
            ageRating: $data['ageRating'] ?? 'All Ages',
            pages: (int)$data['pages'],
            publisher: $data['publisher'],
            publishedAt: DateTime::createFromFormat('Y-m-d', $data['publishedAt'])
        );
        
        // Store in session (in real apps, save to database)
        if (!isset($_SESSION['books'])) {
            $_SESSION['books'] = [];
        }
        
        $id = count($_SESSION['books']) + 1;
        $_SESSION['books'][$id] = $book;
        
        // Redirect to book details (POST-Redirect-GET pattern)
        header('location: /book/' . $id);
    }
}
?>
```

**What's new here?**
- `<form>` element - collects user input
- `action` attribute - where to send the data
- `method="POST"` - how to send the data
- `$_POST` superglobal - accessing submitted form data
- Your existing classes work unchanged!

**Don't worry about**: Complex validation, security, or advanced features yet. Master the basics first!

### 🔀 Why We're Using Sessions Instead of Repository Pattern

**You might notice**: Module 4 taught the Repository pattern for data management, but this module uses session storage directly. Here's why:

**Module 4 Repository Approach:**
```php
// Professional data management
$repository = new BookRepository();
$repository->add($book);
$books = $repository->getAll();
```

**Module 5 Session Approach:**
```php  
// Simplified for learning web concepts
$_SESSION['books'][] = $book;
$books = $_SESSION['books'] ?? [];
```

**Learning Rationale:**
- **Focus on Web Concepts**: Learn HTML, forms, sessions, and routing first
- **Reduced Complexity**: Fewer moving parts while mastering new concepts  
- **Gradual Progression**: Repository pattern returns in database modules
- **Same Principles**: Your objects remain unchanged - only storage mechanism differs

**Your Repository Knowledge Isn't Lost**: In real applications, you'll combine both:
- Repository pattern for database operations (Module 7+)
- Session storage for user-specific data (login status, shopping carts)
- Your Module 4 Repository skills prepare you for professional database work!

### 🔒 Understanding Form Validation and Security

Once you're comfortable with the basic form above, let's learn about protecting your application. **This is an intermediate concept** - make sure you understand the first example before moving on:

```php
<?php
// Enhanced validation example (simplified from actual BookController)
if ($_POST) {
    // INPUT VALIDATION: Check if required data exists
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    
    $errors = [];
    
    // Validate required fields
    if (empty($title)) {
        $errors[] = "Title is required";
    } elseif (strlen($title) > 100) {
        $errors[] = "Title must be less than 100 characters";
    }
    
    if (empty($author)) {
        $errors[] = "Author is required";
    } elseif (strlen($author) > 50) {
        $errors[] = "Author must be less than 50 characters";
    }
    
    if (empty($isbn)) {
        $errors[] = "ISBN is required";
    }
    
    // If validation passes, process the data
    if (empty($errors)) {
        // SANITIZE: Clean the data
        $title = htmlspecialchars($title);
        $author = htmlspecialchars($author);
        $isbn = htmlspecialchars($isbn);
        $genre = htmlspecialchars($genre);
        
        // Create Book object with validated data
        $book = new Book(
            title: $title,
            author: $author,
            isbn: $isbn,
            genre: $genre,
            ageRating: 'All Ages', // Default value
            pages: 200, // Default for demo
            publisher: 'Unknown',
            publishedAt: new DateTime()
        );
        
        // Store in session
        $_SESSION['books'][] = $book;
        
        echo "<h1>Success!</h1>";
        echo "<p>Book '{$title}' added safely!</p>";
    } else {
        // Show validation errors
        echo "<h1>Please fix these errors:</h1>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "<a href='/book'>Try again</a>";
    }
}
?>
```

**What's different here?**
- **Input validation** - checking if data is present and valid
- **Data sanitization** - cleaning data to prevent security issues
- **Error handling** - gracefully handling invalid input
- **`htmlspecialchars()`** - prevents XSS attacks by escaping HTML characters

**Why this matters:**
- **Security**: Protects against malicious input
- **Data Quality**: Ensures your objects receive valid data
- **User Experience**: Clear error messages help users fix problems

### 🗂️ Session Management: Remembering Users

Web applications need to remember information between page requests. Here's how sessions work:

```php
<?php
// login-form.php
session_start(); // Start session handling

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (in real apps, check against database)
    if ($username === 'admin' && $password === 'secret') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
```

```php
<?php
// dashboard.php - Protected page
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login-form.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <p>You logged in at: <?= htmlspecialchars($_SESSION['login_time']) ?></p>
    
    <h2>Your Books</h2>
    <!-- Display user's books here -->
    
    <a href="logout.php">Logout</a>
</body>
</html>
```

**What's happening here?**
- `session_start()` - enables session functionality
- `$_SESSION` - superglobal for storing session data
- Data persists between page requests
- Sessions enable user authentication and personalization

### 🛣️ Basic Routing: Clean URLs

The actual library system uses a professional Router class for clean URL handling:

```php
<?php
// From Router.php - Professional routing implementation
class Router
{
    // Route definitions: [method, pattern, controller action]
    private array $routes = [
        ['get', 'book/:id', [BookController::class, 'show']],     // View book details
        ['get', 'book', [BookController::class, 'createBook']],   // Show add form
        ['post', 'book', [BookController::class, 'store']],       // Process form
        ['get', '', [BookController::class, 'index']],            // Homepage
    ];
    
    public function processRoute(): void
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        
        foreach ($this->routes as $route) {
            [$routeMethod, $routePath, $routeAction] = $route;
            
            if ($method === $routeMethod && $this->matchRoute($routePath)) {
                // Extract parameters (like :id from URL)
                $params = $this->extractParameters($routePath);
                
                // Call the controller method
                [$controllerClass, $method] = $routeAction;
                call_user_func_array([$controllerClass, $method], $params);
                return;
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "<h1>Page Not Found</h1>";
    }
}
?>
```

**Why routing matters:**
- **Clean URLs**: `/books` instead of `books.php`
- **Centralized logic**: All URL handling in one place (like Module 4's Main class)
- **Professional appearance**: URLs that make sense to users
- **Easier maintenance**: Change URLs without changing file names
- **Single Responsibility**: Router has one job, just like your Module 4 classes

### 🚀 Why This is Powerful (The "Aha!" Moment)

**The Beautiful Thing**: Your Module 4 classes work unchanged in web applications!

**Module 04 console limitation:**
```php
// Console-only: Users need technical knowledge
echo "Enter book title: ";
$title = trim(fgets(STDIN));

// Create object (same as Module 5!)
$book = new Book($title, $author);
// Only developers can use this interface
```

**Module 05 web solution:**
```html
<!-- Web interface: Anyone can use this -->
<form action="/book" method="POST">
    <input type="text" name="title" placeholder="Enter book title">
    <button type="submit">Add Book</button>
</form>
```

```php
// Same Book class, different data source!
$title = $_POST['title']; // From web form instead of console
$book = new Book($title, $author); // Identical object creation
```

**The transformation:**
- **Same Business Logic**: Your Book class doesn't change at all
- **Different Interface**: Console input → HTML forms, echo → HTML pages  
- **Accessibility**: Non-technical users can interact with your objects
- **Visual feedback**: Rich HTML interfaces instead of plain text
- **Persistence**: Sessions remember user data across interactions
- **Scalability**: Multiple users can use the system simultaneously

**Key Insight**: Good OOP design (from Module 4) makes this transformation seamless. Your well-designed classes become the foundation for multiple interfaces!

## 📚 Learning Resources

**Your Main Textbook**: https://www.w3schools.com/html/ and https://www.w3schools.com/php/

**🎯 Study Plan for Web Development**: Start with HTML structure, then move to PHP integration. Each section builds on the previous ones:

### **Phase 1: HTML Foundation (Start Here!)**
Master the structure and elements of web pages:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[HTML Basic](https://www.w3schools.com/html/html_basic.asp)** | Foundation of web pages | Document structure, basic tags |
| **[HTML Elements](https://www.w3schools.com/html/html_elements.asp)** | Building blocks of web pages | How HTML elements work together |
| **[HTML Attributes](https://www.w3schools.com/html/html_attributes.asp)** | Customizing element behavior | Adding properties to HTML elements |
| **[HTML Headings](https://www.w3schools.com/html/html_headings.asp)** | Document hierarchy | Organizing content with headers |
| **[HTML Paragraphs](https://www.w3schools.com/html/html_paragraphs.asp)** | Text content structure | Displaying readable text content |

### **Phase 2: Styling and Structure (Build Visual Appeal)**
Learn to make your pages look professional:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[HTML Styles](https://www.w3schools.com/html/html_styles.asp)** | Basic visual customization | Inline styling and appearance |
| **[HTML Formatting](https://www.w3schools.com/html/html_formatting.asp)** | Text presentation | Bold, italic, emphasis elements |
| **[HTML CSS](https://www.w3schools.com/html/html_css.asp)** | Professional styling | Connecting CSS to HTML |
| **[HTML Links](https://www.w3schools.com/html/html_links.asp)** | Navigation between pages | Creating interactive navigation |
| **[HTML Div](https://www.w3schools.com/html/html_div.asp)** | Layout containers | Organizing page structure |

### **Phase 3: Professional HTML (Advanced Structure)**
Master semantic and maintainable HTML:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[HTML Classes](https://www.w3schools.com/html/html_classes.asp)** | Reusable styling | CSS class organization |
| **[HTML Semantics](https://www.w3schools.com/html/html5_semantic_elements.asp)** | Meaningful markup | Professional HTML structure |
| **[HTML Style Guide](https://www.w3schools.com/html/html5_syntax.asp)** | Code quality standards | Writing maintainable HTML |

### **Phase 4: PHP Web Integration (Connect Objects to Web)**
Bridge your OOP knowledge with web interfaces:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[Form Handling](https://www.w3schools.com/php/php_forms.asp)** | User input processing | Connecting forms to PHP objects |
| **[Form Validation](https://www.w3schools.com/php/php_form_validation.asp)** | Data quality and security | Ensuring valid data reaches your objects |
| **[Form URL/E-mail](https://www.w3schools.com/php/php_form_url_email.asp)** | Specialized validation | Handling different data types |
| **[Form Complete](https://www.w3schools.com/php/php_form_complete.asp)** | Professional form handling | Complete, secure form processing |

### **Phase 5: State Management (Advanced Web Features)**
Learn to maintain data across page requests:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[Sessions](https://www.w3schools.com/php/php_sessions.asp)** | User state persistence | Login systems, user preferences |
| **[Cookies](https://www.w3schools.com/php/php_cookies.asp)** | Client-side data storage | Remembering user settings |

### **📖 Additional Architecture Resources**

**MVC Pattern Understanding**:
- **[MVC Model](https://developer.mozilla.org/en-US/docs/Glossary/MVC)** - Essential architecture pattern for professional web applications

**Routing Implementation**:
- **[PHP Router Tutorial](https://tech.jotform.com/what-is-router-and-how-to-create-your-own-router-with-php-fad811cf2873)** - Building clean URL routing systems

### **🚨 Important Study Tips**

**For beginners coming from Module 04**:
1. **Start with HTML basics** - You need to understand web page structure before integrating PHP
2. **Practice each HTML concept** with simple examples before moving to PHP integration
3. **Connect concepts to your objects** - Think about how forms will create/modify your Module 04 classes
4. **Focus on the request-response cycle** - This is fundamental to web development

**Advanced students**:
- If you already know HTML, focus on PHP web integration (Phase 4 and 5)
- Pay special attention to form validation and session management
- Study the MVC and routing resources for architectural understanding

**Remember**: Web development adds a user interface layer to your existing OOP knowledge. Your classes from Module 04 remain unchanged—you're just giving users a better way to interact with them!

## 🎮 Examples: From Console to Web

### Library System Evolution: Web Interface

The **[librarysystem](./example/librarysystem/)** example shows how to transform your console-based OOP code into a professional web application:

**Web Application Structure**:
- **[index.php](./example/librarysystem/index.php)** - Front controller entry point with session management
- **[Book.php](./example/librarysystem/Book.php)** - Enhanced Book class with readonly properties and business logic
- **[BookController.php](./example/librarysystem/BookController.php)** - MVC controller handling web requests and session storage
- **[Router.php](./example/librarysystem/Router.php)** - Professional URL routing with parameter extraction

**HTML Templates** (in `html/` directory):
- **[form.html](./example/librarysystem/html/form.html)** - Comprehensive book creation form with all fields
- **[index.html](./example/librarysystem/html/index.html)** - Homepage displaying all books from session
- **[book.html](./example/librarysystem/html/book.html)** - Individual book details page

**Key Web Concepts Demonstrated**:
- **MVC Architecture**: Clear separation between data (Model), presentation (View), and logic (Controller)
- **Single Responsibility** (from Module 4): Each class has one clear purpose in web context
- **Form Processing**: Converting user input into object creation/modification
- **Session Management**: Maintaining user state across page requests
- **Professional Routing**: Clean URLs that map to specific functionality
- **Encapsulation in Web Context**: Your private properties and validation logic from Module 4 protect against malicious web input
- **Template System**: Reusable HTML components

### Professional Testing in Web Context

The library system example demonstrates how your Module 04 testing knowledge applies to web applications:

**Testing Approach**:
- **Unit Tests**: Your Book and BookController classes (same as Module 04)
- **Integration Tests**: Testing form processing and object creation together
- **Web-Specific Tests**: Validating form handling, session management, and routing

**What This Shows**:
- Your OOP testing skills transfer directly to web development
- Business logic testing remains unchanged
- Additional web-layer testing for user interfaces

## 🔧 Getting Started

**Building on Your Module 4 Foundation**:

1. **Review your OOP knowledge** - Classes, objects, encapsulation, and file organization from Module 4 are essential
2. **Study the learning resources** in the order listed above (HTML foundation first!)
3. **Compare console vs web** - Examine how the same Book class works in both environments
4. **Examine the library system example** - See how Module 4 concepts translate to web applications
5. **Notice what stays the same** - Your business logic and object design principles
6. **Learn what's new** - HTML, forms, sessions, and HTTP request/response cycle
7. **Practice with simple forms** before tackling complex applications
8. **Apply concepts in the assignment** below

**Remember**: You're not learning a completely new programming paradigm. You're learning how to present your well-designed Module 4 classes through web interfaces!

## 📝 Assignment: Library System Part 3

Ready to transform your OOP library system into a web application? Use the concepts you've learned to create user-friendly interfaces:

**[📋 Library System Assignment Part 3](./librarysystem.md)**

This assignment will challenge you to bridge your object-oriented code with professional web interfaces while learning essential web development patterns.
