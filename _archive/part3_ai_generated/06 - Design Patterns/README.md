# Design Patterns in Laravel

Design patterns are proven solutions to recurring problems in software development. Laravel, as a modern PHP framework, makes extensive use of many classic design patterns while also introducing its own architectural patterns. Understanding these patterns helps you write more maintainable, testable, and scalable applications.

## What You Need to Know First

### About Laravel
Laravel is a PHP web framework that provides structure and tools for building web applications. If you're new to Laravel, think of it as a toolkit that handles common web development tasks (routing, databases, authentication) so you can focus on your application's unique features.

**Key Laravel Concepts You'll See:**
- **Controllers**: Handle web requests (like form submissions)
- **Models**: Represent your data (users, files, etc.)
- **Requests**: Validate incoming data
- **Services**: Contain your business logic
- **Facades**: Easy shortcuts to Laravel features (`auth()`, `Storage::`, etc.)

*Don't worry if these are unfamiliar - we'll explain as we go.*

### Prerequisites
- Basic PHP OOP (classes, inheritance, interfaces)
- Understanding of design principles (SOLID, DRY)
- Willingness to learn Laravel concepts gradually

## Why Design Patterns Matter in Laravel Development

### The Construction Site Analogy
Think of design patterns as standardized construction techniques used by professional builders. Just like electricians always wire houses the same proven way, and plumbers follow standard pipe layouts, software developers use design patterns to solve common problems in consistent, reliable ways.

### For Junior Developers
After mastering OOP basics and design principles, understanding patterns helps you:
- **Recognize common solutions** instead of reinventing the wheel
- **Communicate effectively** with other developers using shared vocabulary
- **Build scalable applications** using proven architectural approaches
- **Read Laravel's source code** and understand framework decisions

## Core Laravel Design Patterns

Before we dive into patterns, let's understand the Laravel syntax you'll see in examples:

### Laravel Syntax Quick Reference
```php
// 1. Controllers - Handle web requests
class FileController extends Controller  // Laravel base class
{
    public function store(Request $request)  // $request has form data
    {
        // Your logic here
    }
}

// 2. Models - Represent your data
class User extends Model  // Laravel base class gives database powers
{
    public function files()  // This creates a relationship
    {
        return $this->hasMany(File::class);  // One user has many files
    }
}

// 3. Dependency Injection - Laravel automatically provides what you need
public function store(Request $request, User $user)  // Laravel gives you these
{
    // $request and $user are automatically available
}

// 4. Facades - Laravel shortcuts
auth()->user()           // Get current logged-in user
Storage::put($path, $file)  // Store a file
Mail::send($email)       // Send an email

// 5. Blade Templates - Laravel's view system
{{-- This is a Blade comment --}}
{{ $user->name }}        // Show user's name
@if($user->isAdmin())    // Blade if statement
    <p>You're an admin!</p>
@endif
```

**Don't panic if this looks complex!** Each pattern will explain the Laravel parts as we use them.

### Learning Approach
We'll start simple and build up complexity:

**🌱 BEGINNER PATTERNS** (Start Here)
- Pattern 1: MVC - Separate your concerns
- Pattern 2: Service Layer - Keep controllers thin
- Pattern 3: Strategy - Replace if/else chains

**🌿 INTERMEDIATE PATTERNS** (Laravel-Specific)  
- Pattern 4: Dependency Injection - Let Laravel provide what you need
- Pattern 6: Facade - Use Laravel's shortcuts
- Pattern 8: Singleton - Laravel's container magic

**🌳 ADVANCED PATTERNS** (Laravel Framework Deep Dive)
- Pattern 7: Observer - Laravel Events
- Pattern 13: Chain of Responsibility - Middleware
- Pattern 21: Factory Method - Laravel's internal magic

*Recommendation: Master the first 3 patterns before moving on. Each pattern builds on previous knowledge.*

Let's examine the key design patterns that Laravel uses and how they transform our file upload system from chaotic code into professional architecture.

---

# 🌱 BEGINNER PATTERNS

*These patterns work in any PHP application, not just Laravel. Master these first!*

## Pattern 1: Model-View-Controller (MVC)

### The Basic Idea
MVC separates application logic into three interconnected components: Models handle data, Views present information, and Controllers coordinate between them.

### Construction Analogy
Like separating construction roles: architects (Controllers) coordinate the project, blueprints (Views) show what the client sees, and the actual building materials and foundation (Models) represent the core structure. Each role has clear responsibilities and doesn't interfere with others.

### What This Means in Practice
- **Models**: Handle data, business rules, and database interactions
- **Views**: Present data to users (Blade templates, JSON responses)
- **Controllers**: Coordinate between Models and Views, handle HTTP requests

### Why Should You Care?
MVC prevents spaghetti code by creating clear boundaries. Changes to how data is displayed don't break business logic, and database changes don't require view modifications.

### The Sanity Check
If your controller contains HTML, your view contains database queries, or your model handles HTTP responses, you're violating MVC.

### Bad Example (From Our Bad Implementation)
```php
// From: bad/app/Http/Controllers/FileController.php
class FileController extends Controller
{
    public function store($request)  // Simplified - normally StoreFileRequest
    {
        // BAD: Controller doing Model work (business logic)
        $files = $request['files'];  // Get uploaded files
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();  // Calculate total size
        }
        
        // BAD: Controller checking business rules (Model's job)
        $user = auth()->user();  // Laravel helper to get current user
        if ($user->upload_limit < $totalSize + $user->total_uploaded) {
            return "Upload limit exceeded!";  // BAD: Controller returning raw text
        }

        // BAD: Controller doing direct database work (Model's job)
        foreach ($files as $file) {
            // Save to database directly in controller
            DB::table('files')->insert([
                'user_id' => $user->id,
                'filename' => $file->getName(),
                'size' => $file->getSize()
            ]);
        }
        
        // BAD: No clear View separation - HTML mixed with logic
        return "<h1>Upload successful!</h1><p>Uploaded {$totalSize} bytes</p>";
    }
}
```

**What's wrong here?** Everything is mixed together! The Controller is doing Model work (database), View work (HTML), AND Controller work (coordination).
```

### Good Example (From Our Better Implementation)
```php
// GOOD: Controller Layer - Only coordinates, doesn't do the work
class FileController extends Controller
{
    public function store($request, User $user, FileService $fileService)
    {
        // Laravel automatically gives us $user and $fileService (dependency injection)
        
        // GOOD: Controller just coordinates - delegates actual work
        $files = $request['files'];
        $fileService->uploadFiles($user, $files);  // Service does the work
        
        // GOOD: Return a proper view instead of raw HTML
        return view('upload-success', ['fileCount' => count($files)]);
    }
}

// GOOD: Model Layer - Handles its own data and business rules
class User extends Model  // Laravel's base Model class
{
    public function canUpload($fileSize): bool
    {
        // GOOD: Business logic lives in the Model
        return ($this->total_uploaded + $fileSize) <= $this->upload_limit;
    }
    
    public function addToUploadTotal($size): void
    {
        // GOOD: Model updates itself
        $this->total_uploaded += $size;
        $this->save();  // Laravel method to save to database
    }
}

// GOOD: Service Layer - Contains complex business logic
class FileService
{
    public function uploadFiles(User $user, array $files): void
    {
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }
        
        // Check business rule using Model
        if (!$user->canUpload($totalSize)) {
            throw new Exception('Upload limit exceeded');
        }
        
        // Store files and update user
        foreach ($files as $file) {
            $this->storeFile($user, $file);  // Separate method for single file
        }
        
        $user->addToUploadTotal($totalSize);
    }
    
    private function storeFile(User $user, $file): void
    {
        // File storage logic here
    }
}
```

```html
<!-- GOOD: View Layer - Clean HTML template -->
<!-- File: resources/views/upload-success.blade.php -->
<div class="success-message">
    <h1>Upload Successful!</h1>
    <p>Successfully uploaded {{ $fileCount }} files.</p>
    <a href="/dashboard">Back to Dashboard</a>
</div>
```

**What's better?** Each layer has ONE job:
- **Controller**: Coordinates between layers
- **Model**: Handles data and business rules  
- **Service**: Contains complex logic
- **View**: Just displays information

### Try This Yourself: Simple MVC Exercise

**Goal**: Create a simple blog post system using MVC

**Step 1**: Create a Model (just plain PHP first)
```php
class BlogPost
{
    private array $posts = [];
    
    public function addPost(string $title, string $content): void
    {
        $this->posts[] = ['title' => $title, 'content' => $content, 'id' => count($this->posts) + 1];
    }
    
    public function getAllPosts(): array
    {
        return $this->posts;
    }
    
    public function getPost(int $id): ?array
    {
        foreach ($this->posts as $post) {
            if ($post['id'] === $id) return $post;
        }
        return null;
    }
}
```

**Step 2**: Create a Controller (coordinates everything)
```php
class BlogController
{
    private BlogPost $blogModel;
    
    public function __construct(BlogPost $blogModel)
    {
        $this->blogModel = $blogModel;
    }
    
    public function createPost(array $data): string
    {
        // Validate (Controller's job to coordinate)
        if (empty($data['title']) || empty($data['content'])) {
            return $this->showError("Title and content required");
        }
        
        // Delegate to Model (Model's job to handle data)
        $this->blogModel->addPost($data['title'], $data['content']);
        
        // Return View (View's job to display)
        return $this->showSuccess("Post created successfully!");
    }
    
    private function showError(string $message): string
    {
        return "<div style='color: red;'>Error: {$message}</div>";
    }
    
    private function showSuccess(string $message): string
    {
        return "<div style='color: green;'>Success: {$message}</div>";
    }
}
```

**Step 3**: Try it out
```php
$blog = new BlogPost();
$controller = new BlogController($blog);

// Test the pattern
echo $controller->createPost(['title' => 'My First Post', 'content' => 'Hello World!']);
// Should show: Success: Post created successfully!

echo $controller->createPost(['title' => '', 'content' => 'No title']);
// Should show: Error: Title and content required
```

**Key Learning**: Notice how each class has ONE responsibility. Can you identify what would break if you put database logic in the Controller or HTML generation in the Model?

## Pattern 2: Service Layer Pattern

### The Basic Idea
The Service Layer pattern encapsulates business logic in dedicated service classes, keeping controllers thin and business rules centralized.

### Construction Analogy
Like having specialist contractors: instead of the general contractor doing electrical work, plumbing, and painting, you hire an electrician (Service) who knows all the electrical codes and best practices. The general contractor (Controller) coordinates, but specialists handle the complex work.

### What This Means in Practice
- Services contain complex business logic
- Controllers delegate to services rather than doing work themselves
- Services can be reused across different controllers
- Business logic is testable in isolation

### Why Should You Care?
Thin controllers and focused services make your application easier to maintain, test, and extend. Business logic changes don't require touching controllers or views.

### The Sanity Check
If your controller method is longer than 10 lines or contains complex logic, you need a service layer.

### Bad Example (From Our Bad Implementation)
```php
// Controller doing all the business logic work (actual code from bad example)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Business logic scattered in controller
        $user = auth()->user();
        $application = $user->application;
        
        // Complex file size calculation logic
        $totalUploadedSize = array_reduce($request->file('files'), 
            fn(int $carry, $file) => $carry += $file->getSize(), 0);
            
        // Upload limit business rules in controller
        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages(['max_upload_size' => 'Upload limit exceeded']);
        }

        // File storage business logic in controller
        foreach ($request->file('files') as $file) {
            $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
            // ... more business logic
        }
    }
}
```

### Good Example (From Our Better Implementation)
```php
// From: better/app/Http/Controllers/FileController.php (Thin Controller)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        $validatedData = $request->validated();

        // Controller delegates to service - no business logic here
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );

        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}

// From: better/app/Services/AnswerService.php (Service Layer)
class AnswerService
{
    public function __construct(private Collection $handlers) {}

