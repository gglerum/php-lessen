# Design Principles & Refactoring - Junior Developer Companion

This guide prepares you to understand the advanced refactoring examples in `01 - Design Principles & Refactoring` by building from simple concepts to Laravel-specific patterns.

## Before You Start

**Prerequisites**: 
- Basic OOP understanding
- Laravel basics from `laravel-basics.md`
- Advanced OOP concepts from `advanced-oop.md`

**What you'll gain**: The ability to systematically improve messy code using proven refactoring techniques, preparing you for professional Laravel development.

## Core Concept: What is Refactoring?

**Refactoring** = Improving code structure without changing what it does externally.

Think of it like renovating a house:
- The house still serves the same purpose (shelter)
- But the internal structure becomes better organized, more maintainable, and easier to extend
- You do it in small, safe steps while the house remains livable

## The Refactoring Mindset

### Code Smells (Warning Signs)
Before you can refactor, you need to recognize when code needs improvement:

```php
// SMELL: God Object (class doing too much)
class UserController 
{
    public function register(Request $request) 
    {
        // Validation logic
        if (!$request->has('email') || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email';
        }
        
        // Password hashing
        $hashedPassword = password_hash($request->password, PASSWORD_DEFAULT);
        
        // Database operations  
        $user = new User();
        $user->email = $request->email;
        $user->password = $hashedPassword;
        $user->save();
        
        // Email sending
        mail($request->email, 'Welcome!', 'Thank you for registering!');
        
        // File operations
        if ($request->has('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars');
            $user->avatar = $avatarPath;
            $user->save();
        }
        
        return 'Registration successful';
    }
}
```

**Problems with this code**:
1. **Single Responsibility Violation**: Controller handles validation, database, email, file storage
2. **Hard to Test**: Everything is tangled together
3. **Hard to Change**: Want to change email provider? Have to modify controller
4. **Duplicate Logic**: This validation/email logic will be repeated elsewhere

### Refactoring Strategy

**Step 1: Extract Service Classes**
```php
// Create focused service classes
class UserRegistrationService 
{
    public function __construct(
        private EmailService $emailService,
        private FileUploadService $fileUploadService
    ) {}
    
    public function register(array $userData, ?UploadedFile $avatar = null): User 
    {
        $user = User::create([
            'email' => $userData['email'],
            'password' => bcrypt($userData['password'])
        ]);
        
        $this->emailService->sendWelcomeEmail($user);
        
        if ($avatar) {
            $avatarPath = $this->fileUploadService->uploadAvatar($avatar);
            $user->update(['avatar' => $avatarPath]);
        }
        
        return $user;
    }
}

class EmailService 
{
    public function sendWelcomeEmail(User $user): void 
    {
        // Email logic isolated here
        mail($user->email, 'Welcome!', 'Thank you for registering!');
    }
}

class FileUploadService 
{
    public function uploadAvatar(UploadedFile $file): string 
    {
        // File upload logic isolated here
        return $file->store('avatars');
    }
}
```

**Step 2: Use Laravel Form Requests for Validation**
```php
class RegisterRequest extends FormRequest 
{
    public function rules(): array 
    {
        return [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'avatar' => ['nullable', 'image', 'max:2048']
        ];
    }
}
```

**Step 3: Simplified Controller**
```php
class UserController 
{
    public function __construct(private UserRegistrationService $registrationService) {}
    
    public function register(RegisterRequest $request) 
    {
        $user = $this->registrationService->register(
            $request->validated(),
            $request->file('avatar')
        );
        
        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ]);
    }
}
```

**What we achieved**:
✅ **Single Responsibility**: Each class has one clear job  
✅ **Testable**: Can test email service independently of file uploads  
✅ **Flexible**: Can swap email providers without touching controller  
✅ **Reusable**: Email service can be used elsewhere  
✅ **Laravel Best Practices**: Using Form Requests, service injection

## Key Refactoring Patterns

