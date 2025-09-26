# Domain Driven Design Demonstration

The questionnaire website example shows a clear violation and proper application of Domain Driven Design principles. Users upload filled-in templates that are stored on the server and related to specific questions. The original implementation in [Bad example](../example/bad/app) violates multiple DDD principles, while the refactored version in [Better example](../example/better/app) shows how to do it right.

## What Is Domain Driven Design?

Domain Driven Design (DDD) is a strategic approach to software development that puts your business domain at the center of everything. Instead of thinking about databases, frameworks, or technical concerns first, DDD says "understand the business problem deeply, then model your code to match how domain experts think and talk about the problem."

**The reality check**: Most junior developers start with technical solutions and try to squeeze business logic into them. DDD flips this - start with the business domain, understand it deeply, and let that understanding drive your technical decisions. It's the difference between building a house and then trying to fit a family into it, versus understanding how a family lives and designing the house around their needs.

Think of DDD as your business-first mindset:
- "Does my code speak the same language as the business experts?"
- "Can a domain expert understand my class names and method names?"
- "Is my code organized around business concepts or technical concerns?"

## Key Concepts for Beginners

Before diving into DDD concepts, let's clarify some terms you'll see throughout this guide:

**Domain**: The business problem you're solving - the real-world activities, rules, and processes your software supports. Like "file management for security questionnaires" rather than "database operations."

**Ubiquitous Language**: The shared vocabulary between developers and business experts. If the business calls something an "Application," your code should have an Application class, not a "Form" or "Submission."

**Value Objects**: Small, immutable objects that represent a concept from your domain. Like `FileSize`, `EmailAddress`, or `MoneyAmount` - they're not just data, they contain business rules.

**Entities**: Objects with identity that change over time. A `User` is an entity because even if their email changes, they're still the same user. Entities have lifecycles and business operations.

**Aggregates**: Clusters of related objects that work together and maintain consistency. An `Application` aggregate might contain `Questions` and `Answers` - you always work with them as a unit.

**Domain Services**: When business logic doesn't naturally belong to an entity or value object, it goes in a domain service. Like "calculate security score" - it might need multiple entities to work.

**Repositories**: The interface to your data storage, but designed around domain concepts. `UserRepository.findActiveSubscribers()` not `Database.query('SELECT * FROM users WHERE...')`.

**Why these concepts matter**: As a junior developer, DDD helps you write code that matches how the business actually works, making it easier for everyone to understand and maintain.

## Learning Path for Juniors

**Start here (Foundation):**
1. **Ubiquitous Language** - Use business terms consistently throughout your code
2. **Value Objects** - Replace primitive types with domain-specific objects
3. **Entity Design** - Create entities that model real business concepts

**Build on these (Structure):**
4. **Domain Services** - Extract complex business operations into focused services
5. **Repository Pattern** - Hide data access behind business-focused interfaces
6. **Aggregate Design** - Group related objects that need to stay consistent

**Master these (Advanced):**
7. **Bounded Contexts** - Separate large domains into focused, independent areas
8. **Domain Events** - Model business events that other parts of the system care about
9. **Strategic Design** - Organize entire systems around business capabilities

## Domain Driven Design Analysis

Let's examine how DDD principles transform our file upload system from a database-centric mess into a business-focused, maintainable domain model.

### The Anemic Reality (Bad Example)

The `User` model in our bad example is a classic "anemic domain model" - it's basically a data container with no real business logic:

```php
// From: bad/app/Models/User.php - Anemic domain model
class User extends Authenticatable implements MustVerifyEmail
{
    protected $fillable = [
        'name', 'email', 'phonenumber', 'company_name', 'company_website',
        'job_title', 'password', 'organisation_id', 'verified', 'used_ip_addresses',
        'last_control_id', 'subscription_id'
    ];

    // Business logic scattered and primitive-obsessed
    public function getTotalUploadSize(): int
    {
        return $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
    }

    public function updateUploadSizeTotal(int $totalUploadedSize): void
    {
        $this->upload_size_total = $this->getTotalUploadSize() + $totalUploadedSize;
        $this->save();
    }

    // Magic numbers and primitive obsession
    public function getUploadLimit(): int
    {
        return UPLOAD_LIMIT; // What is UPLOAD_LIMIT? Why this value?
    }
}
```

