# General Design Principles Demonstration

The questionnaire website refactoring showcases how fundamental design principles transform messy, hard-to-maintain code into clean, professional software. The [Bad example](../example/bad/app) violates nearly every principle in the book, while the [Better example](../example/better/app) shows what happens when you actually follow these guidelines.

## What Are General Design Principles?

These are the basic rules that separate professional developers from people who just happen to write code that sometimes works. Think of them as the foundation that everything else builds on - SOLID principles, design patterns, architectural decisions, they all stem from these core ideas.

**The reality check**: Most junior developers learn syntax and frameworks, but skip these principles. That's why their code works on their machine but falls apart when anyone else touches it, or when requirements change, or when it needs to scale beyond a simple prototype.

## Key Concepts for Beginners

Before diving into the principles, let's clarify some terms you'll see throughout this guide:

**Value Objects**: Classes that represent a simple value (like money, file size, or email address). Instead of passing around raw strings or numbers, you create objects that know how to handle themselves. For example, `FileSize::fromBytes(1024)` is much clearer than just passing `1024` around.

**Dependency Injection**: Instead of creating objects inside your classes, you pass them in from outside. This makes testing easier and your code more flexible. Think of it like ordering food delivery instead of cooking everything yourself.

**Service Container**: Laravel's magic box that automatically creates and manages objects for you. When you type-hint a class in a method, Laravel figures out how to build it.

**Coupling**: How much your classes depend on each other. High coupling = classes are tightly connected and hard to change independently. Low coupling = classes can work on their own.

**Cohesion**: How focused a class is on one job. High cohesion = class does one thing well. Low cohesion = class tries to do everything and does nothing well.

**Method Chaining**: Calling methods on the result of other methods, like `object->getThing()->getOtherThing()->doSomething()`. Usually a sign you're reaching too deep into other objects.

## Learning Path for Juniors

**Master these first (Foundation):**

1. **KISS** - Keep your code simple, your future self will thank you
2. **DRY vs WET** - Stop copy-pasting code everywhere
3. **Separation of Concerns** - Each class/method should have one job

**Apply these constantly (Discipline):**

4. **YAGNI** - Don't build features you might need someday
5. **Law of Demeter** - Don't reach through objects to get to other objects
6. **Principle of Least Astonishment** - Your code should do what people expect

**Think with these (Mindset):**

7. **Tell Don't Ask** - Objects should do things, not just hold data
8. **Avoid Premature Optimization** - Make it work, then make it fast

## General Design Principles Analysis

### 1. Keep It Simple Stupid (KISS)

**The idea**: Simple solutions are better than clever ones. If you can't explain your code to a junior developer in five minutes, it's probably too complex.

**Real-world analogy**: Building a house with the right tool for each job. You could technically drive nails with a wrench, but a hammer is simpler, more effective, and less likely to break your wrist or the nail.

**What it means in code**: Choose straightforward solutions over clever ones. If you have a choice between a 5-line method and a 20-line method that does the same thing, choose the 5-line method. Break complex problems into smaller, easier-to-understand pieces.

**Why you should care**: 
- Simple code has fewer bugs
- Simple code is easier to change
- Simple code doesn't require a PhD to maintain

**Quick sanity check**: "Could a junior developer understand this in five minutes?"

**How the bad example screws this up**
The [`FileController`](../example/bad/app/Http/Controllers/FileController.php) tries to do everything in one massive method - authorization, validation, file handling, database operations, business logic. It's like having one construction worker trying to do electrical, plumbing, framing, and roofing all at the same time instead of using the right specialist for each job.

```php
// 80+ lines of mixed responsibilities
public function store(StoreFileRequest $request)
{
    // Authorization logic mixed with validation
    $user = auth()->user();
    $application = $user->application;
    Gate::authorize('update', [$application, $user->organisation->id]);
    
    // Additional validation mixed with business logic  
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'mimes:pdf|min:1|max:5120',
    ]);
    
    // Business logic calculations mixed with everything else
    $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
    
    if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
        throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
    }
    
    // Database operations scattered throughout
    foreach ($request->file('files') as $file) {
        $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
        
        $given_answer = GivenAnswer::updateOrCreate([
            'application_id' => $application->id,
            'question_id' => $request->questionId,
        ], ['answer' => true]);
        
        $given_answer->files()->updateOrCreate([
            'filename' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'path' => $filePath,
            'uuid' => Str::uuid(),
            'user_id' => $user->id
        ]);
    }
    
    // More business logic at the end
    $user->updateUploadSizeTotal($totalUploadedSize);
}
```

