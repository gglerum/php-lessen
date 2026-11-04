# Professional PHP Development Practices

You've learned Laravel basics and advanced OOP concepts. Now you need the professional development practices that separate hobby projects from production-ready applications. This guide covers the essential skills for working on real PHP/Laravel projects.

## What Makes Code "Professional"?

Professional code isn't just code that works - it's code that:
- **Can be safely modified** without breaking other features
- **Communicates clearly** to other developers (including future you)
- **Handles errors gracefully** instead of crashing mysteriously
- **Performs well** under realistic load conditions
- **Protects against security vulnerabilities**
- **Can be deployed confidently** to production environments

## Essential Professional Practices

### 1. Testing Your Code

**The Problem**: Manual testing doesn't scale. As your application grows, you can't manually test every feature after every change.

**The Solution**: Automated tests that run quickly and catch regressions.

#### Unit Testing Basics
```php
// Simple class to test
class Calculator 
{
    public function add(int $a, int $b): int 
    {
        return $a + $b;
    }
    
    public function divide(int $a, int $b): float 
    {
        if ($b === 0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return $a / $b;
    }
}

// PHPUnit test
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase 
{
    private Calculator $calculator;
    
    protected function setUp(): void 
    {
        $this->calculator = new Calculator();
    }
    
    public function test_adds_two_numbers(): void 
    {
        // Arrange
        $a = 5;
        $b = 3;
        
        // Act  
        $result = $this->calculator->add($a, $b);
        
        // Assert
        $this->assertEquals(8, $result);
    }
    
    public function test_divides_two_numbers(): void 
    {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5.0, $result);
    }
    
    public function test_throws_exception_when_dividing_by_zero(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot divide by zero');
        
        $this->calculator->divide(10, 0);
    }
}
```

#### Laravel Testing Example
```php
// Testing a Laravel controller
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadTest extends TestCase 
{
    public function test_user_can_upload_file(): void 
    {
        // Arrange - create test data
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 100);
        
        // Act - perform the action
        $response = $this->actingAs($user)
                        ->post('/upload', ['file' => $file]);
        
        // Assert - verify results
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Upload successful']);
        Storage::disk('local')->assertExists("uploads/{$file->hashName()}");
    }
    
    public function test_upload_fails_with_invalid_file(): void 
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('malware.exe', 100);
        
        $response = $this->actingAs($user)
                        ->post('/upload', ['file' => $file]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }
}
```

**Running Tests**:
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/Unit/CalculatorTest.php

# Laravel Artisan (if using Laravel)
php artisan test

# Run tests with coverage
php artisan test --coverage
```

### 2. Error Handling and Logging

**The Problem**: Errors happen in production. Without proper handling, they crash your application or expose sensitive information.

#### Proper Exception Handling
```php
// BAD: Silently failing or exposing internals
class FileUploadService 
{
    public function uploadFile(UploadedFile $file): string 
    {
        try {
            return $file->store('uploads');
        } catch (Exception $e) {
            // BAD: Silently fail
            return '';
            
            // BAD: Expose internal details to user
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }
}
```

```php
// GOOD: Proper exception handling with logging
use Psr\Log\LoggerInterface;

class FileUploadService 
{
    public function __construct(private LoggerInterface $logger) {}
    
    public function uploadFile(UploadedFile $file): string 
    {
        try {
            $path = $file->store('uploads');
            
            $this->logger->info('File uploaded successfully', [
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize()
            ]);
            
            return $path;
            
        } catch (Exception $e) {
            // Log the technical details
            $this->logger->error('File upload failed', [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Throw user-friendly exception
            throw new FileUploadException('Unable to upload file. Please try again.');
        }
    }
}

// Custom exception for better error handling
class FileUploadException extends Exception 
{
    public function __construct(string $message = 'File upload failed', int $code = 0, ?Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}
```

#### Laravel Error Handling
```php
// Custom exception handler (app/Exceptions/Handler.php)
class Handler extends ExceptionHandler 
{
    public function render($request, Throwable $exception) 
    {
        // Handle our custom exceptions
        if ($exception instanceof FileUploadException) {
            return response()->json([
                'error' => $exception->getMessage(),
                'type' => 'upload_error'
            ], 400);
        }
        
        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $exception->errors()
            ], 422);
        }
        
        return parent::render($request, $exception);
    }
}
```

### 3. Security Fundamentals

**The Reality**: Security vulnerabilities can destroy businesses. Every professional developer must understand basic security principles.

#### Input Validation and Sanitization
```php
// BAD: Trusting user input
class UserController 
{
    public function updateProfile(Request $request) 
    {
        $user = auth()->user();
        
        // BAD: No validation
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();
        
        return response()->json(['message' => 'Profile updated']);
    }
}
```

```php
// GOOD: Proper validation
class UpdateProfileRequest extends FormRequest 
{
    public function authorize(): bool 
    {
        return auth()->check();
    }
    