### 1. Extract Service Layer

**When to use**: Controller methods are getting long and complex

**Before**:
```php
class OrderController 
{
    public function create(Request $request) 
    {
        // Calculate total
        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->price * $item['quantity'];
        }
        
        // Apply discount
        $user = auth()->user();
        if ($user->is_premium) {
            $total *= 0.9; // 10% discount
        }
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending'
        ]);
        
        // Create order items
        foreach ($request->items as $item) {
            $order->items()->create($item);
        }
        
        return $order;
    }
}
```

**After**:
```php
class OrderService 
{
    public function createOrder(User $user, array $items): Order 
    {
        $total = $this->calculateTotal($items);
        $total = $this->applyDiscounts($user, $total);
        
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'status' => 'pending'
        ]);
        
        $this->createOrderItems($order, $items);
        
        return $order;
    }
    
    private function calculateTotal(array $items): float 
    {
        $total = 0;
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->price * $item['quantity'];
        }
        return $total;
    }
    
    private function applyDiscounts(User $user, float $total): float 
    {
        if ($user->is_premium) {
            return $total * 0.9;
        }
        return $total;
    }
    
    private function createOrderItems(Order $order, array $items): void 
    {
        foreach ($items as $item) {
            $order->items()->create($item);
        }
    }
}

class OrderController 
{
    public function __construct(private OrderService $orderService) {}
    
    public function create(CreateOrderRequest $request) 
    {
        $order = $this->orderService->createOrder(
            auth()->user(),
            $request->validated()['items']
        );
        
        return response()->json($order);
    }
}
```

### 2. Extract Value Objects

**When to use**: You have primitive values (strings, numbers) that represent domain concepts

**Before**:
```php
class User 
{
    public string $email;
    
    public function isValidEmail(): bool 
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

class EmailService 
{
    public function send(string $email, string $subject, string $body): void 
    {
        // Need to validate email again here
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        
        // Send logic...
    }
}
```

**After**:
```php
class EmailAddress 
{
    private string $email;
    
    public function __construct(string $email) 
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email: {$email}");
        }
        
        $this->email = $email;
    }
    
    public function toString(): string 
    {
        return $this->email;
    }
    
    public function getDomain(): string 
    {
        return explode('@', $this->email)[1];
    }
}

class User 
{
    public EmailAddress $email;
    
    public function __construct(EmailAddress $email) 
    {
        $this->email = $email;
    }
}

class EmailService 
{
    public function send(EmailAddress $email, string $subject, string $body): void 
    {
        // No validation needed - EmailAddress guarantees validity
        // Send logic using $email->toString()
    }
}
```

**Benefits**:
- **Validation in one place**: Can't create invalid EmailAddress
- **Rich behavior**: Can add domain-specific methods
- **Type safety**: Can't accidentally pass a regular string
- **Self-documenting**: Code clearly shows what type of data is expected

### 3. Dependency Injection Pattern

**When to use**: Classes create their own dependencies (hard to test/swap)

**Before**:
```php
class ReportGenerator 
{
    public function generateSalesReport(): string 
    {
        // Hard-coded dependencies
        $database = new MySQLDatabase();
        $emailer = new SMTPEmailer();
        $logger = new FileLogger('/logs/app.log');
        
        try {
            $sales = $database->query('SELECT * FROM sales');
            $report = $this->formatReport($sales);
            
            $emailer->send('admin@company.com', 'Sales Report', $report);
            $logger->info('Report generated successfully');
            
            return $report;
        } catch (Exception $e) {
            $logger->error("Report generation failed: " . $e->getMessage());
            throw $e;
        }
    }
}
```

**Problems**:
- Can't test without real database/email/file system
- Can't swap to different database or email provider
- Hard to configure (log file path is hard-coded)

