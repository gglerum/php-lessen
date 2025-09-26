# From Basic OOP to Advanced Architecture

You've learned basic OOP (classes, inheritance, polymorphism) and understand Laravel basics, but the advanced materials in `Part 3 (week 27 to 39)` use sophisticated architectural concepts. This guide bridges that gap by building from simple OOP to professional-level architecture.

## The Architectural Mindset Shift

### Where You Are Now
- You can create classes and objects
- You understand inheritance and polymorphism  
- You can build simple applications with Laravel
- Your code works, but feels messy as it gets bigger

### Where You Need to Be
- You think about **responsibilities** before writing code
- You design **interfaces** before implementing classes
- You organize code around **business concepts**, not technical details
- You can **extend** applications without breaking existing features

## Key Architectural Concepts

Let's build these concepts step-by-step using familiar examples.

### 1. Dependency Injection (Without Laravel Magic)

**The Problem**: Your classes create their own dependencies, making them hard to test and inflexible.

```php
// BAD: Class creates its own dependencies
class FileUploader 
{
    public function upload(array $files) 
    {
        // Hard-coded dependency creation
        $validator = new FileValidator();  // What if we want different validation?
        $storage = new LocalStorage();     // What if we want cloud storage?
        
        if (!$validator->isValid($files)) {
            throw new Exception('Invalid files');
        }
        
        return $storage->store($files);
    }
}
```

**The Solution**: Inject dependencies through the constructor.

```php
// GOOD: Dependencies are injected
class FileUploader 
{
    public function __construct(
        private FileValidator $validator,
        private StorageInterface $storage
    ) {}
    
    public function upload(array $files) 
    {
        if (!$this->validator->isValid($files)) {
            throw new Exception('Invalid files');
        }
        
        return $this->storage->store($files);
    }
}

// Usage - you control the dependencies
$validator = new FileValidator();
$storage = new CloudStorage();  // Or LocalStorage, or DatabaseStorage
$uploader = new FileUploader($validator, $storage);
```

**Why This Matters**: 
- You can easily swap implementations (local storage → cloud storage)
- Testing becomes simple (use mock objects)
- Your class focuses on its job, not on creating dependencies

### 2. Interfaces vs Implementation

**The Problem**: Your code depends on concrete classes, making it inflexible.

```php
// BAD: Depending on concrete implementation
class UserService 
{
    public function __construct(private MySQLUserRepository $repository) {}
    
    // What if we want to use PostgreSQL? Or a web API? We're stuck.
}
```

**The Solution**: Depend on interfaces, implement with concrete classes.

```php
// GOOD: Define what you need, not how it's implemented
interface UserRepositoryInterface 
{
    public function findById(int $id): ?User;
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
}

class UserService 
{
    public function __construct(private UserRepositoryInterface $repository) {}
    
    public function createUser(string $email, string $name): User 
    {
        // Check if user already exists
        if ($this->repository->findByEmail($email)) {
            throw new Exception('User already exists');
        }
        
        // Create and save new user
        $user = new User($email, $name);
        $this->repository->save($user);
        
        return $user;
    }
}

// Different implementations
class MySQLUserRepository implements UserRepositoryInterface 
{
    public function findById(int $id): ?User 
    {
        // MySQL implementation
    }
    
    public function save(User $user): void 
    {
        // MySQL implementation
    }
    
    public function findByEmail(string $email): ?User 
    {
        // MySQL implementation
    }
}

class ApiUserRepository implements UserRepositoryInterface 
{
    public function findById(int $id): ?User 
    {
        // API implementation
    }
    
    // ... other methods
}
```

**Why This Matters**:
- You can switch from MySQL to API without changing UserService
- You can create fake repositories for testing
- Your business logic is separated from implementation details

### 3. Value Objects (Beyond Primitive Types)

**The Problem**: You use primitive types (string, int) for domain concepts, leading to bugs and unclear code.

```php
// BAD: Everything is primitives
class User 
{
    public function __construct(
        private string $email,        // What if it's not a valid email?
        private int $uploadLimit      // Bytes? KB? MB? No one knows.
    ) {}
    
    public function canUpload(int $fileSize): bool  // Again, what unit?
    {
        return $this->uploadLimit >= $fileSize;
    }
}

// Usage - error-prone
$user = new User('not-an-email', 1024);  // No validation
$canUpload = $user->canUpload(2048);     // Are these the same units?
```