**DDD Violations:**
1. **Primitive Obsession**: Using `int` for file sizes instead of a `FileSize` domain concept
2. **Anemic Domain Model**: The model is just a data container, business logic is elsewhere
3. **Inconsistent Language**: Methods like `getTotalUploadSize()` don't reflect business language
4. **Mixed Concerns**: Infrastructure concerns (Storage facade) mixed with domain logic
5. **Magic Numbers**: Constants that don't express business meaning

### How We Fix This in the Better Example

The refactored version demonstrates proper DDD principles with rich domain models:

```php
// From: better/app/Models/User.php - Rich domain model
class User extends Authenticatable implements MustVerifyEmail
{
    // Value objects express domain concepts
    public function getTotalUploadSize(): FileSize
    {
        $totalBytes = $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
        
        return FileSize::fromBytes($totalBytes);
    }

    // Domain-focused method names and value objects
    public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
    {
        $this->upload_size_total = $this->getTotalUploadSize()
            ->add($totalUploadedSize)
            ->toBytes();
        $this->save();
    }

    public function getUploadLimit(): FileSize
    {
        return new FileSize(UPLOAD_LIMIT);
    }

    // Business operation with clear intent
    public function canUpload(int $fileSize): bool
    {
        return !$this->getTotalUploadSize()
            ->add(FileSize::fromBytes($fileSize))
            ->exceedsLimit($this->getUploadLimit());
    }
}

// Value object expressing domain concept
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

    public function exceedsLimit(FileSize $limit): bool
    {
        return $this->bytes > $limit->bytes;
    }

    public function add(FileSize $other): self
    {
        return new self($this->bytes + $other->bytes);
    }
}
```

## DDD Concept 1: Ubiquitous Language

### The Basic Idea
Your code should use the same terms that business experts use. If they say "Application" and "Upload Limit," your code should have `Application` classes and `uploadLimit` properties, not technical terms like "FormSubmission" and `maxFileSize`.

### Construction Analogy
Like architects and builders using the same terminology. When an architect says "load-bearing wall," the construction crew doesn't translate it to "big strong divider thing." Everyone uses the same precise language to avoid miscommunication and ensure the building matches the blueprint.

### What This Means in Practice
- Listen to how domain experts describe their work
- Use their exact terminology in class names, method names, and properties
- When you discover new concepts, add them to your shared vocabulary
- Avoid technical jargon that business experts don't understand

### Why You Should Care
When your code speaks the business language, business experts can actually read and review it. Changes become easier because everyone understands what needs to be modified. Bugs decrease because there's less translation between business requirements and code.

### Quick Sanity Check
"Could a business expert look at my class names and immediately understand what they do?"

### Bad Example (From Our Models)
```php
// Technical language that doesn't match business vocabulary
// (Proposed improvement - this class doesn't exist in current codebase)
class FormSubmission  // Business calls them "Applications"
{
    private $maxFileSize;  // Business talks about "Upload Limits"
    private $attachments;  // Business calls them "Supporting Documents"
    
    public function processFiles() {  // Business says "Review Supporting Documents"
        // Technical implementation details
    }
}
```

### Good Example (Proposed DDD Improvement)
```php
// Business language consistently used throughout
// (Proposed improvement - showing how we could improve our domain model)
class Application
{
    private FileSize $uploadLimit;
    private Collection $supportingDocuments;
    
    public function reviewSupportingDocuments(): void {
        // Method name matches business process
    }
}

// Value objects use business terminology
class FileSize  // Not "ByteCount" or "DataSize"
{
    public function exceedsLimit(FileSize $limit): bool {
        // Business concept: "exceeds limit"
    }
}
```

