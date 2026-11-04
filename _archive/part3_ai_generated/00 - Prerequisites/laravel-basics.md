# Laravel Basics for PHP Developers

If you've learned basic PHP OOP but have never worked with Laravel, this guide will give you the foundation you need to understand the advanced examples throughout this course.

## What is Laravel?

Laravel is a PHP web framework that provides structure and tools for building web applications. Think of it as a toolkit that handles common web development tasks (database operations, user authentication, routing web requests) so you can focus on your application's unique features.

**Key insight**: Laravel isn't just a library you include in your PHP files. It's a complete application structure with conventions about where files go, how classes are named, and how different parts communicate.

## Laravel vs Plain PHP

Let's start with a comparison you can understand:

### Plain PHP (What you know):
```php
// A simple PHP class you might write
class User 
{
    private int $id;
    private string $name;
    private string $email;
    
    public function __construct(int $id, string $name, string $email) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getName(): string 
    {
        return $this->name;
    }
}

// Using it
$user = new User(1, "John Doe", "john@example.com");
echo $user->getName();
```

### Laravel Equivalent:
```php
// Laravel Model - automatically connected to database
class User extends Model  // Inherits Laravel's database powers
{
    protected $fillable = ['name', 'email'];  // What fields can be set
    
    // Laravel automatically creates getName(), setName(), save(), etc.
}

// Using it - Laravel does the heavy lifting
$user = User::create(['name' => 'John Doe', 'email' => 'john@example.com']);
echo $user->name;  // Automatic getter
$user->save();     // Automatic database save
```

**The Laravel difference**: Laravel provides base classes with lots of built-in functionality, so you write less code to accomplish more.

## Essential Laravel Concepts

### 1. Models (Your Data)

**Plain English**: Models represent your data and contain business logic. In our course examples, you'll see `User`, `Application`, `File`, etc.

**What Laravel adds**: Automatic database connection, relationships between models, built-in validation.

```php
// Basic Laravel Model structure you'll see everywhere
class User extends Model 
{
    // What fields can be mass-assigned (security feature)
    protected $fillable = ['name', 'email', 'upload_limit'];
    
    // Define relationships (Laravel magic)
    public function files() 
    {
        return $this->hasMany(File::class);  // One user has many files
    }
    
    // Business logic methods (your code)
    public function canUpload(int $fileSize): bool 
    {
        return $this->upload_limit >= $fileSize;
    }
}

// Using the model
$user = User::find(1);                    // Get user from database
$files = $user->files;                    // Get related files (Laravel magic)
$canUpload = $user->canUpload(1024);      // Use business logic
```

### 2. Controllers (Handle Web Requests)

**Plain English**: Controllers handle web requests (form submissions, page loads, etc.) and coordinate between your models and views.

**What Laravel adds**: Automatic request handling, dependency injection, built-in validation.

```php
// Basic Controller structure you'll see in examples
class FileController extends Controller 
{
    // This method handles file upload requests
    public function store(Request $request)  // Laravel gives you the request
    {
        // Validate the uploaded data
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:2048'
        ]);
        
        // Do something with the files
        foreach ($request->file('files') as $file) {
            $file->store('uploads');  // Laravel handles file storage
        }
        
        return response()->json(['message' => 'Files uploaded successfully']);
    }
}
```

### 3. Dependency Injection (Laravel's Magic)

**Plain English**: Instead of creating objects yourself, you tell Laravel what you need and it provides them automatically.

**In plain PHP, you'd do this**:
```php
class FileController 
{
    public function uploadFile() 
    {
        // You create everything manually
        $database = new Database();
        $userRepository = new UserRepository($database);
        $fileService = new FileService($userRepository);
        
        // Use them
        $fileService->uploadFile();
    }
}
```

**In Laravel, you do this**:
```php
class FileController extends Controller 
{
    // Laravel automatically provides these objects
    public function store(Request $request, User $user, FileService $fileService) 
    {
        // Laravel created $request, $user, and $fileService for you
        $fileService->uploadFile();
    }
}
```

**Why this matters**: In the course examples, you'll see methods with lots of parameters. Laravel is automatically creating and providing those objects.

### 4. Facades (Laravel Shortcuts)

**Plain English**: Facades are shortcuts to Laravel services. Instead of complex object creation, you use simple static methods.

```php
// Common facades you'll see in examples:
Auth::user()                    // Get currently logged-in user
Storage::put($path, $content)   // Store a file
Mail::to($user)->send($email)   // Send an email
Gate::allows('edit', $post)     // Check permissions

// Without facades, these would be much longer:
app('auth')->user()
app('filesystem')->put($path, $content)
app('mailer')->to($user)->send($email)
app('gate')->allows('edit', $post)
```

### 5. Blade Templates (Laravel's HTML)

**Plain English**: Blade is Laravel's templating system. It lets you write HTML with PHP-like syntax.

```html
<!-- Regular HTML with PHP -->
<h1><?php echo $user->name; ?></h1>
<?php if($user->isAdmin()): ?>
    <p>You're an admin!</p>
<?php endif; ?>

<!-- Blade template (much cleaner) -->
<h1>{{ $user->name }}</h1>
@if($user->isAdmin())
    <p>You're an admin!</p>
@endif
```