    public function rules(): array 
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ];
    }
    
    public function messages(): array 
    {
        return [
            'name.regex' => 'Name can only contain letters and spaces.',
            'email.unique' => 'This email is already taken.',
        ];
    }
}

class UserController 
{
    public function updateProfile(UpdateProfileRequest $request) 
    {
        $user = auth()->user();
        
        // Data is already validated by the FormRequest
        $user->update($request->validated());
        
        return response()->json(['message' => 'Profile updated successfully']);
    }
}
```

#### SQL Injection Prevention
```php
// BAD: SQL injection vulnerability
class UserRepository 
{
    public function findByEmail(string $email): ?User 
    {
        // NEVER DO THIS - SQL injection risk
        $sql = "SELECT * FROM users WHERE email = '$email'";
        return DB::select($sql);
    }
}
```

```php
// GOOD: Using Laravel's query builder or prepared statements
class UserRepository 
{
    public function findByEmail(string $email): ?User 
    {
        // GOOD: Laravel query builder automatically prevents SQL injection
        return User::where('email', $email)->first();
        
        // GOOD: Raw queries with parameter binding
        // return DB::selectOne('SELECT * FROM users WHERE email = ?', [$email]);
    }
}
```

#### Authentication and Authorization
```php
// Proper middleware usage
class FileController extends Controller 
{
    public function __construct() 
    {
        // Ensure user is authenticated
        $this->middleware('auth');
        
        // Ensure user has verified email
        $this->middleware('verified');
        
        // Rate limiting
        $this->middleware('throttle:60,1')->only(['store']);
    }
    
    public function store(StoreFileRequest $request) 
    {
        // Additional authorization checks
        $this->authorize('upload-files');
        
        // Or check specific permissions
        if (!auth()->user()->can('upload', File::class)) {
            abort(403, 'You do not have permission to upload files.');
        }
        
        // Process the upload...
    }
}
```

### 4. Performance Considerations

**The Goal**: Write code that performs well under realistic conditions, not just in development.

#### Database Query Optimization
```php
// BAD: N+1 query problem
class PostController 
{
    public function index() 
    {
        $posts = Post::all();  // 1 query
        
        foreach ($posts as $post) {
            echo $post->user->name;  // N additional queries!
        }
    }
}
```

```php
// GOOD: Eager loading
class PostController 
{
    public function index() 
    {
        // Load posts with their users in just 2 queries
        $posts = Post::with('user')->get();
        
        foreach ($posts as $post) {
            echo $post->user->name;  // No additional queries
        }
    }
    
    public function show(Post $post) 
    {
        // Load specific relationships only when needed
        return $post->load(['user', 'comments.user']);
    }
}
```

#### Caching Strategies
```php
class ReportService 
{
    public function getMonthlyStats(): array 
    {
        // Cache expensive calculations
        return Cache::remember('monthly-stats', now()->addHour(), function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('last_login_at', '>', now()->subDays(30))->count(),
                'total_uploads' => File::sum('size'),
                'popular_files' => File::orderBy('download_count', 'desc')->limit(10)->get()
            ];
        });
    }
    
    public function getUserUploadStats(User $user): array 
    {
        // Cache per user with tags for easy invalidation
        return Cache::tags(['user-stats', "user-{$user->id}"])
                   ->remember("user-upload-stats-{$user->id}", now()->addMinutes(30), function () use ($user) {
                       return [
                           'total_files' => $user->files()->count(),
                           'total_size' => $user->files()->sum('size'),
                           'recent_files' => $user->files()->latest()->limit(5)->get()
                       ];
                   });
    }
    
    public function invalidateUserStats(User $user): void 
    {
        // Clear specific user's cache when they upload new files
        Cache::tags(["user-{$user->id}"])->flush();
    }
}
```

#### Efficient File Handling
```php
class FileProcessor 
{
    public function processLargeFile(string $filePath): void 
    {
        // BAD: Loading entire file into memory
        // $content = file_get_contents($filePath);
        // $lines = explode("\n", $content);
        
        // GOOD: Process line by line for large files
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Cannot open file: $filePath");
        }
        