## DDD Concept 2: Value Objects

### The Basic Idea
Replace primitive types (int, string, bool) with small objects that represent business concepts. Instead of passing around a bare `int` for file size, create a `FileSize` object that knows how to behave like a file size should.

### Construction Analogy
Like using proper measurements instead of just numbers. Instead of saying "cut the board to 48," you say "cut the board to 48 inches." The unit (inches) is part of the concept, and you can't accidentally add inches to degrees or compare a length to a weight.

### What This Means in Practice
- Create small classes for domain concepts like money, dates, IDs, measurements
- Make them immutable - once created, they can't change
- Add behavior that belongs to that concept (FileSize can check if it exceeds a limit)
- Use them everywhere instead of primitives

### Why You Should Care
Value objects prevent bugs by making invalid operations impossible. You can't accidentally add a file size to a user ID. They also centralize business rules - all file size logic lives in the FileSize class.

### Quick Sanity Check
"Am I passing around bare primitives that represent business concepts?"

### Bad Example (From Our User Model)
```php
// Primitive obsession - just bare integers everywhere
class User
{
    public function getTotalUploadSize(): int  // What unit? Bytes? MB?
    {
        return $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
    }
    
    public function updateUploadSizeTotal(int $totalUploadedSize): void
    {
        // Business logic scattered, units unclear
        $this->upload_size_total = $this->getTotalUploadSize() + $totalUploadedSize;
        $this->save();
    }
    
    public function getUploadLimit(): int  // Magic number with no context
    {
        return UPLOAD_LIMIT;
    }
}
```

### Good Example (From Our Better Implementation)
```php
// Rich value objects express business concepts
class User
{
    public function getTotalUploadSize(): FileSize  // Clear what this represents
    {
        // From: better/app/Models/User.php - actual implementation
        return once(
            function () {
                return FileSize::fromFiles(
                    $this->files
                );
            }
        );
    }
    
    public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
    {
        // Business operations using domain objects
        $this->upload_size_total = $this->getTotalUploadSize()
            ->add($totalUploadedSize)
            ->toBytes();
        $this->save();
    }
    
    public function canUpload(int $fileSize): bool
    {
        // Complex business rule encapsulated in value object behavior
        return !$this->getTotalUploadSize()
            ->add(FileSize::fromBytes($fileSize))
            ->exceedsLimit($this->getUploadLimit());
    }
}

// Value object with business behavior
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
    
    public function exceedsLimit(FileSize $limit): bool
    {
        return $this->bytes > $limit->bytes;
    }
    
    public function add(FileSize $other): self
    {
        return new self($this->bytes + $other->bytes);
    }
}
```

## DDD Concept 3: Entity Design

### The Basic Idea
Entities represent things in your domain that have identity and change over time. A User is an entity because they're still the same user even if their email changes. Focus on the business operations entities can perform, not just their data.

### Construction Analogy
Like a building under construction. The building has an identity (address, permit number) that stays the same even as materials, fixtures, and layouts change. The building can perform operations like "pass inspection," "calculate square footage," or "determine occupancy limits" based on its current state.

### What This Means in Practice
- Entities have unique identities (usually IDs)
- They can change state over time while maintaining identity
- Focus on what entities can DO, not just what data they contain
- Business operations belong on the entities that own the relevant data

### Why You Should Care
Rich entities encapsulate business logic where it naturally belongs. Instead of scattering user-related operations across multiple service classes, the User entity contains operations like "can this user upload files?" This makes code easier to find and maintain.

### Quick Sanity Check
"Does my entity contain meaningful business operations, or is it just a data container?"

### Bad Example (From Our User Model)
```php
// Anemic entity - mostly just data with basic getters/setters
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phonenumber', 'company_name', 'company_website',
        'job_title', 'password', 'organisation_id', 'verified'
    ];
    
    // Weak business operations - just data manipulation
    public function getTotalUploadSize(): int
    {
        return $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
    }
    
    // No real business intelligence
    public function getUploadLimit(): int
    {
        return UPLOAD_LIMIT;  // Just returns a constant
    }
}
```