    public function answerQuestion(Question $question, array $data): void
    {
        // Service contains the business logic for coordinating answer handlers
        $this->handlers->first(fn($h) => $h->canHandle($question))
            ->handle($question, $data);
    }
}
```

## Pattern 3: Strategy Pattern

### The Basic Idea
The Strategy pattern defines a family of algorithms, encapsulates each one, and makes them interchangeable. Laravel uses this for handling different types of questions or operations.

### Construction Analogy
Like having different specialized tools for different jobs: a hammer for nails, a drill for holes, a saw for cutting. Each tool (strategy) does one job perfectly, and you pick the right tool (strategy) based on what you need to accomplish.

### What This Means in Practice
- Different algorithms/approaches are encapsulated in separate classes
- A context class chooses the appropriate strategy at runtime
- New strategies can be added without changing existing code
- Each strategy follows the same interface

### Why Should You Care?
Strategy pattern eliminates complex if/else chains and makes adding new behaviors easy. Your code becomes more flexible and follows the Open/Closed Principle.

### The Sanity Check
If you have a long switch statement or many if/else blocks handling different types of similar operations, you need the Strategy pattern.

### Bad Example (Proposed improvement - showing what NOT to do)
```php
// Hypothetical bad approach with complex conditionals
class QuestionHandler
{
    public function handleQuestion(Question $question, array $data)
    {
        if ($question->type === 'file_upload') {
            // File upload logic here
            $files = $data['files'];
            foreach ($files as $file) {
                // Store file logic
            }
        } elseif ($question->type === 'text_answer') {
            // Text answer logic here
            $answer = $data['answer'];
            // Store text logic
        } elseif ($question->type === 'multiple_choice') {
            // Multiple choice logic here
            $choice = $data['choice'];
            // Validate choice logic
        }
        // Adding new question types means modifying this method
    }
}
```

### Good Example (From Our Better Implementation)
```php
// From: better/app/Contracts/AnswerAction.php (Strategy Interface)
interface AnswerAction
{
    public function canHandle(Question $question): bool;
    public function handle(Question $question, array $data): void;
}

// From: better/app/Actions/Answer/UploadAction.php (Concrete Strategy)
class UploadAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        return $question->type === 'file_upload';
    }

    public function handle(Question $question, array $data): void
    {
        $given_answer = $this->answerQuestion($question->id, true);
        $storedFiles = $this->storeFiles($data);
        $given_answer->files()->createMany($storedFiles);
    }
}

// From: better/app/Services/AnswerService.php (Context Class)
class AnswerService
{
    public function __construct(private Collection $handlers) {}

    public function answerQuestion(Question $question, array $data): void
    {
        // Context selects the appropriate strategy
        $this->handlers->first(fn($h) => $h->canHandle($question))
            ->handle($question, $data);
    }
}
```

---

# 🌿 INTERMEDIATE PATTERNS  

*These patterns are Laravel-specific and assume you understand MVC and Services. They show how Laravel makes complex patterns simple.*

## Pattern 4: Dependency Injection Container

### The Basic Idea
The Dependency Injection pattern provides dependencies to a class rather than having the class create them itself. Laravel's Service Container automates this process.

### Construction Analogy
Like a construction crew where the foreman (Container) makes sure each worker has the right tools delivered to them, rather than each worker having to find and bring their own tools. Workers focus on their job, not on tool management.

### What This Means in Practice
- Classes declare what they need in their constructor
- The container automatically provides those dependencies
- Dependencies can be easily swapped for testing
- Reduces coupling between classes

### Why Should You Care?
Dependency injection makes your code more testable, flexible, and follows SOLID principles. You can easily mock dependencies for testing or swap implementations.

### The Sanity Check
If your classes use `new` to create their dependencies or call static methods extensively, you're not using dependency injection properly.

### Bad Example (From Our Bad Implementation)
```php
// From bad example - creating dependencies manually (actual code)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Hard-coded dependency creation
        $user = auth()->user(); // Using global helper instead of injection
        
        // Direct database access instead of injected dependencies
        $application = $user->application;
        if ($application->id != $request->applicationId) {
            $application = $application->where('id', $request->applicationId)->first();
        }
        
        // No service injection - doing work directly
        $totalUploadedSize = array_reduce($request->file('files'), 
            fn(int $carry, $file) => $carry += $file->getSize(), 0);
    }
}
```

### Good Example (From Our Better Implementation)
```php
// From: better/app/Http/Controllers/FileController.php (Proper Dependency Injection)
class FileController extends Controller
{
    public function store(
        StoreFileRequest $request, 
        #[CurrentUser] User $user,           // Injected user dependency
        AnswerService $answerService         // Injected service dependency
    ) {
        $validatedData = $request->validated();

        // Using injected dependencies instead of creating them
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );

        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}

// From: better/app/Services/AnswerService.php (Constructor Injection)
class AnswerService
{
    public function __construct(private Collection $handlers) {}
    
    // Dependencies injected through constructor, not created inside
}

// From: better/app/Providers/AppServiceProvider.php (Container Configuration)
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Container automatically provides dependencies
        $this->app->singleton(AnswerService::class, function ($app) {
            return new AnswerService(
                collect(
                    array_map(fn($handler) => $app->make($handler), config('answer.handlers', []))
                )
            );
        });
    }
}
```

## Pattern 5: Repository Pattern (Proposed Laravel Enhancement)

### The Basic Idea
The Repository pattern encapsulates data access logic and provides a more object-oriented view of the persistence layer.

### Construction Analogy
Like having a dedicated warehouse manager who knows exactly where every material is stored and how to get it. Instead of construction workers wandering around the warehouse looking for supplies, they ask the warehouse manager (Repository) who handles all the storage and retrieval logistics.

### What This Means in Practice
- Data access logic is centralized in repository classes
- Controllers and services work with repositories instead of models directly
- Database queries are abstracted behind meaningful method names
- Testing becomes easier with fake repositories

### Why Should You Care?
Repository pattern makes your data layer more testable, swappable, and keeps complex queries out of your business logic.

### The Sanity Check
If your controllers or services contain complex Eloquent queries, you might benefit from repositories.

### Laravel's Approach (Actual Implementation)
```php
// Laravel typically uses Eloquent models directly (actual code from our better example)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        // Direct model usage - Laravel's preferred approach
        $question = Question::find($validatedData['questionId']);
        $answerService->answerQuestion($question, $request->file('files'));
    }
}
```

### Repository Pattern Enhancement (Proposed improvement)
```php
// Proposed: UserRepository interface
interface UserRepositoryInterface
{
    public function findWithUploadStats(int $userId): User;
    public function updateUploadTotal(int $userId, FileSize $additionalSize): void;
    public function getUsersExceedingUploadLimit(): Collection;
}

// Proposed: Eloquent implementation
class EloquentUserRepository implements UserRepositoryInterface
{
    public function findWithUploadStats(int $userId): User
    {
        return User::with('files')->findOrFail($userId);
    }
    
    public function updateUploadTotal(int $userId, FileSize $additionalSize): void
    {
        $user = User::findOrFail($userId);
        $user->updateUploadSizeTotal($additionalSize);
    }
    
    public function getUsersExceedingUploadLimit(): Collection
    {
        return User::whereRaw('total_upload_size > upload_limit')->get();
    }
}

// Proposed: Controller using Repository
class FileController extends Controller
{
    public function store(
        StoreFileRequest $request, 
        UserRepositoryInterface $userRepository,
        AnswerService $answerService
    ) {
        $user = $userRepository->findWithUploadStats(auth()->id());
        // Repository handles complex data operations
    }
}
```

## Pattern 6: Facade Pattern

### The Basic Idea
The Facade pattern provides a simplified interface to a complex subsystem. Laravel's Facades provide static-like interfaces to services in the container.

### Construction Analogy
Like a general contractor who provides a single point of contact for homeowners. Instead of the homeowner coordinating with electricians, plumbers, carpenters separately, they work through the general contractor (Facade) who manages all the specialized workers behind the scenes.

### What This Means in Practice
- Facades provide clean, memorable interfaces to Laravel services
- Complex service instantiation is hidden behind simple static calls
- Facades are actually resolved through the service container
- They're testable through Laravel's facade mocking system

### Why Should You Care?
Facades make Laravel services easy to use while maintaining all the benefits of dependency injection under the hood.

### The Sanity Check
If you're writing verbose service container calls instead of using Laravel's facades, you're making things harder than they need to be.

### Common Laravel Facades (Actual Framework Features)
```php
// From Laravel framework - common facade usage
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Facade provides clean interface to authorization system
        Gate::authorize('upload-files', $request->application());
        
        // Facade provides clean interface to filesystem
        Storage::disk('local')->put($path, $fileContent);
        
        // Facade provides clean interface to mail system
        Mail::to($user)->send(new UploadNotification());
    }
}
```

### Behind the Scenes (How Facades Work)
```php
// What Laravel does internally (framework code concept)
class Gate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gate'; // Resolves from service container
    }
}

// When you call Gate::authorize(), Laravel actually does:
app('gate')->authorize();
```

---

# 🌳 ADVANCED PATTERNS

*These patterns show Laravel's framework magic. Don't worry if they seem complex - you'll understand them after mastering the basics.*

## Pattern 7: Observer Pattern

### The Basic Idea
The Observer pattern defines a one-to-many dependency between objects so that when one object changes state, all dependents are notified automatically.

### Construction Analogy
Like having a construction site alarm system. When the fire alarm (Subject) goes off, all workers (Observers) automatically know to evacuate, the sprinkler system activates, and emergency services are notified. The alarm doesn't need to know about each specific response - it just signals, and everyone reacts appropriately.

### What This Means in Practice
- Events are fired when important things happen
- Multiple listeners can respond to the same event
- New functionality can be added without changing existing code
- Loose coupling between the event source and handlers

### Why Should You Care?
Observer pattern makes your application extensible and keeps concerns separated. You can add new features without modifying existing code.

### Laravel's Implementation (Actual Framework Features)
```php
// From Laravel framework - Event and Listener pattern (proposed improvement)
use App\Events\FileUploaded;
use App\Listeners\UpdateUploadStatistics;
use App\Listeners\NotifyAdminOfLargeUpload;
use App\Listeners\ScanFileForViruses;

// Event class (proposed)
class FileUploaded
{
    public function __construct(
        public User $user,
        public Collection $files,
        public FileSize $totalSize
    ) {}
}

// In your controller (proposed improvement)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user)
    {
        // Store files...
        $files = $this->storeFiles($request->file('files'));
        
        // Fire event - observers will handle the rest
        event(new FileUploaded($user, $files, FileSize::fromFiles($files)));
    }
}

// Multiple listeners can respond (proposed)
class UpdateUploadStatistics
{
    public function handle(FileUploaded $event)
    {
        $event->user->updateUploadSizeTotal($event->totalSize);
    }
}

class NotifyAdminOfLargeUpload
{
    public function handle(FileUploaded $event)
    {
        if ($event->totalSize->exceedsLimit(FileSize::fromMegabytes(100))) {
            Mail::to('admin@example.com')->send(new LargeUploadAlert($event));
        }
    }
}
```

## Pattern 8: Singleton Pattern

### The Basic Idea
The Singleton pattern ensures a class has only one instance and provides global access to that instance. Laravel's service container can manage singletons automatically.

### Construction Analogy
Like having one project manager on a construction site. You don't want multiple project managers giving conflicting instructions - there should be exactly one person coordinating the entire project, and everyone knows how to reach them.

### What This Means in Practice
- Some services should have only one instance per request
- Expensive-to-create objects can be reused
- Global state can be managed safely
- Laravel's container handles singleton lifecycle

### Why Should You Care?
Singletons prevent duplicate resource usage and ensure consistent state across your application.

### The Sanity Check
If you're manually implementing singleton logic with static properties, you're doing it wrong - let Laravel's container handle it.

### Laravel's Container Singleton (From Our Better Implementation)
```php
// From: better/app/Providers/AppServiceProvider.php (Actual Code)
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Laravel container manages singleton lifecycle
        $this->app->singleton(AnswerService::class, function ($app) {
            return new AnswerService(
                collect(
                    array_map(fn($handler) => $app->make($handler), config('answer.handlers', []))
                )
            );
        });
    }
}

// Now AnswerService is created once per request and reused everywhere
class FileController extends Controller
{
    public function store(StoreFileRequest $request, AnswerService $answerService)
    {
        // Same AnswerService instance used throughout request
        $answerService->answerQuestion($question, $files);
    }
}
```

### Manual Singleton (Proposed improvement - what NOT to do)
```php
// Don't do this - let Laravel's container handle it
class AnswerService
{
    private static $instance = null;
    