**The Solution**: Create Value Objects that encapsulate behavior and validation.

```php
// GOOD: Value Objects with built-in validation and behavior
class EmailAddress 
{
    private string $email;
    
    public function __construct(string $email) 
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$email}");
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

class FileSize 
{
    private int $bytes;
    
    public function __construct(int $bytes) 
    {
        if ($bytes < 0) {
            throw new InvalidArgumentException('File size cannot be negative');
        }
        $this->bytes = $bytes;
    }
    
    public static function fromKB(float $kb): self 
    {
        return new self((int)($kb * 1024));
    }
    
    public static function fromMB(float $mb): self 
    {
        return new self((int)($mb * 1024 * 1024));
    }
    
    public function toBytes(): int 
    {
        return $this->bytes;
    }
    
    public function toKB(): float 
    {
        return $this->bytes / 1024;
    }
    
    public function isLargerThan(FileSize $other): bool 
    {
        return $this->bytes > $other->bytes;
    }
    
    public function add(FileSize $other): self 
    {
        return new self($this->bytes + $other->bytes);
    }
}

class User 
{
    public function __construct(
        private EmailAddress $email,
        private FileSize $uploadLimit
    ) {}
    
    public function canUpload(FileSize $fileSize): bool 
    {
        return $this->uploadLimit->isLargerThan($fileSize);
    }
}

// Usage - type-safe and clear
$email = new EmailAddress('user@example.com');  // Validates automatically
$uploadLimit = FileSize::fromMB(100);           // Clear units
$user = new User($email, $uploadLimit);

$fileSize = FileSize::fromKB(500);
$canUpload = $user->canUpload($fileSize);       // Type-safe comparison
```

**Why This Matters**:
- Impossible to create invalid data (email validation happens automatically)
- Clear units and operations (no confusion about bytes vs MB)
- Business logic lives in the right place (FileSize knows how to compare itself)
- Type safety prevents bugs at compile time

### 4. Service Classes (Orchestrating Business Logic)

**The Problem**: Business logic is scattered throughout controllers, models, and random helper classes.

```php
// BAD: Business logic scattered everywhere
class FileController extends Controller 
{
    public function upload(Request $request) 
    {
        // Validation logic in controller
        if (count($request->files) > 10) {
            throw new Exception('Too many files');
        }
        
        // Business logic in controller
        $totalSize = 0;
        foreach ($request->files as $file) {
            $totalSize += $file->getSize();
        }
        
        $user = auth()->user();
        if ($user->upload_limit < $totalSize) {
            throw new Exception('Upload limit exceeded');
        }
        
        // Storage logic in controller
        foreach ($request->files as $file) {
            $path = $file->store('uploads');
            // Save to database...
        }
    }
}
```

**The Solution**: Create Service classes that encapsulate complete business operations.

```php
// GOOD: Service class orchestrates the business operation
class FileUploadService 
{
    public function __construct(
        private FileValidator $validator,
        private StorageInterface $storage,
        private UserRepositoryInterface $userRepository
    ) {}
    
    public function uploadFiles(User $user, array $files): UploadResult 
    {
        // Step 1: Validate files
        $this->validator->validateFiles($files);
        
        // Step 2: Check user limits
        $totalSize = FileSize::fromFiles($files);
        if (!$user->canUpload($totalSize)) {
            throw new UploadLimitExceededException($totalSize, $user->getUploadLimit());
        }
        
        // Step 3: Store files
        $storedFiles = [];
        foreach ($files as $file) {
            $storedFiles[] = $this->storage->store($file);
        }
        
        // Step 4: Update user's upload total
        $user->addToUploadTotal($totalSize);
        $this->userRepository->save($user);
        
        // Step 5: Return result
        return new UploadResult($storedFiles, $totalSize);
    }
}

// Controller becomes thin and focused
class FileController extends Controller 
{
    public function upload(Request $request, FileUploadService $uploadService) 
    {
        try {
            $result = $uploadService->uploadFiles(
                auth()->user(),
                $request->file('files')
            );
            
            return response()->json([
                'message' => 'Upload successful',
                'files_count' => count($result->getFiles()),
                'total_size' => $result->getTotalSize()->toMB() . ' MB'
            ]);
            
        } catch (UploadLimitExceededException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

**Why This Matters**:
- Business logic is centralized and reusable
- Controller focuses on HTTP concerns only
- Easy to test business logic in isolation
- Clear separation of responsibilities

### 5. The Repository Pattern (Data Access Layer)

**The Problem**: Database queries are scattered throughout your application.

```php
// BAD: Database queries everywhere
class UserController 
{
    public function show(int $id) 
    {
        $user = User::where('id', $id)->where('active', true)->first();
        // What if this query changes? We have to find all occurrences.
    }
}