### Good Example (From Our Better Implementation)
```php
// Rich entity with meaningful business operations
class User extends Authenticatable
{
    // Business-focused operations that make sense for a User
    public function canUpload(int $fileSize): bool
    {
        return !$this->getTotalUploadSize()
            ->add(FileSize::fromBytes($fileSize))
            ->exceedsLimit($this->getUploadLimit());
    }
    
    public function getTotalUploadSize(): FileSize
    {
        // From: better/app/Models/User.php - actual implementation
        return once(
            function () {
                return FileSize::fromFiles(
                    $this->files
                );
            }
        );
    }
    
    public function getUploadLimit(): FileSize
    {
        // Future: could be based on subscription, organization, etc.
        return new FileSize(UPLOAD_LIMIT);
    }
    
    public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
    {
        $this->upload_size_total = $this->getTotalUploadSize()
            ->add($totalUploadedSize)
            ->toBytes();
        $this->save();
    }
    
    // Role-based operations that make business sense
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }
}
```

## DDD Concept 4: Domain Services

### The Basic Idea
When business logic doesn't naturally belong to any single entity or value object, create a domain service. These are stateless objects that coordinate between multiple domain objects or perform complex operations that span several entities.

### Construction Analogy
Like specialized contractors who coordinate between different building systems. A "Building Inspector" service might need to check electrical work, plumbing, structural integrity, and safety systems to determine if a building passes inspection. No single system can make this determination alone.

### What This Means in Practice
- Domain services are stateless - they don't hold data
- They operate on entities and value objects passed to them
- They contain business logic that doesn't fit naturally into entities
- They coordinate complex operations across multiple domain objects

### Why You Should Care
Domain services prevent your entities from becoming bloated with operations that don't really belong to them. They also make complex business processes explicit and testable as separate units.

### Quick Sanity Check
"Does this business logic naturally belong to one entity, or does it need to coordinate between multiple objects?"

### Bad Example (From Our FileController)
```php
// Business logic scattered in controller, not in domain services
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        $user = auth()->user();
        
        // Upload limit checking logic in wrong place
        $totalUploadedSize = array_reduce($request->file('files'), 
            fn(int $carry, $file) => $carry += $file->getSize(), 0);

        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages([
                'max_upload_size' => 'Attachments file size exceeds upload limit.'
            ]);
        }
        
        // File processing logic mixed with everything else
        foreach ($request->file('files') as $file) {
            // Complex business operations in controller
        }
    }
}
```

### Good Example (From Our Better Implementation)
```php
// Domain service coordinating business operations
class AnswerService
{
    public function __construct(private Collection $handlers) {}

    /**
     * Coordinate answering a question with appropriate handler
     */
    public function answerQuestion(Question $question, array $data): void
    {
        $handler = $this->handlers->first(fn($h) => $h->canHandle($question));
        
        if (!$handler) {
            throw new InvalidArgumentException("No handler available for question type: {$question->type}");
        }
        
        $handler->handle($question, $data);
    }
}

// Specialized domain service for file upload business logic
class UploadAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        return $question->type === 'file_upload';
    }

    public function handle(Question $question, array $data): void
    {
        // Focused business operation
        $given_answer = $this->answerQuestion($question->id, true);
        $storedFiles = $this->storeFiles($data);
        $given_answer->files()->createMany($storedFiles);
    }
    
    /**
     * Business logic for storing files - separate from entity concerns
     */
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
```

## DDD Concept 5: Repository Pattern

### The Basic Idea
Repositories provide a domain-focused interface to your data storage. Instead of writing SQL or Eloquent queries everywhere, you ask the repository for what you need using business language: `$userRepository->findActiveSubscribers()` not `User::where('subscription_active', true)->get()`.

### Construction Analogy
Like a specialized parts warehouse for construction. Instead of builders having to know the exact storage locations, part numbers, and inventory systems, they simply ask the warehouse manager for "fire-rated drywall for residential construction" and get what they need. The repository handles all the storage complexity.

