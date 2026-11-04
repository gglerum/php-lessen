# SOLID Principles - Junior Developer Companion

This guide prepares you to understand the SOLID principles README by building from simple concepts to the Laravel examples you'll encounter.

## Before You Start

**Prerequisites**: 
- Basic OOP (classes, inheritance, interfaces)
- Understanding of `advanced-oop.md` concepts
- Basic Laravel knowledge from `laravel-basics.md`

**What you'll gain**: The ability to recognize and apply SOLID principles in your own code, preparing you for professional-level architecture.

## Quick Reference: What is SOLID?

SOLID is an acronym for five principles that make your code more maintainable:

- **S**ingle Responsibility Principle - One class, one job
- **O**pen/Closed Principle - Open for extension, closed for modification  
- **L**iskov Substitution Principle - Subclasses should be replaceable
- **I**nterface Segregation Principle - Many small interfaces > one large interface
- **D**ependency Inversion Principle - Depend on abstractions, not concrete classes

## Building SOLID Understanding Step-by-Step

### 1. Single Responsibility Principle (SRP)
*"A class should have only one reason to change"*

#### Start Simple
```php
// BAD: Class has multiple responsibilities
class User 
{
    public string $name;
    public string $email;
    
    // Responsibility 1: User data management
    public function getName(): string 
    {
        return $this->name;
    }
    
    // Responsibility 2: Email sending (NOT user's job!)
    public function sendWelcomeEmail(): void 
    {
        mail($this->email, 'Welcome!', 'Welcome to our site!');
    }
    
    // Responsibility 3: Database operations (NOT user's job!)
    public function save(): void 
    {
        $db = new PDO(/* connection details */);
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$this->name, $this->email]);
    }
}
```

**What's wrong**: If we change how emails are sent, we have to modify the User class. If database structure changes, we modify the User class. User class has three reasons to change.

```php
// GOOD: Each class has one responsibility
class User 
{
    public string $name;
    public string $email;
    
    // ONLY responsibility: Managing user data
    public function getName(): string 
    {
        return $this->name;
    }
    
    public function getEmail(): string 
    {
        return $this->email;
    }
}

class EmailService 
{
    // ONLY responsibility: Sending emails
    public function sendWelcomeEmail(User $user): void 
    {
        mail($user->getEmail(), 'Welcome!', 'Welcome to our site!');
    }
}

class UserRepository 
{
    // ONLY responsibility: User database operations
    public function save(User $user): void 
    {
        $db = new PDO(/* connection details */);
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$user->getName(), $user->getEmail()]);
    }
}
```

**The Test**: Ask yourself "What would make me change this class?" If you have more than one answer, split the class.

#### Laravel Example (Building to Main README)
Now you can understand why the main README shows:

```php
// BAD: Controller doing everything
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Authorization (responsibility 1)
        $user = auth()->user();
        Gate::authorize('update', [$application, $user->organisation->id]);
        
        // Validation (responsibility 2)
        $request->validate(['files' => 'required|array']);
        
        // Business logic (responsibility 3)
        $totalUploadedSize = /* calculation */;
        
        // Database operations (responsibility 4)
        foreach ($request->file('files') as $file) {
            // Save file logic
        }
    }
}
```

Versus the "better" example with separate classes for each responsibility.

### 2. Dependency Inversion Principle (DIP)
*"Depend on abstractions, not concretions"*

This is crucial for understanding Laravel's advanced examples.

#### Start Simple
```php
// BAD: Depending on concrete implementation
class OrderProcessor 
{
    public function processOrder(Order $order): void 
    {
        // Directly depending on concrete MySQL class
        $database = new MySQLDatabase();  // What if we switch to PostgreSQL?
        $database->save($order);
        
        // Directly depending on concrete email class  
        $emailer = new SMTPEmailer();     // What if we switch to SendGrid?
        $emailer->send($order->getCustomerEmail(), 'Order confirmed');
    }
}
```

**Problem**: If we change database or email service, we have to modify OrderProcessor.

```php
// GOOD: Depending on abstractions (interfaces)
interface DatabaseInterface 
{
    public function save(Order $order): void;
}

interface EmailerInterface 
{
    public function send(string $to, string $subject, string $body): void;
}

class OrderProcessor 
{
    public function __construct(
        private DatabaseInterface $database,
        private EmailerInterface $emailer
    ) {}
    
    public function processOrder(Order $order): void 
    {
        $this->database->save($order);
        $this->emailer->send($order->getCustomerEmail(), 'Order confirmed', 'Your order is ready!');
    }
}

// Concrete implementations
class MySQLDatabase implements DatabaseInterface 
{
    public function save(Order $order): void 
    {
        // MySQL specific code
    }
}

class PostgreSQLDatabase implements DatabaseInterface 
{
    public function save(Order $order): void 
    {
        // PostgreSQL specific code
    }
}

class SMTPEmailer implements EmailerInterface 
{
    public function send(string $to, string $subject, string $body): void 
    {
        // SMTP specific code
    }
}

// Usage
$database = new MySQLDatabase();          // Easy to swap to PostgreSQLDatabase
$emailer = new SMTPEmailer();             // Easy to swap to SendGridEmailer
$processor = new OrderProcessor($database, $emailer);
```