        try {
            while (($line = fgets($handle)) !== false) {
                $this->processLine($line);
                
                // Optional: Yield control periodically for long-running processes
                if (rand(1, 1000) === 1) {
                    usleep(1000); // Brief pause every ~1000 lines
                }
            }
        } finally {
            fclose($handle);
        }
    }
    
    private function processLine(string $line): void 
    {
        // Process individual line
        $data = json_decode(trim($line), true);
        if ($data) {
            // Save to database, send to queue, etc.
        }
    }
}
```

### 5. Code Organization and Architecture

**The Goal**: Organize code so teams can work efficiently and features can be added without breaking existing functionality.

#### Directory Structure for Larger Projects
```
app/
├── Console/
│   └── Commands/         # Artisan commands
├── Exceptions/          # Custom exceptions
│   ├── FileUploadException.php
│   └── ValidationException.php
├── Http/
│   ├── Controllers/     # Keep controllers thin
│   ├── Middleware/      # Custom middleware
│   └── Requests/        # Form validation
├── Models/             # Eloquent models
├── Policies/           # Authorization logic
├── Services/           # Business logic
│   ├── FileUpload/
│   │   ├── FileUploadService.php
│   │   ├── FileValidator.php
│   │   └── StorageManager.php
│   └── User/
│       ├── UserService.php
│       └── UserPreferencesService.php
├── Repositories/       # Data access layer
│   ├── Contracts/      # Repository interfaces
│   └── Eloquent/       # Implementations
└── ValueObjects/       # Domain value objects
    ├── FileSize.php
    ├── EmailAddress.php
    └── Money.php
```

#### Configuration Management
```php
// Don't scatter configuration throughout your code
class FileUploadService 
{
    // BAD: Hard-coded values
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private const ALLOWED_TYPES = ['pdf', 'jpg', 'png'];
    
    // GOOD: Use configuration files
    public function __construct(private array $config) {}
    
    public function isValidFile(UploadedFile $file): bool 
    {
        return $file->getSize() <= $this->config['max_size']
            && in_array($file->getClientOriginalExtension(), $this->config['allowed_types']);
    }
}

// config/fileupload.php
return [
    'max_size' => env('FILE_UPLOAD_MAX_SIZE', 10 * 1024 * 1024),
    'allowed_types' => ['pdf', 'jpg', 'png', 'docx'],
    'storage_disk' => env('FILE_UPLOAD_DISK', 'local'),
    'virus_scanning_enabled' => env('VIRUS_SCANNING_ENABLED', false),
];