### 6. Request Validation

**Plain English**: Laravel can automatically validate form data before it reaches your controller.

```php
// Form Request class (validates automatically)
class StoreFileRequest extends FormRequest 
{
    public function rules(): array 
    {
        return [
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,jpg|max:2048',
            'question_id' => 'required|integer|exists:questions,id'
        ];
    }
}

// Controller using the validation
class FileController extends Controller 
{
    // If validation fails, user never reaches this method
    public function store(StoreFileRequest $request)  // Already validated!
    {
        // $request->file('files') is guaranteed to exist and be valid
        $files = $request->file('files');
    }
}
```

## Laravel Directory Structure

When you see file paths in examples, here's what they mean:

```
app/
├── Http/
│   ├── Controllers/     // Web request handlers
│   │   └── FileController.php
│   └── Requests/        // Validation classes
│       └── StoreFileRequest.php
├── Models/             // Your data classes
│   ├── User.php
│   └── File.php
├── Services/           // Business logic classes
│   └── FileService.php
└── Providers/          // Laravel configuration
    └── AppServiceProvider.php

resources/
├── views/              // Blade templates (HTML)
│   └── upload-form.blade.php
└── js/                // JavaScript files

routes/
└── web.php            // Define which URLs go to which controllers

tests/
├── Feature/           // Integration tests
└── Unit/             // Unit tests
```

## Laravel Artisan Commands

Laravel includes a command-line tool called Artisan that generates code for you:

```bash
# Create a new model
php artisan make:model User

# Create a new controller
php artisan make:controller FileController

# Create a new request validation class
php artisan make:request StoreFileRequest

# Run tests
php artisan test

# Start the development server
php artisan serve
```

**Why this matters**: When course examples mention "create a new service class," you can use Artisan to generate the basic structure.

## Understanding Laravel "Magic"

Laravel does a lot of things automatically that seem like magic. Here's what's actually happening:

### 1. Automatic Class Loading
```php
// You write this:
use App\Models\User;
$user = new User();

// Laravel automatically finds and loads the User class from app/Models/User.php
```

### 2. Automatic Database Connection
```php
// You write this:
$user = User::find(1);

// Laravel automatically:
// - Connects to the database using config/database.php settings
// - Converts the result to a User object
// - Sets up relationships defined in the User model
```

### 3. Automatic Dependency Injection
```php
// You write this:
public function store(Request $request, FileService $service)

// Laravel automatically:
// - Creates a Request object with the current HTTP request
// - Creates a FileService object (and any dependencies it needs)
// - Calls your method with these objects
```

## Common Laravel Syntax You'll See

In the course examples, you'll encounter these patterns frequently:

```php
// Model relationships
$user->files                    // Get all files for a user
$file->user                     // Get the user who owns a file

// Database queries (Eloquent)
User::where('email', $email)->first()    // Find user by email
File::with('user')->get()                // Get files with user info
$user->files()->create($data)            // Create a new file for user

// Validation
$request->validate($rules)              // Validate request data
$request->validated()                   // Get only validated data

// Responses
return view('upload-form', $data)       // Return HTML page
return response()->json($data)          // Return JSON response
return redirect()->back()               // Redirect user back

// Authorization
Gate::authorize('upload-file', $user)   // Check if user can upload
$user->can('edit', $file)              // Check if user can edit file
```

## Setting Up Your First Laravel Project

To practice with these concepts:

```bash
# Install Laravel (requires Composer - PHP package manager)
composer create-project laravel/laravel my-first-app

# Navigate to the project
cd my-first-app

# Start the development server
php artisan serve

# Visit http://localhost:8000 in your browser
```

## Try This Yourself: Simple File Upload

Create a basic file upload to understand the concepts:

### 1. Create the Model
```bash
php artisan make:model File -m
```

### 2. Edit the migration (database/migrations/xxxx_create_files_table.php):
```php
public function up() 
{
    Schema::create('files', function (Blueprint $table) {
        $table->id();
        $table->string('filename');
        $table->string('path');
        $table->integer('size');
        $table->timestamps();
    });
}
```

### 3. Run the migration:
```bash
php artisan migrate
```

### 4. Create a controller:
```bash
php artisan make:controller FileController
```

### 5. Add upload method to FileController:
```php
class FileController extends Controller 
{
    public function store(Request $request) 
    {
        $request->validate([
            'file' => 'required|file|max:2048'
        ]);
        
        $path = $request->file('file')->store('uploads');
        
        File::create([
            'filename' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'size' => $request->file('file')->getSize(),
        ]);
        
        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
```

### 6. Add route (routes/web.php):
```php
Route::post('/upload', [FileController::class, 'store']);
```

This simple example demonstrates Models, Controllers, validation, and file handling - core concepts you'll see throughout the advanced materials.

## What's Next?

Once you understand these Laravel basics:
1. Practice with the simple file upload example
2. Read through `advanced-oop.md` for architectural concepts
3. Then you'll be ready for the materials in `Part 3 (week 27 to 39)`

**Key takeaway**: Laravel provides structure and automation for common web development tasks. The advanced course materials assume you understand this foundation and focus on architecture and design principles within that structure.