### What This Means in Practice
- Repositories hide data access implementation details
- They provide methods named using business language
- Complex queries are encapsulated with meaningful names
- Your domain logic doesn't know if data comes from MySQL, MongoDB, or APIs

### Why You Should Care
Repositories keep your domain logic free from database concerns. When you need to change how data is stored or add caching, you only modify the repository. Your business logic stays clean and focused.

### Quick Sanity Check
"Are my domain objects directly using database queries, or asking repositories for what they need?"

### Bad Example (From Our Models)
```php
// Domain logic mixed with data access concerns
// (This pattern exists in our current models)
class User extends Authenticatable
{
    public function getActiveApplications()
    {
        // Raw database queries mixed with business logic
        return $this->hasMany(Application::class)
                    ->where('status', 'active')
                    ->where('submitted_at', '>', now()->subDays(30))
                    ->orderBy('submitted_at', 'desc')
                    ->get();
    }
    
    public function getTotalUploadSize(): int
    {
        // Infrastructure concerns (Storage facade) in domain logic
        return $this->files->reduce(function (int $carry, File $file) {
            return $carry + Storage::disk('local')->size($file->path);
        }, 0);
    }
}
```

### Good Example (Proposed DDD Repository Improvement)
```php
// Repository with business-focused interface
// (Proposed improvement - this doesn't exist in current codebase)
interface UserRepositoryInterface
{
    public function findActiveSubscribers(): Collection;
    public function findByEmail(string $email): ?User;
    public function findUsersExceedingUploadLimit(): Collection;
}

class UserRepository implements UserRepositoryInterface
{
    public function findActiveSubscribers(): Collection
    {
        // Complex query hidden behind business-meaningful method name
        return User::whereHas('subscription', function ($query) {
            $query->where('status', 'active')
                  ->where('expires_at', '>', now());
        })->get();
    }
    
    public function findUsersExceedingUploadLimit(): Collection
    {
        return User::where('upload_size_total', '>', UPLOAD_LIMIT)->get();
    }
}

// Clean domain logic using repository
class User extends Authenticatable
{
    public function getTotalUploadSize(): FileSize
    {
        // From: better/app/Models/User.php - actual implementation
        return once(
            function () {
                return FileSize::fromFiles(
                    $this->files
                );
            }
        );
    }
}

// File entity handles its own storage concerns
// (Proposed improvement - this method doesn't exist in current codebase)
class File extends Model
{
    public function getStoredSize(): int
    {
        // Infrastructure concern encapsulated in the right place
        return Storage::disk('local')->size($this->path);
    }
}
```

## DDD Concept 6: Aggregate Design

### The Basic Idea
Aggregates are clusters of related entities and value objects that need to be kept consistent together. You always load, modify, and save aggregates as a unit. One entity in the aggregate is the "root" that controls access to everything inside.

### Construction Analogy
Like a building project where certain components must be coordinated together. When installing a kitchen, you don't randomly change the plumbing, electrical, and cabinetry separately - they're all part of the "Kitchen Installation" aggregate. The general contractor (aggregate root) coordinates all changes to ensure everything works together.

### What This Means in Practice
- Group entities that need to stay consistent together
- Only access internal entities through the aggregate root
- Load and save the entire aggregate as one unit
- Business rules that span multiple entities are enforced at the aggregate level

### Why You Should Care
Aggregates prevent data inconsistencies by ensuring related objects are always modified together. They also simplify your mental model - instead of juggling many individual entities, you think in terms of meaningful business units.

### Quick Sanity Check
"Are there entities in my system that should never be inconsistent with each other?"