**The Test**: Can you easily swap implementations without changing the class that uses them?

#### Laravel Connection
This is exactly what Laravel's Service Container does automatically! When you see:

```php
public function store(Request $request, UserService $userService, EmailService $emailService)
```

Laravel is injecting concrete implementations of these services, but your controller depends on their interfaces/contracts.

### 3. Open/Closed Principle (OCP)
*"Open for extension, closed for modification"*

#### Start Simple
```php
// BAD: Adding new shapes requires modifying AreaCalculator
class AreaCalculator 
{
    public function calculate(array $shapes): float 
    {
        $area = 0;
        foreach ($shapes as $shape) {
            if ($shape instanceof Rectangle) {
                $area += $shape->width * $shape->height;
            } elseif ($shape instanceof Circle) {
                $area += pi() * $shape->radius * $shape->radius;
            } 
            // What if we add Triangle? We have to modify this method!
        }
        return $area;
    }
}
```

```php
// GOOD: Adding new shapes doesn't require modifying AreaCalculator
interface ShapeInterface 
{
    public function calculateArea(): float;
}

class Rectangle implements ShapeInterface 
{
    public function __construct(
        private float $width,
        private float $height
    ) {}
    
    public function calculateArea(): float 
    {
        return $this->width * $this->height;
    }
}

class Circle implements ShapeInterface 
{
    public function __construct(private float $radius) {}
    
    public function calculateArea(): float 
    {
        return pi() * $this->radius * $this->radius;
    }
}

class Triangle implements ShapeInterface  // NEW: Added without changing AreaCalculator
{
    public function __construct(
        private float $base,
        private float $height
    ) {}
    
    public function calculateArea(): float 
    {
        return 0.5 * $this->base * $this->height;
    }
}

class AreaCalculator 
{
    public function calculate(array $shapes): float 
    {
        $area = 0;
        foreach ($shapes as $shape) {
            $area += $shape->calculateArea();  // Works with any ShapeInterface
        }
        return $area;
    }
}
```

**The Test**: Can you add new functionality without changing existing code?

### 4. Interface Segregation Principle (ISP)
*"Clients shouldn't be forced to depend on methods they don't use"*

#### Start Simple
```php
// BAD: Fat interface forces classes to implement methods they don't need
interface WorkerInterface 
{
    public function work(): void;
    public function eat(): void;
    public function sleep(): void;
}

class HumanWorker implements WorkerInterface 
{
    public function work(): void { /* humans work */ }
    public function eat(): void { /* humans eat */ }
    public function sleep(): void { /* humans sleep */ }
}

class RobotWorker implements WorkerInterface 
{
    public function work(): void { /* robots work */ }
    public function eat(): void { /* robots don't eat! */ }     // Forced to implement
    public function sleep(): void { /* robots don't sleep! */ } // Forced to implement
}
```

```php
// GOOD: Small, focused interfaces
interface WorkableInterface 
{
    public function work(): void;
}

interface EatableInterface 
{
    public function eat(): void;
}

interface SleepableInterface 
{
    public function sleep(): void;
}

class HumanWorker implements WorkableInterface, EatableInterface, SleepableInterface 
{
    public function work(): void { /* humans work */ }
    public function eat(): void { /* humans eat */ }
    public function sleep(): void { /* humans sleep */ }
}

class RobotWorker implements WorkableInterface  // Only implements what it needs
{
    public function work(): void { /* robots work */ }
}
```

**The Test**: Are there methods in your interface that some implementing classes will never use?

### 5. Liskov Substitution Principle (LSP)
*"Subclasses should be substitutable for their parent classes"*

This is the trickiest principle.

#### Start Simple
```php
// BAD: Square violates LSP
class Rectangle 
{
    protected int $width;
    protected int $height;
    
    public function setWidth(int $width): void 
    {
        $this->width = $width;
    }
    
    public function setHeight(int $height): void 
    {
        $this->height = $height;
    }
    
    public function getArea(): int 
    {
        return $this->width * $this->height;
    }
}

class Square extends Rectangle  // Square IS-A Rectangle, right?
{
    public function setWidth(int $width): void 
    {
        $this->width = $width;
        $this->height = $width;  // Square must have equal sides
    }
    
    public function setHeight(int $height): void 
    {
        $this->width = $height;   // Square must have equal sides
        $this->height = $height;
    }
}

// This code breaks LSP
function calculateArea(Rectangle $rectangle): int 
{
    $rectangle->setWidth(5);
    $rectangle->setHeight(4);
    return $rectangle->getArea();  // Expects 20, but Square gives 16!
}

$rectangle = new Rectangle();
echo calculateArea($rectangle);  // 20 ✓

$square = new Square();
echo calculateArea($square);     // 16 ✗ (expected 20)
```

