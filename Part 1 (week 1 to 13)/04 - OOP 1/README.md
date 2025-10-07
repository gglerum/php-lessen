# Object-Oriented Programming: Organizing Code the Professional Way

Excellent progress mastering functions and arrays! Now you'll discover Object-Oriented Programming (OOP)—the industry standard approach that transforms scattered functions into organized, professional applications. This isn't just a new syntax; it's a completely different way of thinking about code.

## 🎯 What You'll Learn

By the end of this module, you'll understand:

- **What OOP is and why professionals use it** - Transform chaotic function-based code into organized classes
- **Classes and Objects** - The building blocks that group related data and functionality together  
- **Basic encapsulation** - Protecting data using public and private
- **Simple constructors** - Setting up objects when they're created
- **Multi-file organization** - How to split your code across multiple files
- **Basic testing concepts** - Introduction to verifying your code works correctly

**Advanced topics** (don't worry about these yet!):
- Dependency injection and professional patterns
- Automated testing with PHPUnit
- Complex application architecture

Don't worry if OOP feels abstract at first—we'll start simple and build up gradually!

## 🌉 Bridge: From Functions to Objects

Coming from Module 03 (Functions & Arrays), you've mastered procedural programming where you organize code into functions. Object-Oriented Programming is the next evolutionary step—instead of just organizing functions, you organize **related data and functions together** into cohesive units called **classes**.

### 🧠 The Mental Shift: From "Doing Things" to "Things That Do"

**In Module 03, you thought procedurally:**
```php
// PROCEDURAL APPROACH (Module 03 style)
$book = ["title" => "Harry Potter", "author" => "J.K. Rowling", "pages" => 320];
$books = [$book]; // Array of arrays

function addBook($books, $newBook) {
    $books[] = $newBook;
    return $books;
}

function getBookInfo($book) {
    return $book["title"] . " by " . $book["author"];
}

// Usage: passing data TO functions
$books = addBook($books, $newBook);
echo getBookInfo($book);
```

**In Module 04, you'll think object-oriented:**
```php
// OBJECT-ORIENTED APPROACH (Module 04 style)
class Book {
    private string $title;
    private string $author;
    
    public function __construct(string $title, string $author) {
        $this->title = $title;
        $this->author = $author;
    }
    
    public function getInfo(): string {
        return $this->title . " by " . $this->author;
    }
}

// Usage: asking objects to DO things
$book = new Book("Harry Potter", "J.K. Rowling");
echo $book->getInfo();
```

### 🔄 Key Conceptual Shifts

| **Module 03 Concept** | **Module 04 Evolution** | **Why This Helps** |
|----------------------|-------------------------|-------------------|
| **Function** | **Method** (function inside a class) | Groups related functions together |
| **Global variables** | **Object properties** | Data belongs to specific objects |
| **Associative arrays** | **Objects with methods** | Data + behavior in one place |
| **require_once files** | **Classes in separate files** | Better organization and reusability |
| **Manual array management** | **Object repositories** | Smarter data management |

### 📝 Essential OOP Vocabulary

Before diving into the examples, learn these core terms:

**🏗️ Class**: A blueprint/template for creating objects
- Like a cookie cutter—defines the shape, but isn't the cookie itself
- Contains properties (data) and methods (functions)

**🎯 Object**: A specific instance created from a class
- Like an actual cookie made from the cookie cutter
- Each object has its own data but shares the same structure

**🔧 Constructor**: Special method that runs when you create an object
- Sets up the object with initial data
- Like filling out a form when you create something new

**🔒 Encapsulation**: Controlling access to object data
- `private`: Only the object itself can access
- `public`: Anyone can access
- Protects your data from accidental changes

**📂 Namespace**: Organizing classes to avoid name conflicts
- Like folder structures for your code
- Prevents confusion when multiple classes have similar names

### 🎯 Your First Class (Start Here!)

Let's transform a Module 03 concept into a simple class. **Don't worry about understanding everything at once** - just follow along step by step:

```php
<?php
// STEP 1: Basic class syntax (start simple!)
class SimpleBook {
    // Properties (like variables, but belong to the object)
    public $title;
    public $author;
    
    // Constructor (runs when you create a new object)
    public function __construct($title, $author) {
        $this->title = $title;    // $this refers to "this specific object"
        $this->author = $author;
    }
    
    // Method (like a function, but belongs to the object)
    public function getInfo() {
        return $this->title . " by " . $this->author;
    }
}

// STEP 2: Creating and using objects
$book1 = new SimpleBook("1984", "George Orwell");
$book2 = new SimpleBook("Dune", "Frank Herbert");

// STEP 3: Using object methods
echo $book1->getInfo(); // "1984 by George Orwell"
echo $book2->getInfo(); // "Dune by Frank Herbert"

// Each object has its own data!
echo $book1->title; // "1984"
echo $book2->title; // "Dune"
?>
```

**What's new here?**
- `class` keyword - creates a template
- `public` - means anyone can access it (we'll learn about `private` later)
- `$this` - refers to the current object
- `new` keyword - creates a new object from the class
- `->` - used to access object properties and methods

**Don't worry about**: Type hints, complex validation, or advanced features yet. Master the basics first!

### 🔒 Understanding Encapsulation: Public vs Private

Once you're comfortable with the basic class above, let's learn about protecting your data. **This is an intermediate concept** - make sure you understand the first example before moving on:

```php
<?php
class BetterBook {
    // PRIVATE: Only this class can access these properties
    private $title;
    private $author;
    private $pages;
    
    public function __construct($title, $author, $pages) {
        $this->title = $title;
        $this->author = $author;
        $this->setPages($pages); // Use our validation method
    }
    
    // PUBLIC: Anyone can call these methods
    public function getInfo() {
        return $this->title . " by " . $this->author . " (" . $this->pages . " pages)";
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setPages($pages) {
        // Basic validation: protect against invalid data
        if ($pages <= 0) {
            echo "Error: Pages must be a positive number!\n";
            return; // Don't change the value if it's invalid
        }
        $this->pages = $pages;
    }
}

// Usage
$book = new BetterBook("1984", "George Orwell", 328);

// ✅ This works - using public methods
echo $book->getInfo();
echo $book->getTitle();

// ❌ This would cause an error - private properties can't be accessed directly
// echo $book->title; // Fatal error: Cannot access private property

// ✅ This works - controlled access with validation
$book->setPages(300);

// ✅ This shows our validation in action
$book->setPages(-50); // Will show error message and not change the value
?>
```

**What's different here?**
- `private` - only the class itself can access these properties
- **Getter methods** - safe ways to read private data (`getTitle()`)
- **Setter methods** - safe ways to change private data (`setPages()`)
- **Simple validation** - checking if data makes sense before storing it

**Why encapsulation matters:**
- **Data Protection**: Prevents accidental corruption of object data
- **Validation**: You can check data before storing it
- **Control**: You decide exactly how your object can be used

**Note**: We're using simple error messages here. Later in the course, you'll learn about professional error handling with exceptions.

### 🚀 Why This is Powerful (The "Aha!" Moment)

**Module 03 problem:**
```php
// Messy: data and functions are separate
$book1 = ["title" => "1984", "author" => "George Orwell"];
$book2 = ["title" => "Dune", "author" => "Frank Herbert"];

// Easy to make mistakes:
echo getBookInfo($book2); // Which book? Easy to mix up!
echo getBookInfo($book1);
```

**Module 04 solution:**
```php
// Clean: each object knows its own data and what it can do
$book1 = new SimpleBook("1984", "George Orwell");
$book2 = new SimpleBook("Dune", "Frank Herbert");

// Clear and safe:
echo $book1->getInfo(); // Book1 handles its own data
echo $book2->getInfo(); // Book2 handles its own data
```

### 📁 File Organization Evolution

**Module 03 approach:**
```
project/
├── index.php (500+ lines, everything mixed together)
```

**Module 04 approach:**
```
project/
├── index.php (entry point, ~20 lines)
├── Book.php (Book class)
├── Author.php (Author class)
├── BookRepository.php (manages collections)
└── composer.json (dependencies)
```

**How to connect files with `require_once`:**
```php
<?php
// index.php - Entry point
require_once 'Book.php';        // Load the Book class
require_once 'Author.php';      // Load the Author class
require_once 'BookRepository.php'; // Load the repository

// Now you can use all the classes
$book = new Book("1984", "George Orwell");
$repository = new BookRepository();
$repository->add($book);
?>
```

**Why this matters:**
- Each file has one clear purpose
- Code is easier to find and modify
- Multiple developers can work on different parts
- Code can be reused in other projects
- `require_once` prevents loading the same file twice

### 🎯 Ready to Dive Deeper?

**Take your time!** Make sure you understand the basic concepts above before moving forward. If the simple examples make sense, you're ready for the more complex examples below.

**Learning path recap:**
1. ✅ **Start here**: Understand the mental shift from functions to objects
2. ✅ **Master this**: Create and use simple classes (`SimpleBook`)
3. ✅ **Then learn**: Basic encapsulation with public/private
4. ✅ **Finally**: Look at the professional examples below

The rest of this module will show you:
- How to build complete applications using these concepts
- Professional patterns and advanced techniques
- Testing your objects to ensure they work correctly
- Real-world examples that demonstrate the power of OOP

**Remember**: Every expert was once a beginner. The complex examples below might look intimidating, but they're built using the same basic concepts you just learned. Don't worry if you don't understand everything immediately - focus on the fundamentals first!

**Key takeaway**: OOP isn't just new syntax—it's a powerful way to organize code that makes large applications manageable, maintainable, and professional.

### 🛠️ Professional Tools: Introduction to Composer

You might have noticed `composer.json` in the file structure above. **Composer** is a helpful tool that makes managing multiple files easier. **Don't worry if this seems complex** - you'll see it in action in the examples, and it will make more sense as you progress.

**What Composer does (in simple terms):**
- **Autoloading**: Automatically loads classes when you need them (no more `require_once` everywhere!)
- **Manages Libraries**: Downloads and manages code libraries that other people wrote
- **Professional Standard**: Used in real-world PHP projects

**Simple comparison - what you know vs what's coming:**

**Current approach (what you learned in Module 03):**
```php
<?php
require_once 'Book.php';
require_once 'Author.php';
require_once 'BookRepository.php';
// ... you have to remember to include every file

$book = new Book("1984", "George Orwell");
?>
```

**Professional approach (with Composer):**
```php
<?php
require_once 'vendor/autoload.php'; // Just ONE require statement!

// Composer automatically finds and loads any class you use
$book = new Book("1984", "George Orwell");
$movie = new Movie("Inception", "Christopher Nolan");
// No require_once needed - Composer handles it!
?>
```

**For now, just know:**
- Composer makes file management easier in larger projects
- You'll see it in the examples, but don't worry about mastering it yet
- It's like having an assistant that handles the boring file inclusion work

**When you're ready to learn more:**
- The **moviemanagement** example uses Composer
- You'll run commands like `composer install` to set up projects
- This becomes more important as your projects grow bigger

## 📚 Learning Resources

**Your Main Textbook**: https://www.w3schools.com/php/default.asp

**🎯 Study Plan for Beginners**: Start with the basics and work your way up. Each section builds on the previous ones:

### **Phase 1: Foundation (Start Here!)**
Study these first to understand the basic concepts:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[Include Files](https://www.w3schools.com/php/php_includes.asp)** | Organizing code across multiple files | How to use `require_once` and `include` to split your code |
| **[Classes & Objects](https://www.w3schools.com/php/php_oop_classes_objects.asp)** | The foundation of OOP | Basic class syntax, creating objects, `$this` keyword |

### **Phase 2: Object Behavior (Build On Basics)**
Once you understand classes and objects, learn how to control them:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[Constructor](https://www.w3schools.com/php/php_oop_constructor.asp)** | Setting up objects when they're created | How to initialize objects with data |
| **[Access Modifiers](https://www.w3schools.com/php/php_oop_access_modifiers.asp)** | Controlling who can access what (public/private) | Basic encapsulation and data protection |

### **Phase 3: Advanced Concepts (After Mastering Basics)**
These are more advanced - don't rush to these until you understand the foundation:

| W3Schools Section | Why It's Important | What You'll Learn |
|-------------------|-------------------|------------------|
| **[Static Methods](https://www.w3schools.com/php/php_oop_static_methods.asp)** | Class-level data and methods | Sharing data across all instances |
| **[Namespaces](https://www.w3schools.com/php/php_namespaces.asp)** | Organizing classes and avoiding name conflicts | Professional code organization |

### **📖 Additional Topics Not Covered by W3Schools**

**For encapsulation details**: The W3Schools "Access Modifiers" section covers public/private basics, but for deeper understanding of encapsulation principles, you can also read:
- **[Encapsulation](https://www.geeksforgeeks.org/php-encapsulation/)** - Comprehensive explanation of hiding implementation details

**Topics covered in our examples but not requiring separate study**:
- **`require_once` vs `include`**: Covered in the Include Files section above
- **Object instantiation with `new`**: Covered in Classes & Objects section above  
- **Method chaining and object interactions**: You'll see these in our practical examples

### **🚨 Important Study Tips**

**For beginners coming from Module 03**:
1. **Don't skip Phase 1** - These concepts are essential for everything else
2. **Practice each concept** before moving to the next phase
3. **Focus on understanding over memorization** - The examples in this module will help reinforce the concepts
4. **It's okay to revisit** - OOP concepts take time to fully understand

**Advanced students**:
- If you already understand basic OOP, you can move through the phases more quickly
- Focus on the Phase 3 topics and the professional examples in this module

**Remember**: The goal is understanding, not speed. Take time with each concept until it makes sense!

## 🎮 Examples: From Chaos to Organization

### Hangman v3: OOP Transformation

Remember the hangman game from previous modules? We've completely transformed it using Object-Oriented Programming. **Compare [hangman_v2.php](../03%20-%20Functions%20&%20Arrays/example/hangman_v2.php) with this new version** to see how OOP transforms chaotic function-based code into clean, organized classes.

**The OOP Architecture**:

- **[console.php](./example/hangman_v3/console.php)** - Now ONLY handles user input/output and delegates everything else to specialized classes
- **[GameService](./example/hangman_v3/game/GameService.php)** - The "coordinator" that manages game flow and orchestrates other classes
- **[Word](./example/hangman_v3/game/Word.php)** - Handles all word-related functionality (checking letters, displaying progress)
- **[DrawnHangman](./example/hangman_v3/game/DrawnHangman.php)** - Responsible for drawing the hangman figure
- **[Game](./example/hangman_v3/game/Game.php)** - Stores game state (attempts left, current status)

**The Magic: Single Responsibility Principle**  
Each class has ONE clear job. This makes code easier to understand, test, and modify.

#### Understanding Dependency Injection

The code demonstrates **dependency injection**—a professional technique where objects are created outside a class and passed in through the constructor. For example:

- [RandomWordGenerator](./example/hangman_v3/game/generator/RandomWordGenerator.php) receives a [Words](./example/hangman_v3/game/generator/Words.php) object through its constructor
- We create these objects in `console.php` and pass them where needed

**Why not create objects inside classes?** Because dependency injection makes code flexible. Want to get words from a database instead of an array? Just create a `DatabaseWordProvider` class and inject that instead—no need to change `RandomWordGenerator`!

This flexibility becomes powerful with **polymorphism** (covered in OOP 3).

### Movie Management System: Professional Application Structure

The **[moviemanagement](./example/moviemanagement/)** example shows how a real application is organized using OOP principles:

**Application Structure**:
- **[Movie.php](./example/moviemanagement/Movie.php)** - Represents a movie entity with properties (title, director, rating)
- **[MovieRepository.php](./example/moviemanagement/MovieRepository.php)** - Manages the collection of movies (add, remove, find)
- **[MovieController.php](./example/moviemanagement/MovieController.php)** - Handles movie-related operations and business logic
- **[Main.php](./example/moviemanagement/Main.php)** - Manages the user interface and application flow
- **[index.php](./example/moviemanagement/index.php)** - Entry point that starts the application

**Key OOP Concepts Demonstrated**:
- **Encapsulation**: Movie properties are private, accessed through public methods
- **Single Responsibility**: Each class has one clear purpose
- **Composer Autoloading**: Professional dependency management
- **Namespace Organization**: Keeping code organized and avoiding naming conflicts

## 🧪 Professional Testing: Seeing It in Action

The **moviemanagement** project doesn't just teach OOP concepts—it demonstrates professional testing practices that every developer needs to master.

### Real Working Tests You Can Run

Navigate to the `tests/` folder in moviemanagement to see actual PHPUnit tests in action:

**Unit Tests** (Test individual classes):
- **[MovieTest.php](./example/moviemanagement/tests/Unit/MovieTest.php)** - Learn testing fundamentals with the Movie class

**Feature Tests** (Test complete workflows):
- **[MovieRepositoryTest.php](./example/moviemanagement/tests/Feature/MovieRepositoryTest.php)** - Tests adding and retrieving movies from the repository
- **[MovieControllerTest.php](./example/moviemanagement/tests/Feature/MovieControllerTest.php)** - Tests the complete controller workflow for storing movies
- **[MovieRepositoryEnhancedTest.php](./example/moviemanagement/tests/Feature/MovieRepositoryEnhancedTest.php)** - Advanced repository testing with edge cases and professional patterns
- **[BeginnerIntegrationTest.php](./example/moviemanagement/tests/Feature/BeginnerIntegrationTest.php)** - Learn how multiple classes work together in realistic user scenarios

**Educational Testing Progression**:
1. **Start with Unit Tests** (`MovieTest.php`) - Learn to test individual methods and basic concepts
2. **Progress to Feature Tests** (`MovieRepositoryTest.php`, `MovieControllerTest.php`) - See how to test complete features
3. **Advance to Enhanced Testing** (`MovieRepositoryEnhancedTest.php`) - Professional patterns and edge cases
4. **Master Integration Testing** (`BeginnerIntegrationTest.php`) - Test multiple objects working together

**What These Tests Demonstrate**:
- **Automated Verification**: Code that automatically checks if your classes work correctly
- **Object Testing**: How to test classes, methods, and object interactions
- **Professional Setup**: Real PHPUnit configuration using Composer
- **Test Structure**: Arrange-Act-Assert pattern in action
- **Testing Hierarchy**: Unit → Feature → Integration testing progression
- **Educational Documentation**: Every test includes extensive comments explaining testing concepts

### Why This Matters for You

Instead of manually testing with `echo` statements every time you make a change, these automated tests:
- **Run instantly** to verify everything still works
- **Catch bugs** before they reach users  
- **Document expected behavior** - tests show how code should work
- **Enable confident refactoring** - change code knowing tests will catch issues

### Running the Tests Yourself

```bash
# Navigate to the moviemanagement directory
cd "Part 1 (week 1 to 13)/04 - OOP 1/example/moviemanagement"

# Install dependencies (including PHPUnit)
composer install

# Run all tests
vendor/bin/phpunit tests/
```

**This is exactly how professional developers work** - write code, write tests, run tests, repeat. You're seeing industry-standard practices in action!

## 🔧 Getting Started

1. **Study the learning resources** in the order listed above
2. **Examine the code examples** - Compare hangman_v2 to hangman_v3 to see the transformation
3. **Run the moviemanagement system** to see a complete OOP application
4. **Practice with the assignment** below

## 📝 Assignment: Library System Part 2

Ready to apply OOP principles? Continue building your library system using the concepts you've learned:

**[📋 Library System Assignment Part 2](./librarysystem.md)**

This assignment will challenge you to transform function-based code into clean, organized classes while learning professional development practices.