    private function __construct() {} // Private constructor
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

## Pattern 9: Composition over Inheritance

### The Basic Idea
Favor object composition (has-a relationships) over class inheritance (is-a relationships) to achieve code reuse and flexibility.

### Construction Analogy
Like modular construction where you combine prefabricated components rather than custom-building everything. A house *has* plumbing, electrical, and HVAC systems rather than *being* a subclass of "ElectricalBuilding" that inherits electrical functionality.

### What This Means in Practice
- Use dependency injection to compose behavior
- Prefer interfaces and traits over deep inheritance hierarchies
- Build complex objects from simpler components
- Focus on what objects *do* together, not what they *are*

### Why Should You Care?
Composition is more flexible than inheritance and avoids the fragile base class problem. Changes to one component don't break others.

### The Sanity Check
If your inheritance hierarchy is more than 3 levels deep, or you're using abstract classes just for code sharing, consider composition instead.

### Bad Example (Proposed improvement - showing what NOT to do)
```php
// Deep inheritance hierarchy - fragile and hard to extend
abstract class BaseController extends Controller
{
    protected function validateUpload($files) { /* logic */ }
    protected function storeFiles($files) { /* logic */ }
}

abstract class FileHandlerController extends BaseController
{
    protected function processUpload($files) { /* logic */ }
    protected function notifyUser($message) { /* logic */ }
}

class DocumentController extends FileHandlerController
{
    // Inherits everything - hard to customize individual behaviors
}

class ImageController extends FileHandlerController
{
    // What if images need different validation but same storage?
}
```

### Good Example (From Our Better Implementation)
```php
// From: better/app/Http/Controllers/FileController.php (Composition)
class FileController extends Controller
{
    // Controller composes behavior from injected services
    public function store(
        StoreFileRequest $request,        // Validation behavior
        #[CurrentUser] User $user,        // User behavior
        AnswerService $answerService      // Business logic behavior
    ) {
        // Compose different services to handle the request
        $validatedData = $request->validated();
        $answerService->answerQuestion(Question::find($validatedData['questionId']), $request->file('files'));
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}

// From: better/app/Actions/Answer/UploadAction.php (Composed from base)
class UploadAction extends AnswerAction // Minimal inheritance
{
    // Composes behavior through dependency injection and method calls
    public function handle(Question $question, array $data): void
    {
        $given_answer = $this->answerQuestion($question->id, true);  // Inherited method
        $storedFiles = $this->storeFiles($data);                     // Composed method
        $given_answer->files()->createMany($storedFiles);            // Model relationship
    }
}

// Different file types can compose different behaviors without inheritance
class ImageUploadService
{
    public function __construct(
        private FileValidator $validator,      // Compose validation
        private ImageProcessor $processor,     // Compose image processing
        private FileStorage $storage          // Compose storage
    ) {}
}
```

## Pattern 10: Template Method Pattern

### The Basic Idea
The Template Method pattern defines the skeleton of an algorithm in a base class, letting subclasses override specific steps without changing the algorithm's structure.

### Construction Analogy
Like having a standardized building inspection process: every inspection follows the same steps (check foundation, check electrical, check plumbing, issue certificate), but the specific details of each check vary based on the type of building. The inspector (Template Method) follows the same procedure, but delegates specific checks to specialized inspectors.

### What This Means in Practice
- A base class defines the overall algorithm structure
- Subclasses implement specific steps through method overrides
- The sequence of operations is controlled by the base class
- Common behavior is shared while allowing customization

### Why Should You Care?
Template Method promotes code reuse and ensures consistent processes while allowing flexibility where needed. Laravel uses this pattern extensively.

### The Sanity Check
If you have multiple classes following the same general process but with different specific steps, you're looking at a Template Method opportunity.

### Laravel's Form Request (From Our Better Implementation)
```php
// From: better/app/Http/Requests/StoreFileRequest.php (Template Method in action)
class StoreFileRequest extends FormRequest
{
    // Template method defined in parent FormRequest class:
    // 1. Check authorization (calls authorize())
    // 2. Validate request (calls rules())  
    // 3. Handle validation failure (can override failedValidation())
    // 4. Return validated data
    
    public function authorize(): bool
    {
        // Step 1: Custom authorization logic
        return $this->user()->can('update', [$this->application, $this->user()->organisation_id]);
    }

    public function rules(): array
    {
        // Step 2: Custom validation rules
        return [
            'files' => ['required', 'array'],
            'files.*' => ['mimes:pdf', 'min:1', 'max:5120'],
            'questionId' => ['required', 'integer', 'exists:questions,id'],
            'applicationId' => ['required', 'integer'],
            'total_file_size' => ['required', 'integer', new UploadLimit],
        ];
    }
    
    // Step 3: Can optionally override error handling
    protected function failedValidation(Validator $validator)
    {
        // Custom validation failure logic if needed
        parent::failedValidation($validator);
    }
}

// Laravel's FormRequest base class (framework code concept)
abstract class FormRequest extends Request
{
    // Template method - defines the algorithm structure
    public function validateResolved()
    {
        // Step 1: Authorization check (calls your authorize() method)
        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }
        
        // Step 2: Validation (calls your rules() method)
        $instance = $this->getValidatorInstance();
        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }
    
    // Abstract methods that subclasses must implement
    abstract public function authorize(): bool;
    abstract public function rules(): array;
    
    // Hook methods that subclasses can override
    protected function failedValidation(Validator $validator) { /* default behavior */ }
    protected function failedAuthorization() { /* default behavior */ }
}
```

### Other Laravel Template Method Examples (Framework Features)
```php
// Middleware pipeline uses Template Method
abstract class Middleware
{
    // Template method
    public function handle($request, Closure $next)
    {
        // Before processing (can be overridden)
        $this->before($request);
        
        // Core processing
        $response = $next($request);
        
        // After processing (can be overridden)
        $this->after($request, $response);
        
        return $response;
    }
    
    protected function before($request) { /* override in subclass */ }
    protected function after($request, $response) { /* override in subclass */ }
}

// Artisan Commands use Template Method
abstract class Command
{
    // Template method defines command execution structure
    public function run(InputInterface $input, OutputInterface $output)
    {
        // 1. Setup
        $this->initialize($input, $output);
        
        // 2. Execute (your custom logic)
        return $this->execute($input, $output);
        
        // 3. Cleanup happens automatically
    }
    
    abstract protected function execute(InputInterface $input, OutputInterface $output);
}
```

## Pattern 11: Value Object Pattern

### The Basic Idea
Value Objects represent descriptive aspects of the domain with no conceptual identity. They're immutable and defined by their attributes rather than identity.

### Construction Analogy
Like measurements and specifications in construction: a "2x4 lumber piece" or "3/4 inch bolt" are defined by their properties, not by which specific piece you have. Two 2x4s with the same dimensions are equivalent - you don't care which physical piece you get.

### What This Means in Practice
- Encapsulate primitive values with business meaning
- Make objects immutable for safety
- Provide meaningful operations and comparisons
- Replace primitive obsession with domain concepts

### Why Should You Care?
Value Objects make your code more expressive, prevent bugs through type safety, and encapsulate business rules in the right places.

### The Sanity Check
If you're passing around raw integers, strings, or arrays that represent domain concepts (money, file sizes, coordinates), you need Value Objects.

### Bad Example (From Our Bad Implementation)
```php
// Primitive obsession - raw integers everywhere (actual code from bad example)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Raw integer - no meaning or validation
        $totalUploadedSize = array_reduce($request->file('files'), 
            fn(int $carry, $file) => $carry += $file->getSize(), 0);
            
        // Business logic with raw integers - error prone
        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages(['max_upload_size' => 'Upload limit exceeded']);
        }
        
        // Unclear what this integer represents
        $user->updateUploadSizeTotal($totalUploadedSize);
    }
}
```

### Good Example (From Our Better Implementation)
```php
// From: better/app/ValueObjects/FileSize.php (Value Object)
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

    public static function fromFiles(array $files): self
    {
        $totalSize = array_reduce($files, fn(int $carry, $file) => $carry + $file->getSize(), 0);
        return new self($totalSize);
    }

    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }

    public function add(FileSize $other): self
    {
        return new self($this->bytes + $other->bytes);
    }
    
    public function isWithinLimit(FileSize $limit): bool
    {
        return $this->bytes <= $limit->bytes;
    }
}

// From: better/app/Http/Controllers/FileController.php (Using Value Objects)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        // Clear, type-safe value object instead of raw integer
        $totalFileSize = FileSize::fromFiles($request->file('files'));
        
        // Business logic encapsulated in value object
        $answerService->answerQuestion(Question::find($validatedData['questionId']), $request->file('files'));
        
        // Type-safe method call with meaningful parameter
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}
```

## Pattern 12: Builder Pattern

### The Basic Idea
The Builder pattern constructs complex objects step by step, allowing you to create different representations of an object using the same construction process.

### Construction Analogy
Like ordering a custom house where you work with an architect to specify each component: "I want a 3-bedroom layout, with hardwood floors, granite countertops, and a 2-car garage." The architect (Builder) guides you through each decision step-by-step, and at the end, you get exactly the house you specified. Different customers can make different choices but follow the same building process.

### What This Means in Practice
- Complex objects are built through a series of method calls
- Each step adds or configures part of the final object
- The same builder can create different variations
- The construction process is separated from the final representation

### Why Should You Care?
Builder pattern makes creating complex objects intuitive and readable. Instead of constructor methods with dozens of parameters, you get fluent, self-documenting code.

### The Sanity Check
If you're creating objects with many optional parameters or complex configuration, or if you have "telescoping constructors," you need the Builder pattern.

### Laravel's Query Builder (Framework Feature)
```php
// Laravel's Eloquent Query Builder - most common Builder pattern usage
class FileController extends Controller
{
    public function index(Request $request)
    {
        // Building a complex query step by step
        $files = File::query()
            ->where('user_id', auth()->id())                    // Step 1: Filter by user
            ->where('created_at', '>=', now()->subDays(30))     // Step 2: Last 30 days
            ->whereHas('application', function ($query) {        // Step 3: With application
                $query->where('status', 'active');
            })
            ->with(['user', 'application'])                      // Step 4: Eager load relationships
            ->orderBy('created_at', 'desc')                     // Step 5: Sort by newest
            ->paginate(15);                                     // Step 6: Paginate results

        return response()->json($files);
    }
    
    public function complexSearch(Request $request)
    {
        // Same builder, different configuration
        $query = File::query();
        
        // Conditionally build the query
        if ($request->has('filename')) {
            $query->where('filename', 'like', "%{$request->filename}%");
        }
        
        if ($request->has('extension')) {
            $query->where('extension', $request->extension);
        }
        
        if ($request->has('size_min')) {
            $query->where('size', '>=', $request->size_min);
        }
        
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        return $query->get();
    }
}
```

### Laravel's HTTP Client Builder (Framework Feature)
```php
// Laravel HTTP Client also uses Builder pattern
use Illuminate\Support\Facades\Http;

class ExternalApiService
{
    public function uploadFileToExternalService(File $file)
    {
        // Building HTTP request step by step
        $response = Http::timeout(30)                           // Step 1: Set timeout
            ->withHeaders([                                     // Step 2: Add headers
                'Authorization' => 'Bearer ' . config('api.token'),
                'Accept' => 'application/json',
            ])
            ->attach('file', $file->getContent(), $file->filename)  // Step 3: Attach file
            ->withOptions(['verify' => false])                  // Step 4: SSL options
            ->retry(3, 1000)                                   // Step 5: Retry logic
            ->post('https://api.example.com/upload');          // Step 6: Execute request
            
        return $response->successful();
    }
    
    public function simpleApiCall()
    {
        // Same builder, simpler configuration
        return Http::get('https://api.example.com/status')->json();
    }
}
```

### Custom Builder Example (Proposed Enhancement)
```php
// Custom builder for complex file operations (proposed improvement)
class FileOperationBuilder
{
    private array $files = [];
    private array $validations = [];
    private ?string $destination = null;
    private bool $overwrite = false;
    private ?Closure $progressCallback = null;
    
    public function addFile(string $path, ?string $newName = null): self
    {
        $this->files[] = ['path' => $path, 'name' => $newName];
        return $this;
    }
    
    public function addFiles(array $paths): self
    {
        foreach ($paths as $path) {
            $this->addFile($path);
        }
        return $this;
    }
    
    public function validateExtension(array $allowedExtensions): self
    {
        $this->validations[] = new ExtensionValidator($allowedExtensions);
        return $this;
    }
    
    public function validateSize(int $maxSizeInBytes): self
    {
        $this->validations[] = new SizeValidator($maxSizeInBytes);
        return $this;
    }
    
    public function toDestination(string $path): self
    {
        $this->destination = $path;
        return $this;
    }
    
    public function overwriteExisting(bool $overwrite = true): self
    {
        $this->overwrite = $overwrite;
        return $this;
    }
    
    public function onProgress(Closure $callback): self
    {
        $this->progressCallback = $callback;
        return $this;
    }
    
    public function execute(): FileOperationResult
    {
        // Build and execute the complex file operation
        $operation = new FileOperation(
            $this->files,
            $this->validations,
            $this->destination,
            $this->overwrite,
            $this->progressCallback
        );
        
        return $operation->execute();
    }
}

// Usage of custom builder (proposed improvement)
class FileController extends Controller
{
    public function batchProcess(Request $request)
    {
        $result = (new FileOperationBuilder())
            ->addFiles($request->file('files'))
            ->validateExtension(['pdf', 'docx', 'txt'])
            ->validateSize(5 * 1024 * 1024) // 5MB
            ->toDestination(storage_path('processed'))
            ->overwriteExisting(false)
            ->onProgress(function ($progress) {
                // Broadcast progress to user
                broadcast(new FileProcessingProgress($progress));
            })
            ->execute();
            
        return response()->json(['success' => $result->isSuccessful()]);
    }
}
```

### Bad Example (Without Builder Pattern)
```php
// Complex constructor with many parameters (proposed improvement - what NOT to do)
class FileOperation
{
    public function __construct(
        array $files,
        ?array $allowedExtensions = null,
        ?int $maxSize = null,
        ?string $destination = null,
        bool $overwrite = false,
        bool $validateMime = true,
        ?Closure $progressCallback = null,
        ?Closure $errorCallback = null,
        int $retryCount = 3,
        int $timeout = 30
    ) {
        // Constructor with too many parameters - hard to remember order
    }
}

// Difficult to use and remember parameter order
$operation = new FileOperation(
    $files,
    ['pdf', 'docx'],
    5242880,
    '/storage/files',
    false,
    true,
    null,
    null,
    3,
    30
); // Which parameter is which? Hard to tell!
```

## Pattern 13: Chain of Responsibility Pattern

### The Basic Idea
The Chain of Responsibility pattern passes requests along a chain of handlers. Each handler decides either to process the request or pass it to the next handler in the chain.

### Construction Analogy
Like a building permit approval process: your application goes through multiple departments (Planning, Fire Safety, Structural Engineering, Environmental) in sequence. Each department either approves and passes it to the next department, or rejects it and stops the process. The application doesn't need to know which departments exist - it just enters the chain and gets processed by whoever can handle each aspect.

### What This Means in Practice
- Multiple handlers are chained together in a specific order
- Each handler can process the request, modify it, or pass it along
- Handlers can stop the chain by not calling the next handler
- New handlers can be added without changing existing code

### Why Should You Care?
Chain of Responsibility decouples the sender of a request from its receivers, making your code more flexible and allowing dynamic handler composition.

### The Sanity Check
If you have a series of conditional checks or processing steps that need to happen in sequence, where each step might handle the request differently, you're looking at a Chain of Responsibility scenario.

### Laravel's Middleware Pipeline (Framework Feature)
```php
// Laravel's middleware system is pure Chain of Responsibility
// From: Laravel framework (typical middleware configuration)

// Global middleware chain - every request goes through these
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,           // Handler 1: Trust proxies
    \App\Http\Middleware\HandleCors::class,             // Handler 2: CORS handling
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Handler 3: Maintenance mode
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Handler 4: POST size validation
    \App\Http\Middleware\TrimStrings::class,            // Handler 5: Trim input
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Handler 6: Convert empty strings
];

// Route-specific middleware chains
Route::middleware(['auth', 'verified', 'throttle:60,1'])->group(function () {
    // Handler 1: Authentication
    // Handler 2: Email verification  
    // Handler 3: Rate limiting
    // Then: Your controller
});
```

### Custom Middleware Example (Proposed Enhancement)
```php
// Each middleware is a handler in the chain (proposed improvement)
class FileUploadSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // This handler focuses on file security
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$this->isSecureFile($file)) {
                    abort(422, 'Unsafe file detected');
                }
            }
        }
        
        // Pass to next handler in chain
        return $next($request);
    }
    
    private function isSecureFile($file): bool
    {
        // Security validation logic
        return !in_array($file->getClientOriginalExtension(), ['exe', 'bat', 'cmd']);
    }
}

class FileUploadQuotaMiddleware  
{
    public function handle(Request $request, Closure $next)
    {
        // This handler focuses on upload quotas
        if ($request->hasFile('files')) {
            $totalSize = collect($request->file('files'))->sum(fn($file) => $file->getSize());
            
            if (!auth()->user()->canUpload($totalSize)) {
                abort(422, 'Upload quota exceeded');
            }
        }
        
        // Pass to next handler in chain
        return $next($request);
    }
}

class AuditUploadMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // This handler focuses on auditing
        $response = $next($request); // Process request first
        
        // Log after successful processing
        if ($request->hasFile('files') && $response->isSuccessful()) {
            Log::info('File upload completed', [
                'user_id' => auth()->id(),
                'file_count' => count($request->file('files')),
                'total_size' => collect($request->file('files'))->sum(fn($file) => $file->getSize())
            ]);
        }
        
        return $response;
    }
}

// Chain them together in routes (proposed improvement)
Route::post('/files', [FileController::class, 'store'])
    ->middleware(['auth', FileUploadSecurityMiddleware::class, FileUploadQuotaMiddleware::class, AuditUploadMiddleware::class]);
```

### How Laravel's Chain Works Behind the Scenes
```php
// Laravel's Pipeline class implements Chain of Responsibility (framework concept)
class Pipeline
{
    protected $passable;
    protected $pipes = [];
    
    public function send($passable)
    {
        $this->passable = $passable;
        return $this;
    }
    
    public function through($pipes)
    {
        $this->pipes = $pipes;
        return $this;
    }
    
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );
        
        return $pipeline($this->passable);
    }
    
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                // Each pipe is a handler in the chain
                return $pipe->handle($passable, $stack);
            };
        };
    }
}

// Usage in Laravel's request handling (framework code concept)
$response = (new Pipeline())
    ->send($request)
    ->through($middlewareChain)
    ->then(function ($request) {
        // Final destination: your controller
        return $this->controller->handle($request);
    });
```

### Bad Example (Without Chain of Responsibility)
```php
// Monolithic request handling - all logic in one place (proposed improvement - what NOT to do)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // All validation and processing logic jumbled together
        
        // Security check
        foreach ($request->file('files') as $file) {
            if (in_array($file->getClientOriginalExtension(), ['exe', 'bat'])) {
                abort(422, 'Unsafe file');
            }
        }
        
        // Quota check  
        $totalSize = collect($request->file('files'))->sum(fn($file) => $file->getSize());
        if (!auth()->user()->canUpload($totalSize)) {
            abort(422, 'Quota exceeded');
        }
        
        // Rate limiting check
        if (Cache::get('upload_count_' . auth()->id(), 0) > 10) {
            abort(429, 'Too many uploads');
        }
        
        // Audit logging
        Log::info('Upload started', ['user' => auth()->id()]);
        
        // Business logic
        // ... file processing
        
        // More audit logging
        Log::info('Upload completed', ['user' => auth()->id()]);
        
        // All concerns mixed together - hard to test, modify, or reuse
    }
}
```

## Pattern 14: Command Pattern

### The Basic Idea
The Command pattern encapsulates a request as an object, allowing you to parameterize clients with different requests, queue operations, and support undo operations.

### Construction Analogy
Like work orders on a construction site: instead of the foreman directly telling workers what to do, they write detailed work orders (Commands) that specify exactly what needs to be done, who should do it, and when. These work orders can be scheduled, queued, delegated to different supervisors, or even cancelled if needed. The work order contains all the information needed to complete the task independently.

### What This Means in Practice
- Actions are encapsulated as objects with all necessary data
- Commands can be queued, logged, or executed later
- Complex operations can be undone or redone
- Request processing is decoupled from request execution

### Why Should You Care?
Command pattern makes your application more flexible by separating what needs to be done from when and how it's done. It enables powerful features like job queues, audit logs, and undo functionality.

### The Sanity Check
If you have operations that need to be queued, logged, undone, or executed at different times, you're looking at Command pattern opportunities.

### Laravel's Artisan Commands (Framework Feature)
```php
// Laravel's Artisan system is built on Command pattern (actual framework feature)
// From: Laravel framework (typical Artisan command)

class ProcessUploadsCommand extends Command
{
    protected $signature = 'uploads:process {--batch-size=100}';
    protected $description = 'Process pending file uploads';

    public function handle()
    {
        // Command encapsulates all the logic needed to process uploads
        $batchSize = $this->option('batch-size');
        
        $pendingUploads = PendingUpload::limit($batchSize)->get();
        
        foreach ($pendingUploads as $upload) {
            $this->processUpload($upload);
        }
        
        $this->info("Processed {$pendingUploads->count()} uploads");
    }
    
    private function processUpload(PendingUpload $upload): void
    {
        // All processing logic encapsulated in the command
        $upload->process();
        $upload->markAsProcessed();
    }
}

// Usage: php artisan uploads:process --batch-size=50
```

### Laravel's Job System (Framework Feature)
```php
// Laravel Jobs are Command pattern implementations (actual framework feature)
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessFileUpload implements ShouldQueue
{
    private User $user;
    private array $files;
    private int $questionId;

    public function __construct(User $user, array $files, int $questionId)
    {
        // Command stores all data needed for execution
        $this->user = $user;
        $this->files = $files;
        $this->questionId = $questionId;
    }

    public function handle(AnswerService $answerService)
    {
        // Command executes the operation when called
        $question = Question::find($this->questionId);
        $answerService->answerQuestion($question, $this->files);
        
        $totalSize = FileSize::fromFiles($this->files);
        $this->user->updateUploadSizeTotal($totalSize);
        
        // Log successful processing
        Log::info('File upload processed via queue', [
            'user_id' => $this->user->id,
            'question_id' => $this->questionId,
            'file_count' => count($this->files)
        ]);
    }
    
    public function failed(\Throwable $exception)
    {
        // Handle command failure
        Log::error('File upload processing failed', [
            'user_id' => $this->user->id,
            'question_id' => $this->questionId,
            'error' => $exception->getMessage()
        ]);
    }
}

// Usage in controller (proposed improvement)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user)
    {
        // Instead of processing immediately, queue a command
        ProcessFileUpload::dispatch($user, $request->file('files'), $request->questionId);
        
        return response()->json(['message' => 'Upload queued for processing']);
    }
}
```

### Our Better Implementation Uses Command Pattern
```php
// From: better/app/Actions/Answer/UploadAction.php (Actual Code - Command Pattern)
class UploadAction extends AnswerAction
{
    // This is actually a Command pattern implementation!
    // It encapsulates the "upload files for question" operation
    
    public function canHandle(Question $question): bool
    {
        return $question->type === 'file_upload';
    }

    public function handle(Question $question, array $data): void
    {
        // Command encapsulates all upload logic
        $given_answer = $this->answerQuestion($question->id, true);
        $storedFiles = $this->storeFiles($data);
        $given_answer->files()->createMany($storedFiles);
    }
    
    private function storeFiles(array $files): array
    {
        return array_map(function ($file): array {
            $filePath = '/' . $file->store('uploaded_files/' . $this->application->id, 'local');

            return [
                'filename' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'path' => $filePath,
                'uuid' => Str::uuid(),
                'user_id' => $this->user->id
            ];
        }, $files);
    }
}

// From: better/app/Services/AnswerService.php (Command Invoker)
class AnswerService
{
    public function answerQuestion(Question $question, array $data): void
    {
        // Service acts as command invoker - chooses and executes command
        $command = $this->handlers->first(fn($h) => $h->canHandle($question));
        $command->handle($question, $data);
    }
}
```

### Advanced Command Pattern Example (Proposed Enhancement)
```php
// Complex command with undo capability (proposed improvement)
interface UndoableCommand
{
    public function execute(): void;
    public function undo(): void;
    public function canUndo(): bool;
}

class BulkFileOperationCommand implements UndoableCommand
{
    private array $operations = [];
    private array $executedOperations = [];
    
    public function __construct(
        private User $user,
        private array $fileIds,
        private string $operation, // 'delete', 'move', 'archive'
        private ?string $destination = null
    ) {}
    
    public function execute(): void
    {
        foreach ($this->fileIds as $fileId) {
            $file = File::find($fileId);
            
            $operation = match($this->operation) {
                'delete' => new DeleteFileOperation($file),
                'move' => new MoveFileOperation($file, $this->destination),
                'archive' => new ArchiveFileOperation($file),
                default => throw new InvalidArgumentException('Unknown operation')
            };
            
            // Store for potential undo
            $this->operations[] = $operation;
            
            // Execute and track
            $result = $operation->execute();
            $this->executedOperations[] = $result;
        }
        
        // Log the bulk operation
        Log::info('Bulk file operation completed', [
            'user_id' => $this->user->id,
            'operation' => $this->operation,
            'file_count' => count($this->fileIds),
            'command_id' => $this->getCommandId()
        ]);
    }
    
    public function undo(): void
    {
        if (!$this->canUndo()) {
            throw new RuntimeException('Cannot undo this operation');
        }
        
        // Undo in reverse order
        foreach (array_reverse($this->operations) as $operation) {
            if ($operation instanceof UndoableCommand) {
                $operation->undo();
            }
        }
        
        Log::info('Bulk file operation undone', [
            'user_id' => $this->user->id,
            'operation' => $this->operation,
            'command_id' => $this->getCommandId()
        ]);
    }
    
    public function canUndo(): bool
    {
        return !empty($this->executedOperations) && 
               collect($this->operations)->every(fn($op) => $op instanceof UndoableCommand);
    }
    
    private function getCommandId(): string
    {
        return md5($this->user->id . $this->operation . serialize($this->fileIds));
    }
}

// Command Manager (proposed improvement)
class CommandManager
{
    private array $history = [];
    
    public function execute(UndoableCommand $command): void
    {
        $command->execute();
        
        if ($command->canUndo()) {
            $this->history[] = $command;
            
            // Keep only last 10 commands to prevent memory issues
            if (count($this->history) > 10) {
                array_shift($this->history);
            }
        }
    }
    
    public function undo(): bool
    {
        if (empty($this->history)) {
            return false;
        }
        
        $command = array_pop($this->history);
        $command->undo();
        
        return true;
    }
}

// Usage in controller (proposed improvement)
class FileController extends Controller
{
    public function bulkOperation(BulkFileOperationRequest $request, CommandManager $commandManager)
    {
        $command = new BulkFileOperationCommand(
            auth()->user(),
            $request->file_ids,
            $request->operation,
            $request->destination
        );
        
        $commandManager->execute($command);
        
        return response()->json([
            'message' => 'Bulk operation completed',
            'can_undo' => $command->canUndo()
        ]);
    }
    
    public function undoLastOperation(CommandManager $commandManager)
    {
        $success = $commandManager->undo();
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Operation undone' : 'Nothing to undo'
        ]);
    }
}
```

### Database Transaction Commands (Proposed Enhancement)
```php
// Command pattern for complex database operations (proposed improvement)
class CreateUserWithFilesCommand implements UndoableCommand
{
    private ?User $createdUser = null;
    private array $createdFiles = [];
    
    public function __construct(
        private array $userData,
        private array $filesData
    ) {}
    
    public function execute(): void
    {
        DB::transaction(function () {
            // Create user
            $this->createdUser = User::create($this->userData);
            
            // Create associated files
            foreach ($this->filesData as $fileData) {
                $file = $this->createdUser->files()->create($fileData);
                $this->createdFiles[] = $file;
            }
            
            // Send welcome email
            Mail::to($this->createdUser)->send(new WelcomeEmail());
        });
    }
    
    public function undo(): void
    {
        DB::transaction(function () {
            // Delete files first (foreign key constraints)
            foreach ($this->createdFiles as $file) {
                Storage::delete($file->path);
                $file->delete();
            }
            
            // Delete user
            if ($this->createdUser) {
                $this->createdUser->delete();
            }
        });
    }
    
    public function canUndo(): bool
    {
        return $this->createdUser !== null && $this->createdUser->exists;
    }
}
```

### Bad Example (Without Command Pattern)
```php
// Procedural approach without command encapsulation (proposed improvement - what NOT to do)
class FileController extends Controller
{
    public function processUpload(Request $request)
    {
        // All logic mixed in controller - no encapsulation
        $files = $request->file('files');
        
        // No way to queue this operation
        foreach ($files as $file) {
            $path = $file->store('uploads');
            
            // No way to undo this
            File::create([
                'path' => $path,
                'user_id' => auth()->id(),
                'size' => $file->getSize()
            ]);
        }
        
        // No way to replay or audit this operation
        // No way to handle failures gracefully
        // No separation between request and execution
    }
}
```

## Pattern 15: Factory Pattern

### The Basic Idea
The Factory pattern creates objects without specifying their exact classes, allowing the creation logic to be centralized and making the system more flexible when adding new types.

### Construction Analogy
Like a specialized manufacturing plant that produces different types of building materials. You tell the factory "I need concrete blocks" and it handles all the complexity of mixing cement, sand, and water in the right proportions. The factory knows how to make different types (standard blocks, insulated blocks, decorative blocks) but you don't need to know the manufacturing details.

### What This Means in Practice
- Object creation logic is centralized in factory classes or methods
- New types can be added without changing client code
- Complex object construction is hidden from clients
- Different implementations can be created based on configuration

### Why Should You Care?
Factory pattern makes your code more maintainable by centralizing object creation and makes it easier to swap implementations or add new types without breaking existing code.

### The Sanity Check
If you have complex object creation logic scattered throughout your code, or if you need to create different types of similar objects based on conditions, you need the Factory pattern.

### Laravel's Model Factories (Framework Feature)
```php
// Laravel's Eloquent Model Factories (actual framework feature)
// From: database/factories/UserFactory.php (typical Laravel factory)

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Factory encapsulates user creation logic
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    // Factory methods for different user types
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'permissions' => ['*'],
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    public function withFiles(): static
    {
        return $this->has(File::factory()->count(3));
    }
}

// Usage in tests (actual framework feature)
class UserTest extends TestCase
{
    public function test_user_creation()
    {
        // Factory creates user with appropriate defaults
        $user = User::factory()->create();
        
        // Factory can create specific types
        $admin = User::factory()->admin()->create();
        
        // Factory can create with relationships
        $userWithFiles = User::factory()->withFiles()->create();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin', $admin->role);
        $this->assertCount(3, $userWithFiles->files);
    }
}
```

### Service Container Factories (Framework Feature)
```php
// Laravel's Service Container uses Factory pattern (framework feature)
// From: app/Providers/AppServiceProvider.php

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Factory binding - creates new instance each time
        $this->app->bind(ReportGenerator::class, function ($app) {
            return new ReportGenerator(
                $app->make(DatabaseConnection::class),
                $app->make(PdfRenderer::class),
                config('reports.default_template')
            );
        });

        // Singleton factory - creates instance once
        $this->app->singleton(CacheManager::class, function ($app) {
            return new CacheManager($app['config']['cache']);
        });

        // Conditional factory - different implementations
        $this->app->bind(PaymentProcessor::class, function ($app) {
            $driver = config('payment.default');
            
            return match ($driver) {
                'stripe' => new StripePaymentProcessor(config('payment.stripe')),
                'paypal' => new PayPalPaymentProcessor(config('payment.paypal')),
                'mock' => new MockPaymentProcessor(),
                default => throw new InvalidArgumentException("Unknown payment driver: {$driver}")
            };
        });
    }
}

// Usage anywhere in Laravel (framework feature)
class InvoiceController extends Controller
{
    public function processPayment(Request $request, PaymentProcessor $processor)
    {
        // Container factory provides the right implementation
        $result = $processor->charge($request->amount, $request->payment_method);
        
        return response()->json($result);
    }
}
```

### Custom Factory Example (Proposed Enhancement)
```php
// Custom factory for file processing (proposed improvement)
abstract class FileProcessorFactory
{
    public static function create(string $fileType): FileProcessorInterface
    {
        return match ($fileType) {
            'image' => new ImageProcessor([
                'max_width' => 1920,
                'max_height' => 1080,
                'quality' => 85
            ]),
            'document' => new DocumentProcessor([
                'extract_text' => true,
                'generate_thumbnail' => true
            ]),
            'video' => new VideoProcessor([
                'max_duration' => 300,
                'compress' => true
            ]),
            'archive' => new ArchiveProcessor([
                'extract' => false,
                'scan_contents' => true
            ]),
            default => new GenericFileProcessor()
        };
    }

    public static function createForUpload(UploadedFile $file): FileProcessorInterface
    {
        $mimeType = $file->getMimeType();
        
        $fileType = match (true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'video/') => 'video',
            in_array($mimeType, ['application/pdf', 'application/msword']) => 'document',
            in_array($mimeType, ['application/zip', 'application/x-rar']) => 'archive',
            default => 'generic'
        };

        return self::create($fileType);
    }
}

// Usage in our file upload system (proposed improvement)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user)
    {
        $processedFiles = [];
        
        foreach ($request->file('files') as $file) {
            // Factory creates appropriate processor based on file type
            $processor = FileProcessorFactory::createForUpload($file);
            $processedFile = $processor->process($file);
            $processedFiles[] = $processedFile;
        }
        
        return response()->json(['processed_files' => $processedFiles]);
    }
}
```

### Database Connection Factory (Framework Concept)
```php
// Laravel's database connection factory (framework concept)
class DatabaseManager
{
    protected array $connections = [];
    
    public function connection(string $name = null): Connection
    {
        $name = $name ?: $this->getDefaultConnection();
        
        // Factory pattern - creates connection if it doesn't exist
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }
        
        return $this->connections[$name];
    }
    
    protected function createConnection(string $name): Connection
    {
        $config = $this->getConnectionConfig($name);
        
        // Factory creates different connection types
        return match ($config['driver']) {
            'mysql' => new MySqlConnection($config),
            'pgsql' => new PostgresConnection($config),
            'sqlite' => new SQLiteConnection($config),
            'sqlsrv' => new SqlServerConnection($config),
            default => throw new InvalidArgumentException("Unsupported driver: {$config['driver']}")
        };
    }
}

// This is why you can do: DB::connection('analytics')->table('events')->get()
```

## Pattern 16: Adapter Pattern

### The Basic Idea
The Adapter pattern allows incompatible interfaces to work together by wrapping one interface to match another that clients expect.

### Construction Analogy
Like electrical outlet adapters when traveling internationally. Your laptop charger (client) expects a specific plug format, but the wall outlet (adaptee) in another country has a different format. An adapter converts between them so your charger can work with any outlet type without modification.

### What This Means in Practice
- Wraps existing classes to match expected interfaces
- Allows integration of third-party libraries with different interfaces
- Provides consistent APIs across different implementations
- Enables switching between different service providers

### Why Should You Care?
Adapter pattern lets you integrate disparate systems without modifying their source code and provides consistent interfaces across different implementations.

### The Sanity Check
If you need to use multiple similar services (payment processors, email providers, storage systems) with different APIs, you need the Adapter pattern.

### Laravel's Multi-Driver System (Framework Feature)
```php
// Laravel's cache system uses Adapter pattern (framework feature)
// All drivers implement the same interface but adapt different storage systems

interface CacheInterface
{
    public function get(string $key): mixed;
    public function put(string $key, mixed $value, int $ttl = null): bool;
    public function forget(string $key): bool;
    public function flush(): bool;
}

// Redis Adapter
class RedisStore implements CacheInterface
{
    public function __construct(private Redis $redis) {}
    
    public function get(string $key): mixed
    {
        // Adapts Redis API to Laravel's cache interface
        return $this->redis->get($key);
    }
    
    public function put(string $key, mixed $value, int $ttl = null): bool
    {
        // Adapts Redis setex command to Laravel's put method
        return $this->redis->setex($key, $ttl ?? 3600, serialize($value));
    }
    
    public function forget(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }
    
    public function flush(): bool
    {
        return $this->redis->flushAll();
    }
}

// File System Adapter
class FileStore implements CacheInterface
{
    public function __construct(private string $cachePath) {}
    
    public function get(string $key): mixed
    {
        // Adapts file system operations to Laravel's cache interface
        $file = $this->cachePath . '/' . md5($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    public function put(string $key, mixed $value, int $ttl = null): bool
    {
        // Adapts file operations to cache interface
        $file = $this->cachePath . '/' . md5($key);
        $data = [
            'value' => $value,
            'expires' => time() + ($ttl ?? 3600)
        ];
        
        return (bool) file_put_contents($file, serialize($data));
    }
    
    // ... other methods adapt file operations
}

// Usage - same interface for all cache drivers (framework feature)
class UserService
{
    public function getUser(int $id): User
    {
        // Works with Redis, File, Database, or any cache driver
        return Cache::remember("user.{$id}", 3600, function () use ($id) {
            return User::find($id);
        });
    }
}
```

### Payment Processor Adapter (Proposed Enhancement)
```php
// Adapting different payment APIs to consistent interface (proposed improvement)
interface PaymentProcessorInterface
{
    public function charge(float $amount, string $currency, array $paymentMethod): PaymentResult;
    public function refund(string $transactionId, float $amount = null): RefundResult;
    public function getTransaction(string $transactionId): TransactionDetails;
}

// Stripe Adapter
class StripePaymentAdapter implements PaymentProcessorInterface
{
    public function __construct(private StripeClient $stripe) {}
    
    public function charge(float $amount, string $currency, array $paymentMethod): PaymentResult
    {
        // Adapts Stripe's API to our interface
        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) ($amount * 100), // Stripe uses cents
                'currency' => $currency,
                'payment_method' => $paymentMethod['stripe_token'],
                'confirm' => true,
            ]);
            
            return new PaymentResult(
                success: $intent->status === 'succeeded',
                transactionId: $intent->id,
                amount: $amount,
                currency: $currency
            );
        } catch (StripeException $e) {
            return PaymentResult::failed($e->getMessage());
        }
    }
}

// PayPal Adapter  
class PayPalPaymentAdapter implements PaymentProcessorInterface
{
    public function __construct(private PayPalClient $paypal) {}
    
    public function charge(float $amount, string $currency, array $paymentMethod): PaymentResult
    {
        // Adapts PayPal's different API structure to our interface
        try {
            $payment = new Payment();
            $payment->setIntent('sale')
                   ->setPayer($this->createPayer($paymentMethod))
                   ->setTransactions([
                       $this->createTransaction($amount, $currency)
                   ]);
                   
            $result = $payment->create($this->paypal);
            
            return new PaymentResult(
                success: $result->getState() === 'approved',
                transactionId: $result->getId(),
                amount: $amount,
                currency: $currency
            );
        } catch (PayPalException $e) {
            return PaymentResult::failed($e->getMessage());
        }
    }
}

// Usage - same interface regardless of payment processor (proposed improvement)
class InvoiceController extends Controller
{
    public function processPayment(
        ProcessPaymentRequest $request,
        PaymentProcessorInterface $processor
    ) {
        // Works with Stripe, PayPal, or any payment adapter
        $result = $processor->charge(
            $request->amount,
            $request->currency,
            $request->payment_method
        );
        
        if ($result->success) {
            Invoice::find($request->invoice_id)->markAsPaid($result->transactionId);
        }
        
        return response()->json($result);
    }
}
```

### Filesystem Adapter (Framework Feature)
```php
// Laravel's filesystem adapters (framework concept)
interface FilesystemInterface
{
    public function put(string $path, string $contents): bool;
    public function get(string $path): string;
    public function delete(string $path): bool;
    public function exists(string $path): bool;
}

// Local Filesystem Adapter
class LocalAdapter implements FilesystemInterface
{
    public function put(string $path, string $contents): bool
    {
        // Adapts PHP file functions to consistent interface
        return (bool) file_put_contents($this->prefixPath($path), $contents);
    }
    
    public function get(string $path): string
    {
        return file_get_contents($this->prefixPath($path));
    }
}

// S3 Adapter
class S3Adapter implements FilesystemInterface
{
    public function __construct(private S3Client $s3, private string $bucket) {}
    
    public function put(string $path, string $contents): bool
    {
        // Adapts AWS S3 SDK to consistent interface
        try {
            $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $path,
                'Body' => $contents,
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
}

// Usage - same interface for local files or cloud storage (framework feature)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Works with local, S3, FTP, or any storage adapter
        foreach ($request->file('files') as $file) {
            $path = 'uploads/' . $file->getClientOriginalName();
            Storage::put($path, $file->getContent());
        }
    }
}
```

## Pattern 17: Decorator Pattern

### The Basic Idea
The Decorator pattern adds new functionality to objects dynamically without altering their structure, by wrapping them in decorator classes that implement the same interface.

### Construction Analogy
Like adding features to a house after it's built: you can add a security system, solar panels, or a deck without changing the house's core structure. Each addition (decorator) enhances the house's functionality while keeping the original foundation intact.

### What This Means in Practice
- Functionality is added through wrapper classes rather than inheritance
- Objects can be decorated with multiple layers of functionality
- Decorators implement the same interface as the objects they wrap
- Behavior can be composed at runtime

### Why Should You Care?
Decorator pattern provides flexible alternatives to subclassing and allows you to combine behaviors dynamically without creating explosion of classes.

### The Sanity Check
If you find yourself creating many subclasses just to add combinations of features, or if you need to add/remove functionality at runtime, you need the Decorator pattern.

### Laravel's Middleware as Decorators (Framework Feature)
```php
// Laravel middleware decorates request/response handling (actual framework concept)
// Each middleware wraps the next one, adding functionality

class AuthenticateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Decorates the request with authentication logic
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Pass to next decorator/handler
        return $next($request);
    }
}

class LogRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Decorates with logging functionality
        Log::info('Request started', ['url' => $request->url()]);
        
        $response = $next($request);
        
        Log::info('Request completed', ['status' => $response->getStatusCode()]);
        
        return $response;
    }
}

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Decorates with rate limiting functionality
        $key = 'rate_limit:' . $request->ip();
        
        if (Cache::get($key, 0) > 100) {
            abort(429, 'Too Many Requests');
        }
        
        Cache::increment($key);
        
        return $next($request);
    }
}

// Multiple decorators can be stacked (framework feature)
Route::get('/api/data', [DataController::class, 'index'])
    ->middleware(['auth', 'log.request', 'rate.limit']); // Multiple decorators
```

### Cache Decorator Example (Proposed Enhancement)
```php
// Decorating services with caching functionality (proposed improvement)
interface UserRepositoryInterface
{
    public function find(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
}

class DatabaseUserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }
    
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    
    public function create(array $data): User
    {
        return User::create($data);
    }
}

// Cache decorator adds caching functionality without changing the repository
class CachedUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private int $cacheTtl = 3600
    ) {}
    
    public function find(int $id): ?User
    {
        // Decorates with caching
        return Cache::remember(
            "user.{$id}",
            $this->cacheTtl,
            fn() => $this->repository->find($id)
        );
    }
    
    public function findByEmail(string $email): ?User
    {
        return Cache::remember(
            "user.email.{$email}",
            $this->cacheTtl,
            fn() => $this->repository->findByEmail($email)
        );
    }
    
    public function create(array $data): User
    {
        // For write operations, invalidate related caches
        $user = $this->repository->create($data);
        
        Cache::forget("user.email.{$user->email}");
        
        return $user;
    }
}

// Audit decorator adds logging functionality
class AuditedUserRepository implements UserRepositoryInterface
{
    public function __construct(private UserRepositoryInterface $repository) {}
    
    public function find(int $id): ?User
    {
        $user = $this->repository->find($id);
        
        if ($user) {
            Log::info('User accessed', ['user_id' => $id]);
        }
        
        return $user;
    }
    
    public function create(array $data): User
    {
        $user = $this->repository->create($data);
        
        Log::info('User created', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return $user;
    }
    
    public function findByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }
}

// Service provider can stack decorators (proposed improvement)
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            $repository = new DatabaseUserRepository();
            
            // Stack decorators based on configuration
            if (config('cache.enabled')) {
                $repository = new CachedUserRepository($repository);
            }
            
            if (config('audit.enabled')) {
                $repository = new AuditedUserRepository($repository);
            }
            
            return $repository;
        });
    }
}
```

## Pattern 18: Proxy Pattern

### The Basic Idea
The Proxy pattern provides a placeholder or surrogate for another object to control access to it, adding functionality like lazy loading, access control, or caching.

### Construction Analogy
Like a security guard at a construction site who controls access to the actual worksite. The guard (proxy) handles initial interactions, checks credentials, logs visits, and only allows appropriate access to the real construction area (target object). Visitors interact with the guard as if they're interacting with the site itself.

### What This Means in Practice
- Proxy controls access to the real object
- Can add functionality like lazy loading, caching, or security
- Client interacts with proxy as if it were the real object
- Proxy can optimize expensive operations

### Why Should You Care?
Proxy pattern optimizes performance through lazy loading and caching, and provides additional control over how objects are accessed.

### The Sanity Check
If you have expensive objects that aren't always needed, or if you need to control access to objects, you're looking at Proxy pattern opportunities.

### Laravel's Eloquent Relationships (Framework Feature)
```php
// Eloquent relationships use Proxy pattern for lazy loading (framework feature)
class User extends Model
{
    public function files()
    {
        // Returns a proxy that will load files only when accessed
        return $this->hasMany(File::class);
    }
    
    public function application()
    {
        // Proxy for lazy loading application
        return $this->belongsTo(Application::class);
    }
}

// Usage - demonstrates lazy loading proxy behavior (framework feature)
class FileController extends Controller
{
    public function show(User $user)
    {
        // User is loaded, but files are NOT loaded yet (proxy exists)
        $userName = $user->name; // No additional query
        
        // First access to files triggers the actual database query
        $fileCount = $user->files->count(); // NOW files are loaded via proxy
        
        // Subsequent access uses cached data
        $firstFile = $user->files->first(); // No additional query
        
        return response()->json([
            'user' => $userName,
            'file_count' => $fileCount,
            'first_file' => $firstFile
        ]);
    }
}
```

### Cache Proxy Example (Proposed Enhancement)
```php
// Proxy that adds caching to expensive operations (proposed improvement)
interface ExpensiveServiceInterface
{
    public function generateReport(array $parameters): Report;
    public function processData(array $data): ProcessedData;
}

class ExpensiveService implements ExpensiveServiceInterface
{
    public function generateReport(array $parameters): Report
    {
        // Expensive operation - complex calculations, API calls, etc.
        sleep(5); // Simulating expensive operation
        
        return new Report($parameters);
    }
    
    public function processData(array $data): ProcessedData
    {
        // Another expensive operation
        sleep(3);
        
        return new ProcessedData($data);
    }
}

// Cache proxy controls access and adds caching
class CachedServiceProxy implements ExpensiveServiceInterface
{
    public function __construct(
        private ExpensiveServiceInterface $service,
        private int $cacheTtl = 1800
    ) {}
    
    public function generateReport(array $parameters): Report
    {
        $cacheKey = 'report:' . md5(serialize($parameters));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($parameters) {
            // Proxy controls when the real service is called
            Log::info('Cache miss - generating report', ['parameters' => $parameters]);
            return $this->service->generateReport($parameters);
        });
    }
    
    public function processData(array $data): ProcessedData
    {
        $cacheKey = 'processed_data:' . md5(serialize($data));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($data) {
            Log::info('Cache miss - processing data', ['data_size' => count($data)]);
            return $this->service->processData($data);
        });
    }
}

// Usage - client doesn't know it's using a proxy (proposed improvement)
class ReportController extends Controller
{
    public function generate(
        GenerateReportRequest $request,
        ExpensiveServiceInterface $service // Could be real service or proxy
    ) {
        // First call might be slow (cache miss)
        $report = $service->generateReport($request->validated());
        
        // Subsequent identical calls will be fast (cache hit)
        return response()->json($report);
    }
}
```

## Pattern 19: State Pattern

### The Basic Idea
The State pattern allows an object to change its behavior when its internal state changes, appearing as if the object changed its class.

### Construction Analogy
Like a construction project that behaves differently depending on its current phase: during planning (permits, drawings), foundation work (excavation, concrete), framing (structure, roofing), and finishing (plumbing, electrical). The same project responds differently to requests based on which phase it's in.

### What This Means in Practice
- Object behavior changes based on internal state
- Each state is encapsulated in separate classes
- State transitions are controlled and explicit
- Eliminates complex conditional logic

### Why Should You Care?
State pattern eliminates complex if/else chains for state-dependent behavior and makes adding new states easier without modifying existing code.

### The Sanity Check
If you have objects with complex state-dependent behavior or long switch statements based on object state, you need the State pattern.

### Order State Management (Proposed Enhancement)
```php
// State pattern for order processing (proposed improvement)
abstract class OrderState
{
    abstract public function process(Order $order): void;
    abstract public function cancel(Order $order): void;
    abstract public function ship(Order $order): void;
    abstract public function canTransitionTo(string $state): bool;
}

class PendingOrderState extends OrderState
{
    public function process(Order $order): void
    {
        // Process payment
        $paymentResult = PaymentService::charge($order->total, $order->payment_method);
        
        if ($paymentResult->successful()) {
            $order->setState(new ProcessingOrderState());
            $order->addNote('Payment processed successfully');
        } else {
            $order->setState(new FailedOrderState());
            $order->addNote('Payment failed: ' . $paymentResult->error);
        }
    }
    
    public function cancel(Order $order): void
    {
        $order->setState(new CancelledOrderState());
        $order->addNote('Order cancelled while pending');
    }
    
    public function ship(Order $order): void
    {
        throw new InvalidStateTransition('Cannot ship pending order');
    }
    
    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['processing', 'cancelled', 'failed']);
    }
}

class ProcessingOrderState extends OrderState
{
    public function process(Order $order): void
    {
        throw new InvalidStateTransition('Order is already being processed');
    }
    
    public function cancel(Order $order): void
    {
        // Refund payment before cancelling
        PaymentService::refund($order->payment_transaction_id);
        $order->setState(new CancelledOrderState());
        $order->addNote('Order cancelled and refunded');
    }
    
    public function ship(Order $order): void
    {
        $trackingNumber = ShippingService::ship($order);
        $order->tracking_number = $trackingNumber;
        $order->setState(new ShippedOrderState());
        $order->addNote('Order shipped with tracking: ' . $trackingNumber);
        
        // Send shipping notification
        Mail::to($order->user)->send(new OrderShippedNotification($order));
    }
    
    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['shipped', 'cancelled']);
    }
}

class ShippedOrderState extends OrderState
{
    public function process(Order $order): void
    {
        throw new InvalidStateTransition('Order is already shipped');
    }
    
    public function cancel(Order $order): void
    {
        throw new InvalidStateTransition('Cannot cancel shipped order');
    }
    
    public function ship(Order $order): void
    {
        throw new InvalidStateTransition('Order is already shipped');
    }
    
    public function canTransitionTo(string $state): bool
    {
        return in_array($state, ['delivered', 'returned']);
    }
}

// Order model uses state pattern (proposed improvement)
class Order extends Model
{
    private OrderState $state;
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->state = new PendingOrderState();
    }
    
    public function process(): void
    {
        $this->state->process($this);
        $this->save();
    }
    
    public function cancel(): void
    {
        $this->state->cancel($this);
        $this->save();
    }
    
    public function ship(): void
    {
        $this->state->ship($this);
        $this->save();
    }
    
    public function setState(OrderState $state): void
    {
        $this->state = $state;
        $this->status = $state->getStateName();
    }
    
    public function canTransitionTo(string $state): bool
    {
        return $this->state->canTransitionTo($state);
    }
}

// Usage in controller (proposed improvement)
class OrderController extends Controller
{
    public function process(Order $order)
    {
        try {
            $order->process();
            return response()->json(['message' => 'Order processed successfully']);
        } catch (InvalidStateTransition $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function ship(Order $order)
    {
        try {
            $order->ship();
            return response()->json(['message' => 'Order shipped successfully']);
        } catch (InvalidStateTransition $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

## Pattern 20: Null Object Pattern

### The Basic Idea
The Null Object pattern provides a default object with neutral behavior to eliminate null checks throughout your code.

### Construction Analogy
Like having a "no work scheduled" default task on a construction site. Instead of workers constantly checking if there's work to do (null checks), they always have a task assigned - even if that task is "standby" or "maintenance." The standby task does nothing harmful but provides consistent behavior.

### What This Means in Practice
- Replaces null returns with objects that have neutral behavior
- Eliminates repetitive null checks in client code
- Provides consistent interfaces even when no data exists
- Reduces complexity in calling code

### Why Should You Care?
Null Object pattern makes your code more robust by eliminating null pointer exceptions and reduces the need for defensive null checks everywhere.

### The Sanity Check
If you have many null checks scattered throughout your code, or if null returns break the flow of your program, you need the Null Object pattern.

### User Preferences Example (Proposed Enhancement)
```php
// Null object pattern for user preferences (proposed improvement)
interface UserPreferencesInterface
{
    public function getTheme(): string;
    public function getLanguage(): string;
    public function getTimezone(): string;
    public function getNotificationSettings(): array;
    public function isEmailNotificationsEnabled(): bool;
}

class UserPreferences implements UserPreferencesInterface
{
    public function __construct(private array $preferences) {}
    