**How we fix this in the better example**
The [`FileController`](../example/better/app/Http/Controllers/FileController.php) does exactly one thing: coordinate the request handling. Each piece of logic lives in its own simple, focused place.

```php
public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
{
    $validatedData = $request->validated();
    
    $answerService->answerQuestion(
        Question::find($validatedData['questionId']),
        $request->file('files')
    );
    
    $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
}
```

Simple, readable, obvious.

### 2. Don't Repeat Yourself (DRY) vs Write Everything Twice (WET)

**The idea**: Every piece of knowledge should exist in exactly one place. When you find yourself copy-pasting code, that's a code smell.

**DRY vs WET explained**: 
- **DRY (Don't Repeat Yourself)**: Write each piece of logic once and reuse it
- **WET (Write Everything Twice)**: The anti-pattern where you duplicate code instead of abstracting it. Some developers joke that WET also stands for "We Enjoy Typing" because you end up writing the same logic over and over

**When WET is actually better than DRY**:
Sometimes duplication is the right choice! Consider WET when:
- **Different contexts with coincidental similarity**: Code that looks the same now but will evolve differently
- **Premature abstraction risk**: You're not sure if the similarity is real or just temporary
- **Simple, stable code**: The duplicated code is so simple that abstraction adds complexity without benefit
- **Different ownership**: Two teams need similar functionality but want to evolve it independently

**Rule of thumb**: Follow the "Rule of Three" - duplicate twice (WET), refactor on the third occurrence (DRY).

**Real-world analogy**: Building blueprints exist in one master copy. If the architect had five different blueprint sets for the same building, any change would require updating all copies - and someone would inevitably miss one, leading to construction errors.

**Why you should care**:
- Changes only need to happen in one place
- Bugs only need to be fixed once
- Your code becomes much more maintainable

**Quick sanity check**: 
- "Am I copying and pasting this code?"
- "Is this duplication because the code will evolve differently (WET OK) or because I'm being lazy (DRY needed)?"

**How the bad example screws this up (WET violations)**
Validation logic is duplicated between the controller and the form request. File size calculations are scattered in multiple places. Authorization checks happen in several different ways. This is classic WET code - the same logic written multiple times in different places.

**Laravel concepts explained for beginners:**
- **Form Requests**: Special classes that handle validation and authorization for incoming requests. Instead of validating in your controller, you create a separate class that handles it.
- **Gate::authorize()**: Laravel's way of checking if a user has permission to do something
- **ValidationException**: Laravel's way of throwing validation errors back to the user

```php
// In StoreFileRequest (bad example)
public function rules(): array
{
    return [
        'files.*' => ['required', File::types(['pdf'])->max('5mb')]
    ];
}

// In FileController (bad example) - DUPLICATE validation!
public function store(StoreFileRequest $request)
{
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'mimes:pdf|min:1|max:5120',  // Same rules, different syntax!
    ]);
    
    // File size calculation scattered everywhere
    $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
    
    // Upload limit checking duplicated logic
    if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
        throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
    }
}
``` 

**How we fix this in the better example (DRY implementation)**
- File size logic lives in the [`FileSize`](../example/better/app/ValueObjects/FileSize.php) value object
- Validation rules are centralized in [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php)
- Business logic is handled by [`AnswerService`](../example/better/app/Services/AnswerService.php)
- Upload limit checking is encapsulated in [`UploadLimit`](../example/better/app/Rules/UploadLimit.php) rule

Each piece of logic exists exactly once, in the most logical place. 

**Example of good WET vs bad WET**:
```php
// Good WET: Different contexts that happen to look similar
class UserUploadValidator {
    public function validate($fileSize) {
        return $fileSize <= $this->user->upload_limit;
    }
}

class OrganizationUploadValidator {
    public function validate($fileSize) {
        return $fileSize <= $this->organization->upload_limit;  // Looks the same but will evolve differently
    }
}

// Bad WET: True duplication that should be DRY
class FileController {
    public function store() {
        if ($file->size > 1024 * 1024 * 5) { throw new Exception('Too big'); }
    }
    
    public function update() {
        if ($file->size > 1024 * 1024 * 5) { throw new Exception('Too big'); }  // Same logic, same context
    }
}
```

### 3. Separation of Concerns

**The idea**: Different parts of your code should handle different types of problems. Don't mix user interface logic with business rules, or database code with validation.

**Real-world analogy**: A well-organized construction site has specialized teams: electricians handle wiring, plumbers handle pipes, carpenters handle framing. Imagine the chaos if the electrician was also trying to do plumbing and carpentry work.

**Why you should care**:
- Changes to one concern don't break unrelated code
- You can test each concern independently
- Different developers can work on different concerns without conflicts

**Quick sanity check**: "Is this class/method trying to do more than one type of thing?"

**How the bad example screws this up**
The `FileController` mixes everything together:
- HTTP concerns (handling the request)
- Business logic (processing uploads)
- Data access (saving to database)
- Authorization (checking permissions)
- Validation (verifying file types)

```php
// Bad: Everything mixed together in one method
public function store(StoreFileRequest $request)
{
    // HTTP concern: Getting request data
    $user = auth()->user();
    $application = $user->application;
    
    // Authorization concern: Checking permissions
    Gate::authorize('update', [$application, $user->organisation->id]);
    
    // Validation concern: Additional validation beyond form request
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'mimes:pdf|min:1|max:5120',
    ]);
    
    // Business logic concern: Processing the upload
    $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
    
    if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
        throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
    }
    
    // Data access concern: Saving to database
    foreach ($request->file('files') as $file) {
        $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
        
        $given_answer = GivenAnswer::updateOrCreate([
            'application_id' => $application->id,
            'question_id' => $request->questionId,
        ], ['answer' => true]);
        
        $given_answer->files()->updateOrCreate([
            'filename' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'path' => $filePath,
            'uuid' => Str::uuid(),
            'user_id' => $user->id
        ]);
    }
    
    // More business logic: Updating user stats
    $user->updateUploadSizeTotal($totalUploadedSize);
}
```

**How we fix this in the better example**
Each concern gets its own home:
- **HTTP concerns**: [`FileController`](../example/better/app/Http/Controllers/FileController.php) - just coordinates
- **Authorization**: [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php) - handles permissions
- **Validation**: [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php) and [`UploadLimit`](../example/better/app/Rules/UploadLimit.php) - verify data
- **Business logic**: [`AnswerService`](../example/better/app/Services/AnswerService.php) and [`UploadAction`](../example/better/app/Actions/Answer/UploadAction.php) - handle the actual work
- **Data representation**: [`FileSize`](../example/better/app/ValueObjects/FileSize.php) - encapsulates file size logic

```php
// Good: Each concern separated into its own class/method
class FileController extends Controller
{
    // HTTP concern only: coordinate the request
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        $validatedData = $request->validated(); // Authorization & validation handled in StoreFileRequest
        
        $answerService->answerQuestion(         // Business logic delegated to service
            Question::find($validatedData['questionId']),
            $request->file('files')
        );
        
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size'])); // Data updates via model
    }
}

// Authorization concern: StoreFileRequest
public function authorize(): bool
{
    return $this->user()->can('update', [$this->application, $this->user()->organisation_id]);
}

// Business logic concern: AnswerService
public function answerQuestion(Question $question, array $data): void
{
    $this->handlers->first(fn($h) => $h->canHandle($question))
        ->handle($question, $data);
}
```

### 4. You Aren't Gonna Need It (YAGNI)

**The idea**: Don't build features because you think you might need them someday. Build what you need right now, and add more when you actually need it.

**Real-world analogy**: Don't build a foundation that can support a 50-story skyscraper when you're only building a 2-story house. Build what you need now, and if you actually need to expand later, you can reinforce or rebuild the foundation then.

**What it means in code**: Only implement features you actually need right now. Don't build hooks for future extensibility, don't add configuration options "just in case," and don't create abstractions until you have at least two concrete examples.

**Why you should care**:
- Less code means fewer bugs
- Simpler codebase is easier to maintain
- You won't waste time on features nobody uses

**Quick sanity check**: "Do I need this feature right now, or am I just imagining I might need it?"

**How the bad example screws this up**
The bad example doesn't really violate YAGNI - it's too much of a mess to have unnecessary features. But many junior developers would look at this refactoring and think "I should build a generic file processor that can handle any type of upload for any purpose."

```php
// Bad: Over-engineering for imaginary future needs
class UniversalFileProcessor 
{
    public function process($files, $type = 'upload', $destination = 'local', $transformations = [], $callbacks = [])
    {
        // 200+ lines of code handling every possible scenario
        // - Different storage drivers (local, S3, FTP, SFTP, Dropbox)
        // - Image transformations (resize, crop, watermark, filters)
        // - Document conversions (PDF to Word, image to PDF)
        // - Video processing (compression, format conversion)
        // - Audio processing (compression, format conversion)
        // - Virus scanning integration
        // - OCR text extraction
        // - Metadata extraction
        // - Thumbnail generation
        // - Progress callbacks
        // - Retry mechanisms
        // - Rate limiting
        // - Queue integration
        // ... all for a simple file upload feature
    }
}
```

**How we fix this in the better example**
The polymorphic action system (`AnswerAction`, `UploadAction`) might look like over-engineering, but it's actually solving a real, current need - the system already has different types of questions that need different handling. The architecture supports exactly what's needed:
- File upload questions (implemented with [`UploadAction`](../example/better/app/Actions/Answer/UploadAction.php))
- Room for other question types that already exist in the system

It's not building for imaginary future needs - it's organizing current complexity.

**What "polymorphic action system" means for beginners:**
Think of it like having different workers in a factory. Each worker (action class) knows how to handle their specific job, but they all follow the same basic instructions (interface). The supervisor (AnswerService) doesn't need to know the details of each job - they just say "handle this question" and the right worker takes over.

```php
// The interface defines what every action must do
interface AnswerAction {
    public function canHandle(Question $question): bool;
    public function handle(Question $question, array $data): void;
}

// Each action handles its specific question type
class UploadAction implements AnswerAction { /* handles file uploads */ }
class TextAction implements AnswerAction { /* would handle text questions */ }
class MultipleChoiceAction implements AnswerAction { /* would handle multiple choice */ }
```

```php
// Good: Building only what's needed right now
class UploadAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        // Check if the question type is 'file_upload'
        return $question->type === 'file_upload';
    }
    
    public function handle(Question $question, array $data): void
    {
        // Only handles file uploads - the current requirement
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

// The architecture allows for future question types WITHOUT over-engineering
// When we actually need TextAction or MultipleChoiceAction, we'll add them then
```

### 5. Law of Demeter (Don't Talk to Strangers)

**The idea**: An object should only talk to its direct friends, not to friends of friends. Don't chain method calls through multiple objects.

**Real-world analogy**: In a well-organized company, if you need office supplies, you ask the office manager directly, not the office manager's assistant's intern who might know where the supply closet key is kept.

**Why you should care**:
- Changes deep in the object graph don't break your code
- Your code is less coupled to implementation details
- It's much easier to test and mock

**Quick sanity check**: "Am I chaining method calls like `object->getThing()->getOtherThing()->doSomething()`?"

**What method chaining looks like and why it's problematic:**
```php
// Bad: Reaching through multiple objects (method chaining)
$user->getProfile()->getAddress()->getCity()->getName()
$order->getCustomer()->getBillingAddress()->getZipCode()
$file->getMetadata()->getUploader()->getOrganisation()->getName()
```

If any object in that chain changes its structure, your code breaks. Plus, you're depending on the internal structure of multiple classes.

**How the bad example screws this up**
Lots of reaching through object relationships and accessing properties directly instead of asking objects to do things for themselves.

**How we fix this in the better example**
- The controller asks the `AnswerService` to handle the business logic, rather than reaching into file objects
- The `FileSize` value object encapsulates file size operations instead of exposing raw calculations
- The `User` model provides a `canUpload()` method instead of exposing upload limit calculations

```php
// Bad: Reaching through relationships
if ($user->uploadLimit->remaining < $file->size) { ... }

// Good: Asking the object to do its job
if (!$user->canUpload($fileSize)) { ... }
```

### 6. Principle of Least Astonishment

**The idea**: Your code should do what people expect it to do. Method names should be descriptive, behavior should be predictable, and there shouldn't be hidden side effects.

**Real-world analogy**: When you press the elevator call button, you expect it to bring an elevator to your floor. You don't expect it to also turn on the building's sprinkler system and lock all the office doors.

**Why you should care**:
- Other developers (including future you) can understand the code quickly
- Fewer surprises mean fewer bugs
- Code reviews go faster when everything is obvious

**Quick sanity check**: "Would another developer be surprised by what this method does?"

**How the bad example screws this up**
The `store` method does way more than "store" - it validates, authorizes, processes, calculates, and updates multiple models. The name doesn't match the complexity.

```php
// Bad: Method names that don't match what they actually do
class FileController 
{
    public function store(StoreFileRequest $request)  // "store" but does 80+ lines of everything
    {
        // Authorization (not storing!)
        $user = auth()->user();
        Gate::authorize('update', [$application, $user->organisation->id]);
        
        // Validation (not storing!)
        $request->validate(['files' => 'required|array', 'files.*' => 'mimes:pdf|min:1|max:5120']);
        
        // Business logic (not storing!)
        $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
        }
        
        // Multiple database operations (way more than just "storing")
        foreach ($request->file('files') as $file) {
            $given_answer = GivenAnswer::updateOrCreate([...], ['answer' => true]);
            $given_answer->files()->updateOrCreate([...]);  // Complex logic hidden in "store"
        }
        
        // Finally... some actual storing, but also more business logic
        $user->updateUploadSizeTotal($totalUploadedSize);  // Surprise! User updates too
    }
}
```

**How we fix this in the better example**
- `store()` method stores files - no surprises
- `answerQuestion()` answers a question - obvious
- `canUpload()` checks if upload is allowed - clear
- `fromBytes()` creates a FileSize from bytes - predictable

Each method does exactly what its name suggests.

```php
// Good: Method names that clearly describe their single responsibility
class FileController 
{
    public function store(StoreFileRequest $request, User $user, AnswerService $answerService)
    {
        // Does exactly what "store" suggests - coordinates file storage
        $validatedData = $request->validated();
        
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );
        
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}

class FileSize
{
    public static function fromBytes(int $bytes): self      // Creates from bytes - obvious
    public static function fromFiles(array $files): self   // Creates from files array - clear
    public function toBytes(): int                         // Converts to bytes - predictable
    public function exceedsLimit(FileSize $limit): bool    // Compares to limit - no surprises
    public function add(FileSize $other): self             // Adds another FileSize - obvious
}

class User
{
    public function canUpload(int $fileSize): bool         // Checks permission - clear intent
    public function getTotalUploadSize(): FileSize         // Gets current total - no side effects
    public function updateUploadSizeTotal(FileSize $size): void  // Updates total - does what it says
    public function getUploadLimit(): FileSize             // Gets limit - predictable
}
```

### 7. Tell Don't Ask

**The idea**: Instead of asking an object for its data and then acting on it, tell the object what you want it to do. Objects should be responsible for their own behavior.

**Real-world analogy**: Instead of micromanaging every detail ("Check your schedule, see if you're free, calculate how long this will take, then decide if you can do it"), you give clear instructions to competent team members ("Please handle the client presentation") and trust them to manage their own responsibilities.

**Why you should care**:
- Objects encapsulate their own business rules
- Less code duplication when behavior is centralized
- Easier to maintain when logic lives in the right place

**Quick sanity check**: "Am I asking for data just so I can make decisions about it?"

**How the bad example screws this up**
The controller asks the user for their upload limit, asks files for their sizes, then makes decisions about whether the upload should be allowed.

**How we fix this in the better example**
```php
// Instead of asking and deciding:
if ($user->getUploadLimit() < $user->getCurrentUpload() + $fileSize) {
    // reject
}

// Tell the object what you want:
if (!$user->canUpload($fileSize)) {
    // reject  
}
```

The `User` model knows its own upload rules. The `FileSize` object knows how to handle size calculations. Objects do their own jobs.

### 8. Avoid Premature Optimization

**The idea**: Make your code work correctly first, then make it fast if you need to. Don't optimize for performance problems you don't actually have.

**Real-world analogy**: Don't spend weeks designing the most efficient workflow for a task that only takes five minutes and happens once a month. Focus your optimization efforts where they'll actually make a difference.

**Why you should care**:
- Premature optimization often makes code more complex without meaningful benefits
- You might optimize the wrong things
- Correct, maintainable code is more valuable than fast, broken code

**Quick sanity check**: "Do I have actual performance problems, or am I just imagining them?"

**How the bad example screws this up**
It's not optimized at all - but that's not the problem. The problem is that it's unmaintainable.

**How we fix this in the better example**
The refactored code prioritizes clarity and maintainability over micro-optimizations:
- Uses value objects like `FileSize` even though they add slight overhead
- Implements polymorphic actions even though a simple service would be "faster"
- Uses dependency injection even though it's not the most performant option

But there's one smart optimization: Laravel's `once()` helper in the `User` model ensures expensive upload size calculations only happen once per request.

```php
public function getTotalUploadSize(): FileSize
{
    return once(function () {
        // Expensive calculation that's cached for the request
        return FileSize::fromBytes($this->files()->sum('size'));
    });
}
```

This is good optimization because:
1. It solves a real performance issue (repeated expensive calculations)
2. It doesn't sacrifice code clarity
3. It's easy to remove if requirements change

## Key Improvements Through General Design Principles

### Simplified Controller Architecture
The [`FileController`](../example/better/app/Http/Controllers/FileController.php) transformation demonstrates KISS in action - from 80+ lines of mixed responsibilities to a focused 8-line coordination method:

```php
public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
{
    $validatedData = $request->validated();
    
    $answerService->answerQuestion(
        Question::find($validatedData['questionId']),
        $request->file('files')
    );
    
    $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
}
```

### Value Object Introduction
DRY principle led to creating the [`FileSize`](../example/better/app/ValueObjects/FileSize.php) value object, eliminating repeated file size calculations and providing a single source of truth:

```php
class FileSize
{
    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }
    
    public function inMegabytes(): float
    {
        return round($this->bytes / 1024 / 1024, 2);
    }
}
```

### Separation of Concerns Implementation
Each responsibility is now handled by the appropriate class:
- **Request handling**: [`FileController`](../example/better/app/Http/Controllers/FileController.php)
- **Validation & authorization**: [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php)
- **Business logic**: [`AnswerService`](../example/better/app/Services/AnswerService.php)
- **File operations**: [`UploadAction`](../example/better/app/Actions/Answer/UploadAction.php)
- **Data representation**: [`FileSize`](../example/better/app/ValueObjects/FileSize.php)

## Result

Following these general design principles transforms the chaotic 80+ line controller method into a clean, professional system where:

- **KISS**: Each class has one simple job and does it well
- **DRY**: Logic exists in exactly one place, eliminating duplication
- **Separation of Concerns**: Different types of problems are handled by appropriate specialists
- **YAGNI**: Only builds what's actually needed right now, avoiding over-engineering
- **Law of Demeter**: Objects ask their direct collaborators to do work, reducing coupling
- **Least Astonishment**: Methods do exactly what their names suggest, making code predictable
- **Tell Don't Ask**: Objects encapsulate their own behavior instead of exposing data
- **Avoid Premature Optimization**: Prioritizes maintainability with smart performance choices where needed

The result is code that's not just working, but truly professional - maintainable, extensible, and reliable. This is the difference between code that "happens to work" and code that can evolve with changing requirements.

## Quick Reference for Junior Developers

### Before Writing Any Code, Ask:
1. **KISS**: "Is this the simplest solution that could work?"
2. **DRY**: "Have I written this logic somewhere else already?"
3. **Separation of Concerns**: "Is this class trying to do more than one type of thing?"
4. **YAGNI**: "Do I actually need this feature right now?"
5. **Law of Demeter**: "Am I reaching through multiple objects to get what I need?"
6. **Least Astonishment**: "Would this method's behavior surprise another developer?"
7. **Tell Don't Ask**: "Should this object be doing this work itself?"
8. **Premature Optimization**: "Am I optimizing for a performance problem I don't actually have?"

### Red Flags in Your Code:
- **Long methods or classes** → Break them down (KISS, Separation of Concerns)
- **Copy-pasted code** → Extract to shared location (DRY)
- **Classes doing multiple unrelated things** → Split responsibilities (Separation of Concerns)
- **Building features "for later"** → Focus on current requirements (YAGNI)
- **Chained method calls** → Ask direct collaborators instead (Law of Demeter)
- **Confusing method names** → Make behavior obvious (Least Astonishment)
- **Asking for data to make decisions** → Let objects handle their own logic (Tell Don't Ask)
- **Complex performance optimizations** → Make it work first, then make it fast (Avoid Premature Optimization)

### Common Junior Developer Mistakes:
1. **Over-engineering simple problems** - KISS principle violation
2. **Copy-pasting instead of extracting** - DRY principle violation
3. **Building "flexible" solutions for imaginary future needs** - YAGNI violation
4. **Mixing different types of logic in one place** - Separation of concerns violation
5. **Optimizing before measuring** - Premature optimization trap

### 💡 **Simple Design Principles Refactoring Steps for Beginners:**

**Week 1**: Start with KISS
- Look for methods longer than 20 lines and break them down
- Extract complex logic into smaller, well-named methods
- Focus on making your code readable first

**Week 2**: Apply DRY/WET wisely
- Find duplicated code and extract it to shared methods
- Be careful not to DRY too early - sometimes duplication is better than the wrong abstraction
- Practice identifying when code is "coincidentally similar" vs "fundamentally the same"

**Week 3**: Separate concerns
- Identify classes that do multiple things
- Extract one responsibility at a time (start with the easiest)
- Move related methods and data together

**Week 4**: Practice YAGNI
- Remove unused code and "future-proofing" features
- When adding new features, ask "Do I need this right now?"
- Learn to resist building extensibility you don't currently need

**Week 5**: Apply Law of Demeter and Tell Don't Ask
- Look for long method chains and refactor them
- Move logic closer to the data it operates on
- Practice asking objects to do work instead of asking for data

**Week 6**: Focus on clarity (Least Astonishment)
- Review your method names - do they match what the methods actually do?
- Simplify confusing logic and add explanatory comments
- Make your code's intention obvious

Remember: These principles work together, but you don't need to master them all at once. Start with KISS and DRY, then gradually incorporate the others as you become more comfortable.

## How to Spot These Problems in Your Own Code

As a junior developer, here are practical warning signs to watch for:

### 🚨 **Design Principles Violation Warning Signs:**

**Your method is getting long** (20+ lines)
- Probably violates KISS and Separation of Concerns
- Ask: "Can I break this into smaller, focused methods?"

**You're copy-pasting code**
- Violates DRY principle
- Ask: "Can I extract this into a shared method or class?"

**Your class name has "And" or "Manager" in it**
- Probably violates Separation of Concerns
- Ask: "Is this class trying to do multiple jobs?"

**You're calling lots of methods in a chain**
- Violates Law of Demeter
- Ask: "Should the object I'm calling handle this for me?"

**Your method name doesn't match what it does**
- Violates Principle of Least Astonishment
- Ask: "Would someone be surprised by what this method actually does?"

**You're building features "just in case"**
- Violates YAGNI
- Ask: "Do I actually need this feature right now?"