### Bad Example (From Our Current Structure)
```php
// No aggregate boundaries - entities can be modified independently
// (This reflects current patterns in our codebase)
class Application extends Model
{
    // Application can be modified without considering its answers
}

class GivenAnswer extends Model
{
    // Answers can be created/modified independently of their application
    public function updateAnswer($newValue)
    {
        $this->answer = $newValue;
        $this->save(); // No validation that this makes sense for the application
    }
}

class File extends Model
{
    // Files can be added without checking application state
    public function addToApplication($applicationId)
    {
        $this->application_id = $applicationId;
        $this->save(); // No validation of application status or limits
    }
}

// Controller can modify pieces independently, causing inconsistency
class FileController
{
    public function store($request)
    {
        // Direct manipulation of aggregate internals
        $answer = GivenAnswer::updateOrCreate([...]);
        $answer->files()->updateOrCreate([...]);
        // What if the application is already submitted? No consistency checks!
    }
}
```

### Good Example (Proposed DDD Aggregate Improvement)
```php
// Application aggregate root controls access to internal entities
// (Proposed improvement - this design doesn't exist in current codebase)
class Application extends Model
{
    /**
     * Add supporting documents to this application
     * Enforces business rules at aggregate level
     */
    public function addSupportingDocuments(Question $question, array $files): void
    {
        if ($this->isSubmitted()) {
            throw new DomainException('Cannot add documents to submitted application');
        }
        
        if (!$this->hasQuestion($question)) {
            throw new DomainException('Question does not belong to this application');
        }
        
        // Aggregate ensures consistency between answer and files
        $answer = $this->getOrCreateAnswer($question);
        $answer->addFiles($files);
        
        // All changes saved together
        $this->save();
    }
    
    /**
     * Submit application - ensures all required questions are answered
     */
    public function submit(): void
    {
        if (!$this->isComplete()) {
            throw new DomainException('Cannot submit incomplete application');
        }
        
        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->save();
        
        // Could fire domain event here
        event(new ApplicationSubmitted($this));
    }
    
    private function isComplete(): bool
    {
        return $this->questions->every(fn($question) => 
            $this->hasAnswerFor($question) || !$question->isRequired()
        );
    }
    
    private function getOrCreateAnswer(Question $question): GivenAnswer
    {
        return $this->answers()->firstOrCreate([
            'question_id' => $question->id
        ]);
    }
}

// Controller works through aggregate root
// (Proposed improvement)
class ApplicationController
{
    public function addDocuments(Request $request, Application $application)
    {
        $question = Question::findOrFail($request->question_id);
        
        // Business operation through aggregate root
        $application->addSupportingDocuments($question, $request->file('files'));
        
        return response()->json(['message' => 'Documents added successfully']);
    }
}
```

## Key Improvements Through Domain Driven Design

Here's exactly how DDD principles transformed our file upload system:

### 1. **Business Language Replaces Technical Jargon**
- **Before**: `FormSubmission`, `maxFileSize`, generic method names
- **After**: `Application`, `FileSize`, `canUpload()`, domain-focused naming
- **Impact**: Business experts can read and understand the code structure

### 2. **Rich Value Objects Replace Primitive Obsession**
- **Before**: Bare `int` values for file sizes, magic numbers everywhere
- **After**: `FileSize` objects with business behavior like `exceedsLimit()`
- **Impact**: Impossible to mix up units, business rules centralized

### 3. **Rich Entities Replace Anemic Data Containers**
- **Before**: Models with just getters/setters and basic queries
- **After**: Entities with meaningful business operations like `canUpload()`
- **Impact**: Business logic lives where it naturally belongs

### 4. **Domain Services Coordinate Complex Operations**
- **Before**: Business logic scattered across controllers
- **After**: Focused services like `AnswerService` handling domain operations
- **Impact**: Complex business processes are explicit and testable

### 5. **Repository Pattern Hides Data Concerns**
- **Before**: Direct database queries mixed with business logic
- **After**: Business-focused repository interfaces abstracting storage
- **Impact**: Domain logic stays clean, storage implementation can change

### 6. **Aggregate Design Ensures Consistency**
- **Before**: Entities modified independently, causing data inconsistencies
- **After**: Related objects grouped in aggregates with enforced business rules
- **Impact**: Data stays consistent, business invariants are maintained