    public function getTheme(): string
    {
        return $this->preferences['theme'] ?? 'light';
    }
    
    public function getLanguage(): string
    {
        return $this->preferences['language'] ?? 'en';
    }
    
    public function getTimezone(): string
    {
        return $this->preferences['timezone'] ?? 'UTC';
    }
    
    public function getNotificationSettings(): array
    {
        return $this->preferences['notifications'] ?? [];
    }
    
    public function isEmailNotificationsEnabled(): bool
    {
        return $this->preferences['email_notifications'] ?? true;
    }
}

// Null object - provides safe defaults when user has no preferences
class NullUserPreferences implements UserPreferencesInterface
{
    public function getTheme(): string
    {
        return 'light';
    }
    
    public function getLanguage(): string
    {
        return 'en';
    }
    
    public function getTimezone(): string
    {
        return 'UTC';
    }
    
    public function getNotificationSettings(): array
    {
        return [
            'email' => true,
            'push' => false,
            'sms' => false
        ];
    }
    
    public function isEmailNotificationsEnabled(): bool
    {
        return true;
    }
}

// Service that uses null object pattern (proposed improvement)
class UserPreferenceService
{
    public function getPreferences(User $user): UserPreferencesInterface
    {
        $preferences = $user->preferences;
        
        // Instead of returning null, return null object
        if (empty($preferences)) {
            return new NullUserPreferences();
        }
        
        return new UserPreferences($preferences);
    }
}

// Usage - no null checks needed! (proposed improvement)
class DashboardController extends Controller
{
    public function show(User $user, UserPreferenceService $preferenceService)
    {
        $preferences = $preferenceService->getPreferences($user);
        
        // No null checks needed - null object provides safe defaults
        return view('dashboard', [
            'theme' => $preferences->getTheme(),
            'language' => $preferences->getLanguage(),
            'timezone' => $preferences->getTimezone(),
            'notifications' => $preferences->getNotificationSettings()
        ]);
    }
}
```

### File Processing Null Object (Proposed Enhancement)
```php
// Null object for file processors (proposed improvement)  
interface FileProcessorInterface
{
    public function process(UploadedFile $file): ProcessedFile;
    public function canProcess(UploadedFile $file): bool;
    public function getProcessingTime(): int;
}

// Regular processors
class ImageProcessor implements FileProcessorInterface
{
    public function process(UploadedFile $file): ProcessedFile
    {
        // Complex image processing logic
        return new ProcessedFile($file, ['resized', 'optimized']);
    }
    