**After**:
```php
interface DatabaseInterface 
{
    public function query(string $sql): array;
}

interface EmailInterface 
{
    public function send(string $to, string $subject, string $body): void;
}

interface LoggerInterface 
{
    public function info(string $message): void;
    public function error(string $message): void;
}

class ReportGenerator 
{
    public function __construct(
        private DatabaseInterface $database,
        private EmailInterface $emailer,
        private LoggerInterface $logger
    ) {}
    
    public function generateSalesReport(): string 
    {
        try {
            $sales = $this->database->query('SELECT * FROM sales');
            $report = $this->formatReport($sales);
            
            $this->emailer->send('admin@company.com', 'Sales Report', $report);
            $this->logger->info('Report generated successfully');
            
            return $report;
        } catch (Exception $e) {
            $this->logger->error("Report generation failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function formatReport(array $sales): string 
    {
        // Report formatting logic
        return "Sales Report: " . count($sales) . " records";
    }
}

// Easy testing with mocks
class ReportGeneratorTest extends TestCase 
{
    public function test_generates_report_successfully() 
    {
        $mockDatabase = $this->createMock(DatabaseInterface::class);
        $mockEmailer = $this->createMock(EmailInterface::class);
        $mockLogger = $this->createMock(LoggerInterface::class);
        
        $mockDatabase->expects($this->once())
                    ->method('query')
                    ->willReturn([['sale' => 'data']]);
        
        $generator = new ReportGenerator($mockDatabase, $mockEmailer, $mockLogger);
        $result = $generator->generateSalesReport();
        
        $this->assertStringContains('Sales Report: 1 records', $result);
    }
}
```

## Laravel-Specific Refactoring Patterns

### 1. Form Request Validation

**Before**: Controller handles validation
```php
class UserController 
{
    public function update(Request $request, User $user) 
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'age' => 'nullable|integer|min:18'
        ]);
        
        $user->update($request->all());
        return $user;
    }
}
```

**After**: Dedicated Form Request
```php
class UpdateUserRequest extends FormRequest 
{
    public function authorize(): bool 
    {
        return $this->user()->can('update', $this->route('user'));
    }
    
    public function rules(): array 
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->route('user'))],
            'age' => ['nullable', 'integer', 'min:18']
        ];
    }
    
    public function messages(): array 
    {
        return [
            'age.min' => 'You must be at least 18 years old to use this service.'
        ];
    }
}

class UserController 
{
    public function update(UpdateUserRequest $request, User $user) 
    {
        $user->update($request->validated());
        return $user;
    }
}
```

### 2. Repository Pattern for Data Access

**Before**: Controller directly uses Eloquent
```php
class PostController 
{
    public function index() 
    {
        $posts = Post::with('author')
                    ->where('published', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        return view('posts.index', compact('posts'));
    }
    
    public function featured() 
    {
        $posts = Post::with('author')
                    ->where('published', true)
                    ->where('featured', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
        
        return view('posts.featured', compact('posts'));
    }
}
```

**After**: Repository handles data access
```php
interface PostRepositoryInterface 
{
    public function getPublished(int $perPage = 10): LengthAwarePaginator;
    public function getFeatured(int $limit = 5): Collection;
    public function findPublished(int $id): ?Post;
}

class PostRepository implements PostRepositoryInterface 
{
    public function getPublished(int $perPage = 10): LengthAwarePaginator 
    {
        return Post::with('author')
                  ->where('published', true)
                  ->orderBy('created_at', 'desc')
                  ->paginate($perPage);
    }
    
    public function getFeatured(int $limit = 5): Collection 
    {
        return Post::with('author')
                  ->where('published', true)
                  ->where('featured', true)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }
    
    public function findPublished(int $id): ?Post 
    {
        return Post::with('author')
                  ->where('published', true)
                  ->find($id);
    }
}

class PostController 
{
    public function __construct(private PostRepositoryInterface $postRepository) {}
    
    public function index() 
    {
        $posts = $this->postRepository->getPublished();
        return view('posts.index', compact('posts'));
    }
    
    public function featured() 
    {
        $posts = $this->postRepository->getFeatured();
        return view('posts.featured', compact('posts'));
    }
}
```