**Problem**: You can't substitute Square for Rectangle because it changes behavior.

```php
// GOOD: Use composition instead of inheritance
interface ShapeInterface 
{
    public function getArea(): int;
}

class Rectangle implements ShapeInterface 
{
    public function __construct(
        private int $width,
        private int $height
    ) {}
    
    public function getArea(): int 
    {
        return $this->width * $this->height;
    }
}

class Square implements ShapeInterface 
{
    public function __construct(private int $side) {}
    
    public function getArea(): int 
    {
        return $this->side * $this->side;
    }
}

// Both can be substituted for ShapeInterface
function calculateArea(ShapeInterface $shape): int 
{
    return $shape->getArea();  // Works correctly for both
}
```

**The Test**: If you substitute a subclass for its parent, does the behavior remain correct?

## Putting It All Together

Here's how SOLID principles work together in a simple file upload example:

```php
// Interfaces (DIP - depend on abstractions)
interface FileValidatorInterface 
{
    public function validate(array $files): bool;
}

interface FileStorageInterface 
{
    public function store(UploadedFile $file): string;
}

interface UserRepositoryInterface 
{
    public function findById(int $id): ?User;
    public function save(User $user): void;
}

// Single Responsibility: Each class has one job
class FileValidator implements FileValidatorInterface  // ISP: Small interface
{
    public function validate(array $files): bool 
    {
        foreach ($files as $file) {
            if ($file->getSize() > 10 * 1024 * 1024) {  // 10MB limit
                return false;
            }
        }
        return true;
    }
}

class LocalFileStorage implements FileStorageInterface  // LSP: Can substitute any FileStorageInterface
{
    public function store(UploadedFile $file): string 
    {
        return $file->store('uploads');
    }
}

class CloudFileStorage implements FileStorageInterface  // LSP: Substitutable
{
    public function store(UploadedFile $file): string 
    {
        // Cloud storage implementation
        return $this->uploadToCloud($file);
    }
}

class FileUploadService  // SRP: Only handles file upload coordination
{
    public function __construct(
        private FileValidatorInterface $validator,      // DIP: Depend on abstraction
        private FileStorageInterface $storage,          // DIP: Depend on abstraction  
        private UserRepositoryInterface $userRepository // DIP: Depend on abstraction
    ) {}
    
    public function uploadFiles(int $userId, array $files): array 
    {
        // Validate files
        if (!$this->validator->validate($files)) {
            throw new InvalidFileException();
        }
        
        // Store files
        $storedPaths = [];
        foreach ($files as $file) {
            $storedPaths[] = $this->storage->store($file);
        }
        
        return $storedPaths;
    }
}

// OCP: Adding new file types doesn't require changing FileUploadService
class ImageFileStorage implements FileStorageInterface  // New implementation
{
    public function store(UploadedFile $file): string 
    {
        // Image-specific storage (resize, optimize, etc.)
        return $this->storeOptimizedImage($file);
    }
}
```

## Common SOLID Mistakes to Avoid

### 1. Over-Engineering
Don't create interfaces for every single class. Start simple, add interfaces when you actually need flexibility.

### 2. Premature Abstraction  
Don't abstract until you have a concrete need for multiple implementations.

### 3. Interface Explosion
Don't create dozens of tiny interfaces. Group related methods logically.

### 4. Violating LSP with Inheritance
Prefer composition over inheritance to avoid LSP violations.

### 5. Ignoring SRP in Services
Keep your service classes focused. If a service is doing user management AND file processing, split it.

## Practice Exercise

Before reading the main SOLID README, try this:

**Task**: Refactor this class to follow SOLID principles:

```php
class UserManager 
{
    public function createUser(string $email, string $password): void 
    {
        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }
        
        if (strlen($password) < 8) {
            throw new Exception('Password too short');
        }
        
        // Save to database
        $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
        $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        
        // Send welcome email
        mail($email, 'Welcome!', 'Thanks for joining!');
        
        // Log the action
        file_put_contents('app.log', "User created: $email\n", FILE_APPEND);
    }
}
```

**Challenge**: Identify SRP violations and create separate classes following SOLID principles.

**Solution**: Create UserValidator, UserRepository, EmailService, Logger, and UserService classes.

## Ready for the Main README?

You should now be able to understand:
- Why the "bad" examples violate SOLID principles
- How the Laravel examples demonstrate good SOLID design
- Why dependency injection makes testing easier
- How interfaces provide flexibility

**Next step**: Read `04 - SOLID/README.md` and see how these principles apply to the Laravel file upload refactoring!