    public function canProcess(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }
    
    public function getProcessingTime(): int
    {
        return 30; // seconds
    }
}

// Null object processor - safe default behavior
class NullFileProcessor implements FileProcessorInterface
{
    public function process(UploadedFile $file): ProcessedFile
    {
        // Do nothing - just return file as-is
        return new ProcessedFile($file, ['no_processing']);
    }
    
    public function canProcess(UploadedFile $file): bool
    {
        return true; // Can "process" any file (by doing nothing)
    }
    
    public function getProcessingTime(): int
    {
        return 0; // No processing time
    }
}

// Factory that uses null object pattern (proposed improvement)
class FileProcessorFactory
{
    private static array $processors = [
        ImageProcessor::class,
        DocumentProcessor::class,
        VideoProcessor::class,
    ];
    
    public static function getProcessor(UploadedFile $file): FileProcessorInterface
    {
        foreach (self::$processors as $processorClass) {
            $processor = new $processorClass();
            if ($processor->canProcess($file)) {
                return $processor;
            }
        }
        
        // Instead of returning null or throwing exception, return null object
        return new NullFileProcessor();
    }
}

// Usage - no need to check for null processors (proposed improvement)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        $results = [];
        
        foreach ($request->file('files') as $file) {
            $processor = FileProcessorFactory::getProcessor($file);
            
            // No null checks needed - null object handles unknown file types safely
            $processedFile = $processor->process($file);
            $results[] = [
                'filename' => $file->getClientOriginalName(),
                'processing_time' => $processor->getProcessingTime(),
                'processed' => $processedFile
            ];
        }
        