## Result

Following Domain Driven Design principles transforms the database-centric code into a business-focused system where:

- **Ubiquitous Language**: Every class and method name speaks the business domain
- **Value Objects**: Business concepts like FileSize encapsulate their own behavior
- **Rich Entities**: Models contain meaningful business operations, not just data
- **Domain Services**: Complex business processes are explicit and well-organized
- **Clean Repositories**: Data access is hidden behind domain-focused interfaces
- **Consistent Aggregates**: Related objects stay consistent through controlled access

The result is code that's not just technically sound, but actually represents how the business works - maintainable, understandable, and aligned with business needs.

## Quick Reference for Junior Developers

### Before Writing Any Code, Ask:

1. **Ubiquitous Language**: "Am I using the same terms the business experts use?"
2. **Value Objects**: "Am I passing around bare primitives that represent business concepts?"
3. **Entity Design**: "Does this entity contain meaningful business operations?"
4. **Domain Services**: "Does this business logic naturally belong to one entity?"
5. **Repository Pattern**: "Are my domain objects directly using database queries?"
6. **Aggregate Design**: "Are there entities that should never be inconsistent with each other?"

### Red Flags in Your Code:

- **Technical names** that business experts don't understand → Use ubiquitous language
- **Primitive obsession** (int, string everywhere) → Create value objects
- **Anemic entities** (just getters/setters) → Add business operations
- **Business logic in controllers** → Extract domain services
- **Direct database queries** in business logic → Use repository pattern
- **Entities modified independently** → Design proper aggregates

### Common Junior Developer Mistakes:

1. **Using technical terms instead of business language** - Ubiquitous language violation
2. **Primitive obsession with int/string everywhere** - Value object opportunity missed
3. **Creating data containers instead of rich entities** - Anemic domain model
4. **Putting business logic in controllers or services** - Domain logic belongs in domain
5. **Direct database access in domain logic** - Repository pattern violation
6. **Modifying related entities independently** - Aggregate boundary violation

## How to Spot These Problems in Your Own Code

### 🚨 **DDD Violation Warning Signs:**

**Your business experts can't understand your class names**
- Probably violates Ubiquitous Language principle
- Ask: "Do my class names match the terms business experts use?"

**You're passing around lots of int and string parameters**
- Violates Value Object principle
- Ask: "Do these primitives represent business concepts that should have their own classes?"

**Your entities are mostly just getters and setters**
- Probably violates Rich Entity Design
- Ask: "What business operations should this entity be able to perform?"

**Your controllers contain complex business logic**
- Violates Domain Service principle
- Ask: "Should this business logic be in a domain service instead?"

**Your models contain direct database queries**
- Violates Repository Pattern
- Ask: "Should this query be hidden behind a business-focused repository method?"

**Related entities can become inconsistent**
- Probably violates Aggregate Design
- Ask: "Are there entities that should always be modified together?"

### 💡 **Simple DDD Refactoring Steps for Beginners:**

**Week 1**: Start with ubiquitous language
- Listen to business experts and note their terminology
- Rename classes and methods to match business language
- Remove technical jargon from your domain code

**Week 2**: Create your first value objects
- Find places where you use int/string for business concepts
- Create small value objects like FileSize, EmailAddress, Money
- Add basic behavior to these objects

**Week 3**: Enrich your entities
- Add business operations to your entities
- Move business logic from services into appropriate entities
- Make entities do more than just hold data

**Week 4**: Extract domain services
- Find business logic that doesn't belong to any single entity
- Create domain services to coordinate between entities
- Keep services stateless and focused

**Week 5**: Implement repository pattern
- Create interfaces for your data access needs
- Hide complex queries behind business-meaningful method names
- Remove direct database queries from your domain logic

**Week 6**: Design proper aggregates
- Identify entities that need to stay consistent together
- Create aggregate roots to control access to internal entities
- Ensure related objects are always modified as a unit