// Usage with Laravel's service container
class FileUploadService 
{
    public function __construct() 
    {
        $this->config = config('fileupload');
    }
}
```

### 6. Environment Management

**The Goal**: Different settings for development, testing, and production environments.

#### Environment Variables (.env files)
```bash
# .env (never commit to version control)
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:your-32-character-key-here

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Custom application settings
FILE_UPLOAD_MAX_SIZE=10485760
VIRUS_SCANNING_ENABLED=false
```

```bash
# .env.example (commit this to show required variables)
APP_ENV=local
APP_DEBUG=true
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Add descriptions for custom variables
FILE_UPLOAD_MAX_SIZE=10485760  # Maximum file size in bytes
VIRUS_SCANNING_ENABLED=false   # Enable virus scanning for uploads
```

#### Configuration Best Practices
```php
// GOOD: Environment-specific behavior
class EmailService 
{
    public function sendEmail(string $to, string $subject, string $body): void 
    {
        if (app()->environment('local', 'testing')) {
            // In development, log emails instead of sending
            Log::info("Email would be sent to {$to}", [
                'subject' => $subject,
                'body' => $body
            ]);
            return;
        }
        
        if (app()->environment('staging')) {
            // In staging, only send to approved domains
            $allowedDomains = ['mycompany.com', 'gmail.com'];
            $domain = explode('@', $to)[1] ?? '';
            
            if (!in_array($domain, $allowedDomains)) {
                Log::info("Email blocked in staging environment", ['to' => $to]);
                return;
            }
        }
        
        // Production: send normally
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
}
```

### 7. Deployment and Production Readiness

#### Health Checks and Monitoring
```php
// Create health check endpoints
class HealthController extends Controller 
{
    public function basic(): JsonResponse 
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment()
        ]);
    }
    
    public function detailed(): JsonResponse 
    {
        $checks = [];
        
        // Database connectivity
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'healthy';
        } catch (Exception $e) {
            $checks['database'] = 'unhealthy';
        }
        
        // Storage accessibility
        try {
            Storage::disk('local')->put('health-check.txt', 'test');
            Storage::disk('local')->delete('health-check.txt');
            $checks['storage'] = 'healthy';
        } catch (Exception $e) {
            $checks['storage'] = 'unhealthy';
        }
        
        // Memory usage
        $checks['memory'] = [
            'used' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
        
        $allHealthy = !in_array('unhealthy', $checks);
        
        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ], $allHealthy ? 200 : 503);
    }
}
```

## Common Professional Development Mistakes

### 1. Not Writing Tests
**Mistake**: "I'll add tests later" (you never do)
**Solution**: Write tests as you develop, not after

### 2. Poor Error Messages
**Mistake**: Generic "Something went wrong" messages
**Solution**: Specific, actionable error messages for users; detailed logging for developers

### 3. Hard-Coding Configuration
**Mistake**: Magic numbers and strings scattered throughout code
**Solution**: Centralized configuration files and environment variables

### 4. Ignoring Performance Until Too Late
**Mistake**: "We'll optimize when we need to scale"
**Solution**: Write efficient code from the start, measure performance regularly

### 5. No Monitoring or Logging
**Mistake**: Finding out about production issues from angry users
**Solution**: Comprehensive logging and monitoring from day one

## Tools for Professional PHP Development

### Essential Tools
```bash
# Dependency management
composer install
composer update

# Code quality
./vendor/bin/phpstan analyse          # Static analysis
./vendor/bin/php-cs-fixer fix         # Code formatting
./vendor/bin/phpunit                  # Testing

# Laravel specific
php artisan migrate                   # Database migrations
php artisan queue:work               # Process background jobs
php artisan schedule:run             # Run scheduled tasks
```

### Development Workflow
```bash
# 1. Feature development
git checkout -b feature/user-uploads
# Write code, write tests, commit frequently

# 2. Code quality checks
./vendor/bin/phpstan analyse
./vendor/bin/php-cs-fixer fix
./vendor/bin/phpunit

# 3. Integration
git checkout main
git pull origin main
git checkout feature/user-uploads
git rebase main  # or merge, depending on team policy

# 4. Deploy
git push origin feature/user-uploads
# Create pull request, code review, merge, deploy
```

## Practice Project: Professional File Upload System

**Goal**: Build a production-ready file upload system demonstrating all professional practices.

### Requirements
- [ ] Users can upload multiple files with validation
- [ ] Files are scanned for viruses (simulated)
- [ ] Upload progress tracking
- [ ] Email notifications on completion
- [ ] Admin dashboard showing upload statistics
- [ ] Rate limiting and security measures
- [ ] Comprehensive test coverage
- [ ] Proper error handling and logging
- [ ] Caching for performance
- [ ] Health check endpoints

### Implementation Checklist
- [ ] **Testing**: Unit tests for services, feature tests for endpoints
- [ ] **Security**: Input validation, file type checking, rate limiting
- [ ] **Performance**: Eager loading, caching, efficient file handling
- [ ] **Error Handling**: Custom exceptions, user-friendly messages, detailed logging
- [ ] **Code Organization**: Services, repositories, value objects, proper namespacing
- [ ] **Configuration**: Environment variables, separate configs for different environments
- [ ] **Monitoring**: Health checks, performance metrics, error tracking

This project will give you hands-on experience with all professional development practices covered in this guide.

## Next Steps

**You're ready for advanced architectural topics when you can**:
- [ ] Write comprehensive tests for your code
- [ ] Handle errors gracefully with proper logging
- [ ] Implement basic security measures automatically
- [ ] Consider performance implications of your design choices
- [ ] Organize code in a logical, maintainable structure
- [ ] Use environment-specific configuration properly
- [ ] Create production-ready applications with monitoring

**Ready to continue?** Move on to the advanced materials in `Part 3 (week 27 to 39)` - you now have the foundation to understand and apply sophisticated architectural patterns in real-world, professional applications.