        return response()->json($results);
    }
}
```

## Pattern 21: Factory Method Pattern

### The Basic Idea
The Factory Method pattern defines an interface for creating objects, but lets subclasses decide which class to instantiate. It uses inheritance to delegate object creation to subclasses.

### Construction Analogy
Like different construction companies that all follow the same building process but specialize in different types of buildings. A residential builder and a commercial builder both follow the same general construction steps (foundation, framing, finishing), but each creates different types of structures. The building process is the same, but the specific building type is determined by which specialist company you hire.

### What This Means in Practice
- A base class defines the algorithm that uses objects
- Subclasses implement factory methods that create specific object types
- Object creation is deferred to subclasses
- The same algorithm works with different object types

### Why Should You Care?
Factory Method promotes loose coupling by eliminating the need to bind application-specific classes into your code. It provides hooks for subclasses to extend object creation.

### The Sanity Check
If you have a class that needs to create objects but doesn't know exactly which class to instantiate until runtime, or if you want subclasses to specify the objects they create, you need the Factory Method pattern.

### Laravel's Notification System (Framework Feature)
```php
// Laravel's notification system uses Factory Method pattern (actual framework concept)
abstract class NotificationChannel
{
    // Template method that uses the factory method
    public function send($notifiable, Notification $notification)
    {
        // Use factory method to create appropriate message
        $message = $this->createMessage($notifiable, $notification);
        
        // Common sending logic
        return $this->deliver($message, $notifiable);
    }
    