**Benefits**:
- **Reusable queries**: Same logic used in multiple places
- **Testable**: Can mock repository for testing
- **Cacheable**: Easy to add caching to repository methods
- **Database agnostic**: Could swap Eloquent for raw SQL or different ORM

## Refactoring Process

### Step-by-Step Approach

1. **Identify the smell**: What makes the code hard to work with?
2. **Write tests first**: Ensure current behavior is preserved
3. **Extract in small steps**: Don't refactor everything at once
4. **Run tests after each step**: Make sure nothing breaks
5. **Clean up**: Remove dead code, improve naming

### Example: Refactoring a Fat Controller

**Starting Point**:
```php
// 150+ lines of mixed responsibilities
class OrderController extends Controller 
{
    public function create(Request $request) 
    {
        // Validation (20 lines)
        // Business logic (50 lines)
        // Database operations (30 lines)
        // Email notifications (25 lines)
        // File generation (25 lines)
    }
}
```

**Step 1: Extract Form Request**
```php
class CreateOrderRequest extends FormRequest 
{
    // Move validation here
}

class OrderController extends Controller 
{
    public function create(CreateOrderRequest $request) 
    {
        // 130 lines remaining
    }
}
```

**Step 2: Extract Service**
```php
class OrderService 
{
    // Move business logic here
}

class OrderController extends Controller 
{
    public function create(CreateOrderRequest $request) 
    {
        // 80 lines remaining
    }
}
```

**Step 3: Extract Additional Services**
```php
class OrderNotificationService { /* Email logic */ }
class OrderDocumentService { /* File generation */ }

class OrderController extends Controller 
{
    public function create(CreateOrderRequest $request) 
    {
        // 20 lines remaining - just coordination!
    }
}
```

## Common Refactoring Mistakes to Avoid

### 1. Over-Engineering
```php
// BAD: Over-abstracted for simple use case
interface ColorInterface {
    public function getRed(): int;
    public function getGreen(): int; 
    public function getBlue(): int;
}

class Color implements ColorInterface { /* ... */ }
class ColorFactory { /* ... */ }
class ColorValidator { /* ... */ }
class ColorRepository { /* ... */ }

// For displaying a simple color picker...

// GOOD: Keep it simple until complexity demands abstraction
class ColorPicker {
    public function getColors(): array {
        return ['red', 'green', 'blue', 'yellow'];
    }
}
```

### 2. Premature Abstraction
```php
// BAD: Creating interfaces before you need them
interface UserServiceInterface {
    public function createUser(array $data): User;
}

class UserService implements UserServiceInterface {
    // Only one implementation exists...
}

// GOOD: Start with concrete class, extract interface when you need multiple implementations
class UserService {
    public function createUser(array $data): User {
        // Implementation
    }
}
```

### 3. Breaking Too Much at Once
```php
// BAD: Refactoring entire codebase in one commit
// - Extract all services
// - Add all interfaces  
// - Implement all patterns
// - Change directory structure
// Result: Nothing works, hard to debug

// GOOD: One small improvement at a time
// Commit 1: Extract validation to Form Request
// Commit 2: Extract email logic to service
// Commit 3: Add interface for email service
// Each step works and can be tested independently
```

## Ready for the Advanced Material

**You're ready to tackle `01 - Design Principles & Refactoring` when you can**:

- [ ] Recognize code smells in existing Laravel code
- [ ] Extract service classes from fat controllers
- [ ] Create and use Form Requests for validation
- [ ] Apply dependency injection using Laravel's container
- [ ] Write interfaces and implement them
- [ ] Refactor code in small, testable steps
- [ ] Use repositories to abstract data access

The advanced material shows these patterns applied to complex, real-world Laravel applications with sophisticated business logic. With this foundation, you'll understand not just *what* the refactored code does, but *why* each refactoring decision was made.