class UserService 
{
    public function findActiveUser(int $id) 
    {
        // Duplicate query logic
        return User::where('id', $id)->where('active', true)->first();
    }
}
```

**The Solution**: Centralize data access in Repository classes.

```php
// GOOD: Repository encapsulates data access
interface UserRepositoryInterface 
{
    public function findActiveById(int $id): ?User;
    public function findActiveByEmail(string $email): ?User;
    public function findUsersWithUploadLimitExceeded(): array;
    public function save(User $user): void;
}

class EloquentUserRepository implements UserRepositoryInterface 
{
    public function findActiveById(int $id): ?User 
    {
        return User::where('id', $id)
                  ->where('active', true)
                  ->first();
    }
    
    public function findActiveByEmail(string $email): ?User 
    {
        return User::where('email', $email)
                  ->where('active', true)
                  ->first();
    }
    
    public function findUsersWithUploadLimitExceeded(): array 
    {
        return User::whereRaw('upload_total > upload_limit')
                  ->where('active', true)
                  ->get()
                  ->toArray();
    }
    
    public function save(User $user): void 
    {
        $user->save();
    }
}

// Services use repositories
class UserService 
{
    public function __construct(private UserRepositoryInterface $repository) {}
    
    public function findActiveUser(int $id): ?User 
    {
        return $this->repository->findActiveById($id);
    }
    
    public function getUsersExceedingUploadLimit(): array 
    {
        return $this->repository->findUsersWithUploadLimitExceeded();
    }
}
```

**Why This Matters**:
- Query logic is centralized and reusable
- Easy to change database implementation
- Clear, business-focused method names
- Simple to create fake repositories for testing

## Putting It All Together: A Complete Example

Here's how these concepts work together in a realistic scenario:

```php
// Value Objects
class FileSize { /* implementation from above */ }
class EmailAddress { /* implementation from above */ }

// Entities
class User 
{
    public function __construct(
        private int $id,
        private EmailAddress $email,
        private FileSize $uploadLimit,
        private FileSize $uploadTotal
    ) {}
    
    public function canUpload(FileSize $fileSize): bool 
    {
        return $this->uploadTotal
                   ->add($fileSize)
                   ->isLargerThan($this->uploadLimit) === false;
    }
    
    public function addToUploadTotal(FileSize $size): void 
    {
        $this->uploadTotal = $this->uploadTotal->add($size);
    }
    
    // Getters...
    public function getId(): int { return $this->id; }
    public function getEmail(): EmailAddress { return $this->email; }
    public function getUploadTotal(): FileSize { return $this->uploadTotal; }
}

// Repository Interface
interface UserRepositoryInterface 
{
    public function findById(int $id): ?User;
    public function save(User $user): void;
}

// Service that coordinates business logic
class FileUploadService 
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private StorageInterface $storage,
        private FileValidator $validator
    ) {}
    
    public function uploadFilesForUser(int $userId, array $files): UploadResult 
    {
        // Step 1: Get user
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException($userId);
        }
        
        // Step 2: Validate files
        $this->validator->validate($files);
        
        // Step 3: Check upload limits
        $totalSize = FileSize::fromFiles($files);
        if (!$user->canUpload($totalSize)) {
            throw new UploadLimitExceededException();
        }
        
        // Step 4: Store files
        $storedFiles = [];
        foreach ($files as $file) {
            $storedFiles[] = $this->storage->store($file);
        }
        
        // Step 5: Update user
        $user->addToUploadTotal($totalSize);
        $this->userRepository->save($user);
        
        return new UploadResult($storedFiles, $totalSize);
    }
}