    // Factory method - subclasses decide what to create
    abstract protected function createMessage($notifiable, Notification $notification): MessageInterface;
    
    // Common functionality shared by all channels
    protected function deliver(MessageInterface $message, $notifiable)
    {
        // Common delivery logic
    }
}

// Concrete factory implementations
class MailChannel extends NotificationChannel
{
    protected function createMessage($notifiable, Notification $notification): MessageInterface
    {
        // Creates email-specific message
        return new MailMessage(
            $notification->toMail($notifiable)
        );
    }
}

class SlackChannel extends NotificationChannel
{
    protected function createMessage($notifiable, Notification $notification): MessageInterface
    {
        // Creates Slack-specific message
        return new SlackMessage(
            $notification->toSlack($notifiable)
        );
    }
}

class DatabaseChannel extends NotificationChannel
{
    protected function createMessage($notifiable, Notification $notification): MessageInterface
    {
        // Creates database-specific message
        return new DatabaseMessage([
            'type' => get_class($notification),
            'data' => $notification->toDatabase($notifiable),
            'read_at' => null,
        ]);
    }
}

// Usage - Laravel handles this automatically (framework feature)
class User extends Authenticatable
{
    use Notifiable;
}

class FileUploadedNotification extends Notification
{
    public function via($notifiable)
    {
        // Laravel will use appropriate factory method based on channels
        return ['mail', 'slack', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your file has been uploaded successfully.')
            ->action('View File', url('/files'));
    }
    
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('File uploaded successfully!')
            ->attachment(function ($attachment) {
                $attachment->title('View File', url('/files'));
            });
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your file has been uploaded successfully.',
            'file_url' => url('/files'),
        ];
    }
}
```

### Authentication Guards (Framework Feature)
```php
// Laravel's authentication system uses Factory Method (framework concept)
abstract class Guard
{
    // Template method that uses factory method
    public function attempt(array $credentials = [], bool $remember = false)
    {
        // Common authentication logic
        $user = $this->validateCredentials($credentials);
        
        if ($user) {
            // Use factory method to create appropriate session/token
            $authToken = $this->createAuthToken($user, $remember);
            return $this->completeAuthentication($user, $authToken);
        }
        
        return false;
    }
    
    // Factory method - subclasses decide what auth mechanism to create
    abstract protected function createAuthToken($user, bool $remember): AuthTokenInterface;
    
    protected function validateCredentials(array $credentials)
    {
        // Common credential validation
    }
    
    protected function completeAuthentication($user, AuthTokenInterface $token)
    {
        // Common post-authentication logic
    }
}

class SessionGuard extends Guard
{
    protected function createAuthToken($user, bool $remember): AuthTokenInterface
    {
        // Creates session-based authentication
        return new SessionAuthToken(
            session_id: session()->getId(),
            remember: $remember,
            user_id: $user->id
        );
    }
}

class TokenGuard extends Guard
{
    protected function createAuthToken($user, bool $remember): AuthTokenInterface
    {
        // Creates token-based authentication
        return new ApiAuthToken(
            token: $user->createToken('auth-token')->plainTextToken,
            expires_at: $remember ? now()->addDays(30) : now()->addDay(),
            user_id: $user->id
        );
    }
}

class JwtGuard extends Guard
{
    protected function createAuthToken($user, bool $remember): AuthTokenInterface
    {
        // Creates JWT-based authentication
        return new JwtAuthToken(
            jwt: JWT::encode(['user_id' => $user->id], config('app.key')),
            expires_at: $remember ? now()->addDays(30) : now()->addHours(2),
            user_id: $user->id
        );
    }
}
```

### File Processing Factory Method (Proposed Enhancement)
```php
// Custom factory method for our file upload system (proposed improvement)
abstract class FileProcessor
{
    // Template method that uses factory method
    public function processUpload(UploadedFile $file): ProcessedFile
    {
        // Common processing steps
        $this->validateFile($file);
        
        // Use factory method to create appropriate handler
        $handler = $this->createHandler($file);
        
        // Process using the specific handler
        $result = $handler->process($file);
        
        // Common post-processing
        $this->logProcessing($file, $result);
        
        return $result;
    }
    
    // Factory method - subclasses decide which handler to create
    abstract protected function createHandler(UploadedFile $file): FileHandlerInterface;
    
    protected function validateFile(UploadedFile $file): void
    {
        // Common file validation logic
        if (!$file->isValid()) {
            throw new InvalidFileException('File is not valid');
        }
    }
    
    protected function logProcessing(UploadedFile $file, ProcessedFile $result): void
    {
        Log::info('File processed', [
            'original_name' => $file->getClientOriginalName(),
            'processed_size' => $result->getSize(),
            'processor' => static::class
        ]);
    }
}

class ImageProcessor extends FileProcessor
{
    protected function createHandler(UploadedFile $file): FileHandlerInterface
    {
        // Creates image-specific handlers based on file type
        return match ($file->getClientOriginalExtension()) {
            'jpg', 'jpeg' => new JpegImageHandler(),
            'png' => new PngImageHandler(),
            'gif' => new GifImageHandler(),
            'webp' => new WebpImageHandler(),
            default => new GenericImageHandler()
        };
    }
}

class DocumentProcessor extends FileProcessor
{
    protected function createHandler(UploadedFile $file): FileHandlerInterface
    {
        // Creates document-specific handlers
        return match ($file->getMimeType()) {
            'application/pdf' => new PdfDocumentHandler(),
            'application/msword' => new WordDocumentHandler(),
            'application/vnd.ms-excel' => new ExcelDocumentHandler(),
            'text/plain' => new TextDocumentHandler(),
            default => new GenericDocumentHandler()
        };
    }
}

class ArchiveProcessor extends FileProcessor
{
    protected function createHandler(UploadedFile $file): FileHandlerInterface
    {
        // Creates archive-specific handlers
        return match ($file->getClientOriginalExtension()) {
            'zip' => new ZipArchiveHandler(),
            'rar' => new RarArchiveHandler(),
            'tar', 'tar.gz' => new TarArchiveHandler(),
            '7z' => new SevenZipArchiveHandler(),
            default => new GenericArchiveHandler()
        };
    }
}

// Usage in our file upload system (proposed improvement)
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user)
    {
        $processedFiles = [];
        
        foreach ($request->file('files') as $file) {
            // Factory method pattern - processor creates appropriate handler
            $processor = $this->getProcessorForFile($file);
            $processedFile = $processor->processUpload($file);
            $processedFiles[] = $processedFile;
        }
        
        return response()->json(['processed_files' => $processedFiles]);
    }
    
    private function getProcessorForFile(UploadedFile $file): FileProcessor
    {
        $mimeType = $file->getMimeType();
        
        return match (true) {
            str_starts_with($mimeType, 'image/') => new ImageProcessor(),
            str_starts_with($mimeType, 'application/') => new DocumentProcessor(),
            in_array($file->getClientOriginalExtension(), ['zip', 'rar', 'tar', '7z']) => new ArchiveProcessor(),
            default => new GenericFileProcessor()
        };
    }
}
```

### Broadcasting Drivers (Framework Concept)
```php
// Laravel's broadcasting system uses Factory Method (framework concept)
abstract class BroadcastDriver
{
    public function broadcast(array $channels, string $event, array $payload = [])
    {
        // Common broadcasting logic
        $this->validateChannels($channels);
        
        // Use factory method to create appropriate message
        $message = $this->createBroadcastMessage($event, $payload);
        
        // Send to all channels
        foreach ($channels as $channel) {
            $this->sendToChannel($channel, $message);
        }
    }
    
    // Factory method - subclasses create driver-specific messages
    abstract protected function createBroadcastMessage(string $event, array $payload): BroadcastMessage;
    
    abstract protected function sendToChannel(string $channel, BroadcastMessage $message): void;
}

class PusherBroadcastDriver extends BroadcastDriver
{
    protected function createBroadcastMessage(string $event, array $payload): BroadcastMessage
    {
        return new PusherBroadcastMessage($event, $payload);
    }
    
    protected function sendToChannel(string $channel, BroadcastMessage $message): void
    {
        $this->pusher->trigger($channel, $message->getEvent(), $message->getPayload());
    }
}

class RedisBroadcastDriver extends BroadcastDriver
{
    protected function createBroadcastMessage(string $event, array $payload): BroadcastMessage
    {
        return new RedisBroadcastMessage($event, $payload);
    }
    
    protected function sendToChannel(string $channel, BroadcastMessage $message): void
    {
        $this->redis->publish($channel, json_encode([
            'event' => $message->getEvent(),
            'data' => $message->getPayload()
        ]));
    }
}
```

### Difference from Simple Factory Pattern
```php
// Simple Factory (Pattern 15 - what we already have)
class FileProcessorFactory
{
    public static function create(string $type): FileProcessorInterface
    {
        // Factory class decides what to create
        return match ($type) {
            'image' => new ImageProcessor(),
            'document' => new DocumentProcessor(),
            default => new GenericProcessor()
        };
    }
}

// Factory Method (Pattern 21 - this pattern)
abstract class FileUploadHandler
{
    public function handleUpload(UploadedFile $file)
    {
        // Template method uses factory method
        $processor = $this->createProcessor($file); // Factory method call
        return $processor->process($file);
    }
    
    // Subclasses decide what to create
    abstract protected function createProcessor(UploadedFile $file): ProcessorInterface;
}

class ImageUploadHandler extends FileUploadHandler
{
    protected function createProcessor(UploadedFile $file): ProcessorInterface
    {
        return new ImageProcessor(); // Subclass decides
    }
}
```

## How These Patterns Work Together

Our file upload system demonstrates how multiple design patterns create a cohesive, professional architecture:

### Pattern Interaction Example
```php
// Multiple patterns working together in our better implementation
class FileController extends Controller  // MVC Pattern
{
    public function store(
        StoreFileRequest $request,        // Validation (Command Pattern)
        #[CurrentUser] User $user,        // Dependency Injection
        AnswerService $answerService      // Service Layer + DI
    ) {
        $validatedData = $request->validated();
        
        // Strategy Pattern (AnswerService chooses upload strategy)
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );
        
        // Value Object Pattern (FileSize encapsulates file size logic)
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
        
        // Observer Pattern (proposed - events could be fired here)
        // event(new FileUploaded($user, $files, $totalSize));
    }
}
```

### The Result: Professional Laravel Architecture

By applying these design patterns, our chaotic 80-line controller becomes a clean, professional system where:

1. **MVC Pattern** separates concerns properly
2. **Service Layer** handles business logic 
3. **Strategy Pattern** manages different question types
4. **Dependency Injection** makes everything testable
5. **Value Objects** encapsulate business concepts
6. **Composition** creates flexible, reusable components
7. **Facades** provide clean interfaces (when needed)
8. **Observer** enables extensible event handling (when needed)
9. **Singleton** manages expensive resources efficiently
10. **Template Method** standardizes processes with customization points (Form Requests)
11. **Builder** constructs complex objects fluently (Query Builder, HTTP Client)
12. **Chain of Responsibility** processes requests through middleware pipeline
13. **Command** encapsulates operations for queuing and undo (Jobs, Artisan, Actions)
14. **Repository** could abstract complex data access (when needed)
15. **Factory** creates objects with centralized logic (Eloquent factories, service bindings)
16. **Adapter** integrates different systems with consistent interfaces (drivers)
17. **Decorator** adds functionality without changing existing code (middleware, caching)
18. **Proxy** provides lazy loading and access control (Eloquent relationships)
19. **State** manages complex workflows (order processing, file states)
20. **Null Object** eliminates null checks with safe defaults
21. **Factory Method** lets subclasses decide which objects to create (notifications, guards)

## Quick Reference for Junior Developers

### Before Adding New Features, Ask:

1. **MVC**: "Am I putting business logic in controllers or HTML in models?"
2. **Service Layer**: "Is my controller doing complex work, or delegating to services?"
3. **Strategy**: "Do I have multiple if/else blocks for similar operations?"
4. **Dependency Injection**: "Am I creating dependencies with 'new' or injecting them?"
5. **Value Objects**: "Am I passing around primitive values that represent domain concepts?"
6. **Composition**: "Can I combine existing components instead of inheriting?"
7. **Repository**: "Are my controllers full of complex database queries?"
8. **Observer**: "Do multiple things need to happen when this event occurs?"
9. **Singleton**: "Should this service have only one instance per request?"
10. **Template Method**: "Do I have classes following similar processes with different steps?"
11. **Builder**: "Am I creating objects with many parameters or complex configuration?"
12. **Chain of Responsibility**: "Do I have sequential processing steps that could be middleware?"
13. **Command**: "Should this operation be queued, logged, or undoable?"
14. **Facade**: "Would a simpler interface make this service easier to use?"
15. **Factory**: "Am I repeating complex object creation logic?"
16. **Adapter**: "Do I need to integrate systems with different interfaces?"
17. **Decorator**: "Do I need to add functionality without changing existing classes?"
18. **Proxy**: "Do I have expensive objects that could be lazy loaded?"
19. **State**: "Does my object behavior change based on its current state?"
20. **Null Object**: "Am I writing many null checks throughout my code?"
21. **Factory Method**: "Do I need subclasses to decide which objects to create?"

### Red Flags in Your Laravel Code:

- **Fat controllers** with business logic → Use Service Layer pattern
- **Long if/else chains** for similar operations → Use Strategy pattern
- **Manual dependency creation** with `new` → Use Dependency Injection
- **Primitive obsession** with raw integers/strings → Use Value Objects
- **Deep inheritance hierarchies** → Use Composition over Inheritance
- **Complex database queries in controllers** → Consider Repository pattern
- **Scattered event handling** → Use Observer pattern
- **Manual singleton implementation** → Use Laravel's container singletons
- **Duplicated process logic** → Use Template Method pattern
- **Complex constructors** with many parameters → Use Builder pattern
- **Monolithic request processing** → Use Chain of Responsibility (middleware)
- **Immediate complex operations** → Use Command pattern (Jobs/Queues)
- **Verbose service instantiation** → Use appropriate Facades
- **Mixed concerns** in single classes → Apply proper MVC separation
- **Repeated object creation logic** → Use Factory pattern
- **Inconsistent third-party integrations** → Use Adapter pattern  
- **Adding features by modifying existing classes** → Use Decorator pattern
- **Expensive operations loading unnecessarily** → Use Proxy pattern
- **Complex state-dependent behavior** → Use State pattern
- **Defensive null checks everywhere** → Use Null Object pattern
- **Hardcoded object creation in base classes** → Use Factory Method pattern

### Common Junior Developer Mistakes:

1. **Putting business logic in controllers** - MVC violation
2. **Not using Laravel's service container** - Missing Dependency Injection benefits
3. **Creating complex conditional chains** - Missing Strategy pattern opportunities
4. **Working with raw primitives** - Not leveraging Value Objects
5. **Building deep inheritance hierarchies** - Not using Composition properly
6. **Mixing data access with business logic** - Repository pattern could help
7. **Hardcoding event responses** - Not using Observer pattern for extensibility
8. **Manual resource management** - Not leveraging Singleton pattern appropriately
9. **Duplicating similar processes** - Missing Template Method opportunities
10. **Creating objects with telescoping constructors** - Missing Builder pattern opportunities
11. **Mixing validation/processing concerns** - Missing Chain of Responsibility opportunities
12. **Processing everything synchronously** - Missing Command pattern opportunities
13. **Avoiding Laravel's facades** - Making simple things complex
14. **Violating single responsibility** - Each class should have one reason to change
15. **Repeating complex object creation** - Missing Factory pattern opportunities
16. **Not abstracting third-party dependencies** - Missing Adapter pattern benefits
17. **Modifying existing classes for new features** - Missing Decorator pattern benefits
18. **Loading expensive data unnecessarily** - Missing Proxy pattern optimizations
19. **Using complex if/else for state behavior** - Missing State pattern opportunities
20. **Writing defensive null checks everywhere** - Missing Null Object pattern benefits
21. **Hardcoding object types in base classes** - Missing Factory Method pattern benefits

## Design Patterns in Laravel Context

### Laravel-Specific Pattern Usage:

**Always Use These Patterns:**
- **MVC** - Laravel's core architecture
- **Dependency Injection** - Laravel's container is built for this
- **Service Layer** - Keep controllers thin
- **Value Objects** - For domain concepts like money, sizes, coordinates

**Use When Appropriate:**
- **Strategy Pattern** - For handling different types of similar operations
- **Observer Pattern** - For event-driven features (notifications, logging, etc.)
- **Facade Pattern** - When building reusable packages or complex APIs
- **Composition** - Almost always prefer over inheritance

**Use Sparingly:**
- **Repository Pattern** - Laravel's Eloquent is often sufficient
- **Singleton Pattern** - Let Laravel's container handle lifecycle

**Laravel Handles These For You:**
- **Factory Pattern** - Eloquent factories, service container bindings
- **Builder Pattern** - Query builder, HTTP client builder
- **Template Method** - Form requests, middleware, commands (you customize the steps)
- **Adapter Pattern** - Multi-driver systems (database, cache, filesystem, mail)
- **Decorator Pattern** - Middleware system
- **Proxy Pattern** - Eloquent relationship lazy loading
- **Command Pattern** - Artisan commands, queued jobs

**Consider Adding Yourself:**
- **State Pattern** - For complex workflow management
- **Null Object Pattern** - For eliminating null checks in your domain

Remember: Laravel already implements many patterns internally. Focus on the patterns that improve your application code, not on reimplementing what Laravel already provides.