// Thin controller
class FileController extends Controller 
{
    public function upload(
        Request $request,
        FileUploadService $uploadService
    ) {
        try {
            $result = $uploadService->uploadFilesForUser(
                auth()->id(),
                $request->file('files')
            );
            
            return response()->json(['success' => true, 'result' => $result]);
            
        } catch (UploadLimitExceededException $e) {
            return response()->json(['error' => 'Upload limit exceeded'], 400);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
}
```

## Testing with This Architecture

One of the biggest benefits of this approach is how easy testing becomes:

```php
class FileUploadServiceTest extends TestCase 
{
    public function test_uploads_files_successfully() 
    {
        // Arrange - create mock dependencies
        $mockUserRepo = $this->createMock(UserRepositoryInterface::class);
        $mockStorage = $this->createMock(StorageInterface::class);
        $mockValidator = $this->createMock(FileValidator::class);
        
        $user = new User(
            1,
            new EmailAddress('test@example.com'),
            FileSize::fromMB(100),  // 100MB limit
            FileSize::fromMB(50)    // 50MB already used
        );
        
        // Set up expectations
        $mockUserRepo->expects($this->once())
                    ->method('findById')
                    ->with(1)
                    ->willReturn($user);
                    
        $mockValidator->expects($this->once())
                     ->method('validate')
                     ->with($this->anything());
                     
        $mockStorage->expects($this->once())
                   ->method('store')
                   ->willReturn('stored_file_path');
        
        // Create service with mocks
        $service = new FileUploadService($mockUserRepo, $mockStorage, $mockValidator);
        
        // Act
        $result = $service->uploadFilesForUser(1, ['fake_file']);
        
        // Assert
        $this->assertInstanceOf(UploadResult::class, $result);
    }
    
    public function test_throws_exception_when_upload_limit_exceeded() 
    {
        // Similar setup but with user at their limit
        $user = new User(
            1,
            new EmailAddress('test@example.com'),
            FileSize::fromMB(100),  // 100MB limit
            FileSize::fromMB(99)    // 99MB already used
        );
        
        // ... test that uploading 2MB file throws UploadLimitExceededException
    }
}
```

## Common Mistakes to Avoid

### 1. Over-Engineering
Don't create interfaces and services for every tiny class. Start simple and add complexity when you actually need it.

### 2. Anemic Models
Don't create models that are just data containers. Put business logic in your entities:

```php
// BAD: Anemic model
class User 
{
    public int $uploadLimit;
    public int $uploadTotal;
    
    // No behavior, just getters/setters
}

// GOOD: Rich model
class User 
{
    public function canUpload(FileSize $size): bool 
    {
        // Business logic in the entity
        return $this->uploadTotal->add($size)->isLargerThan($this->uploadLimit) === false;
    }
}
```

### 3. Fat Services
Don't put everything in one giant service. Break them down by business capability:

```php
// BAD: One service does everything
class ApplicationService 
{
    public function uploadFiles() { /* ... */ }
    public function sendEmail() { /* ... */ }  
    public function generateReport() { /* ... */ }
    public function calculateTax() { /* ... */ }
}

// GOOD: Focused services
class FileUploadService { /* ... */ }
class EmailService { /* ... */ }
class ReportService { /* ... */ }
class TaxCalculationService { /* ... */ }
```

## Next Steps

You now understand the architectural concepts used throughout the advanced materials. When you see the `Part 3 (week 27 to 39)` guides:

- **GRASP Principles**: You'll understand how to assign responsibilities
- **SOLID Principles**: You'll recognize the interface and dependency patterns
- **Clean Code**: You'll appreciate why the refactored examples are better
- **Domain Driven Design**: You'll see how business concepts drive the architecture
- **Design Patterns**: You'll understand the problems they solve

**Practice Exercise**: Take the simple Laravel file upload from the Laravel Basics guide and refactor it using these concepts:
1. Create a FileSize value object
2. Create a FileUploadService
3. Create a UserRepositoryInterface
4. Extract validation into a separate class
5. Write unit tests for your service

This hands-on practice will solidify these concepts before you dive into the advanced principles.
