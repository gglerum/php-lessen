# SOLID Principles Demonstration

The questionnaire website refactoring perfectly demonstrates how SOLID principles transform chaotic, unmaintainable code into professional, extensible software. The [Bad example](../example/bad/app) violates every SOLID principle imaginable, while the [Better example](../example/better/app) shows what happens when you actually follow these guidelines.

## What Are SOLID Principles?

SOLID is an acronym for five design principles that make your code more maintainable, flexible, and understandable. Think of them as the advanced rules that build on top of general design principles - once you understand KISS, DRY, and Separation of Concerns, SOLID teaches you how to organize your classes and interfaces professionally.

**The reality check**: Most developers can write working code, but SOLID principles separate code that "just works" from code that can evolve with changing requirements. Every major framework and well-designed application follows these principles, whether consciously or not.

## Key Concepts for Beginners

Before diving into SOLID, let's clarify some terms you'll see throughout this guide:

**Abstractions**: Interfaces and abstract classes that define contracts without implementation details. Think of them as job descriptions - they tell you what needs to be done, not how to do it.

**Inheritance**: When one class extends another, inheriting its properties and methods. Like a specialist job role that builds on a general one.

**Polymorphism**: Different classes implementing the same interface in their own way. Like different construction workers all implementing "build foundation" differently based on their specialty.

**Dependency**: When one class needs another class to work. Like a building's electrical system depending on a power source.

**Interface**: A contract that defines what methods a class must have, without saying how they work. Like building blueprints - they specify what rooms are needed but not how to build them.

**Dependency Injection**: Instead of a class creating its own dependencies, they're "injected" (passed in) from outside. Like a construction worker being given the tools they need rather than buying their own.

**Why these concepts matter**: As a junior developer moving beyond basic OOP, these concepts let you build flexible, professional software that can handle changing requirements without breaking.

## Learning Path for Juniors

**Start here (Foundation):**

1. **Single Responsibility** - Each class should have one reason to change
2. **Dependency Inversion** - Depend on abstractions, not concrete implementations

**Build on these (Structure):**

3. **Open/Closed** - Open for extension, closed for modification
4. **Interface Segregation** - Many small interfaces are better than one large one

**Master this (Advanced):**

5. **Liskov Substitution** - Subclasses should be replaceable with their parent classes

## SOLID Principles Analysis

### 1. Single Responsibility Principle (SRP)

**The idea**: A class should have only one reason to change. Each class should be responsible for one thing and do it well.

**Real-world analogy**: A construction worker specializes in one trade - an electrician handles wiring, a plumber handles pipes, a carpenter handles framing. You wouldn't ask the electrician to also do plumbing and roofing just because they're all "construction work."

**What it means in code**: Each class should have exactly one reason to change. When you're deciding where to put a new method or responsibility, ask yourself: "If this thing changes, would it affect other responsibilities in this class?" If yes, it probably belongs elsewhere.

**Why you should care**:
- Changes to one feature don't break unrelated features
- Classes are easier to understand and test
- You can work on different parts of the system independently

**Quick sanity check**: "If I had to change this class, would it be for one specific reason, or multiple different reasons?"

**How the bad example screws this up**
The [`FileController`](../example/bad/app/Http/Controllers/FileController.php) has multiple responsibilities:
- HTTP request handling
- Authorization logic
- File validation
- Business logic processing
- Database operations
- File storage management

```php
// Bad: One class doing everything (multiple responsibilities)
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Responsibility 1: User authentication
        $user = auth()->user();
        
        // Responsibility 2: Authorization logic
        Gate::authorize('update', [$application, $user->organisation->id]);
        
        // Responsibility 3: Validation logic
        $request->validate(['files' => 'required|array', 'files.*' => 'mimes:pdf|min:1|max:5120']);
        
        // Responsibility 4: Business logic calculations
        $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
        
        // Responsibility 5: Upload limit checking
        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
        }
        
        // Responsibility 6: File storage
        // Responsibility 7: Database operations
        // ... 80+ lines of mixed concerns
    }
}
```

This class would need to change if:
- HTTP handling changes
- Authorization rules change
- Validation rules change
- Business logic changes
- Storage logic changes
- Database schema changes

**How we fix this in the better example**
Each class has a single, clear responsibility:
- [`FileController`](../example/better/app/Http/Controllers/FileController.php): Only handles HTTP coordination
- [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php): Only handles request validation and authorization
- [`AnswerService`](../example/better/app/Services/AnswerService.php): Only coordinates business logic
- [`UploadAction`](../example/better/app/Actions/Answer/UploadAction.php): Only handles file upload business logic
- [`FileSize`](../example/better/app/ValueObjects/FileSize.php): Only represents and manipulates file sizes

```php
// Good: Single responsibility - only coordinates HTTP requests
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        $validatedData = $request->validated();
        
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );
        
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}
```

Now each class has exactly one reason to change.

**Step-by-step refactoring for beginners:**
If you find yourself with a "god class" like the bad FileController, here's how to refactor it:

1. **Identify responsibilities**: List everything the class does (HTTP, auth, validation, business logic, storage)
2. **Extract one at a time**: Start with the easiest - move validation to a Request class
3. **Create service classes**: Move business logic to dedicated service classes
4. **Use dependency injection**: Instead of creating dependencies inside methods, pass them in
5. **Test as you go**: Each extraction should leave the system working the same way

The key is doing this gradually - don't try to refactor everything at once!

### 2. Open/Closed Principle (OCP)

**The idea**: Classes should be open for extension but closed for modification. You should be able to add new functionality without changing existing code.

**Real-world analogy**: A well-designed building has standardized connection points - plumbing fixtures connect to standard pipe fittings, electrical devices connect to standard outlets. You can add new fixtures or appliances (open for extension) without rewiring the building's core infrastructure (closed for modification).

**What it means in code**: You should be able to add new features by creating new classes that implement existing interfaces, rather than modifying the classes that already work. This is usually achieved through interfaces, abstract classes, and design patterns.

**Why you should care**:
- Adding new features doesn't risk breaking existing functionality
- You can extend behavior without touching tested code
- Different developers can add features without conflicts

**Quick sanity check**: "Can I add new behavior without modifying existing classes?"

**How the bad example screws this up**
To add a new question type (like text questions), you'd have to modify the existing controller, adding more if/else statements and making it even more complex.

```php
// Bad: Violates Open/Closed - needs modification for each new question type
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Existing file upload logic...
        
        // To add text questions, you'd need to add this here:
        // if ($questionType === 'text') {
        //     // Text handling logic
        // } elseif ($questionType === 'multiple_choice') {
        //     // Multiple choice logic
        // }
        // This means modifying existing, working code
    }
}
```

**How we fix this in the better example**
The action system allows new question types to be added without modifying existing code:

```php
// Good: Contract that enables extension without modification
interface AnswerAction
{
    public function canHandle(Question $question): bool;
    public function handle(Question $question, array $data): void;
}

// Existing action - this never needs to change
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

// New functionality - just add a new class, don't modify existing ones
class TextAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        return $question->type === 'text';
    }
    
    public function handle(Question $question, array $data): void
    {
        // Text question logic - completely new code, existing classes unchanged
        $given_answer = $this->answerQuestion($question->id, true);
        // Store text answer logic here
    }
}

// Service automatically works with new action types - no modifications needed
class AnswerService
{
    public function __construct(private Collection $handlers) {}
    
    public function answerQuestion(Question $question, array $data): void
    {
        $this->handlers->first(fn($h) => $h->canHandle($question))
            ->handle($question, $data);
    }
}
```

Adding new question types requires zero changes to existing classes.

### 3. Liskov Substitution Principle (LSP)

**The idea**: Objects of a subclass should be replaceable with objects of the parent class without breaking the application. If you have a contract, all implementations should honor that contract.

**Real-world analogy**: In a well-designed building, any standard window can replace another standard window of the same size. Whether it's single-pane, double-pane, or triple-pane glass, each fulfills the basic contract of being a "window" (lets in light, provides weather protection) even though they have different properties.

**What it means in code**: If you have a parent class or interface, you should be able to replace it with any of its subclasses without breaking your application. All implementations should honor the same contract - same parameters, same return types, same expected behavior.

**Why you should care**:
- Your code becomes more reliable and predictable
- You can swap implementations without fear of breaking things
- Unit testing becomes much easier

**Quick sanity check**: "Can I replace this subclass with its parent class without breaking anything?"

**How the bad example screws this up**
The bad example doesn't really use inheritance properly, so LSP violations aren't obvious. But a common violation would be:

```php
// Bad: Violates LSP - subclass changes expected behavior
class FileValidator
{
    public function validate($file)
    {
        return $file->getSize() < 5000000; // Returns boolean
    }
}

class StrictFileValidator extends FileValidator
{
    public function validate($file)
    {
        if ($file->getSize() > 5000000) {
            throw new Exception('File too large'); // Throws exception instead of returning boolean!
        }
        return true;
    }
}

// This breaks when you substitute:
function processFile($file, FileValidator $validator)
{
    if ($validator->validate($file)) { // Expects boolean, might get exception
        // Process file
    }
}
```

**How we fix this in the better example**
All action classes properly implement the `AnswerAction` contract from the actual codebase:

```php
// Good: Contract that all implementations must honor
interface AnswerAction
{
    public function canHandle(Question $question): bool;  // Always returns boolean
    public function handle(Question $question, array $data): void;  // Always returns void
}

// Real implementation from the better example
class UploadAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        return $question->type === 'file_upload';  // Returns boolean as promised
    }
    
    public function handle(Question $question, array $data): void
    {
        $given_answer = $this->answerQuestion($question->id, true);
        $storedFiles = $this->storeFiles($data);
        $given_answer->files()->createMany($storedFiles);
        // Returns void as promised, no exceptions thrown
    }
}

// Another implementation could be added that honors the same contract
class TextAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        return $question->type === 'text';  // Returns boolean as promised
    }
    
    public function handle(Question $question, array $data): void
    {
        // Would handle text questions, returns void as promised
        // Could never throw exceptions or return different types
    }
}

// Service can reliably use any implementation
class AnswerService
{
    public function answerQuestion(Question $question, array $data): void
    {
        $handler = $this->handlers->first(fn($h) => $h->canHandle($question));
        // We know canHandle() returns boolean and handle() returns void
        $handler->handle($question, $data);
    }
}
```

Every action implementation can be substituted for the interface without surprises.

### 4. Interface Segregation Principle (ISP)

**The idea**: No client should be forced to depend on methods it doesn't use. Create small, focused interfaces rather than large, monolithic ones.

**Real-world analogy**: A building's control systems should be specialized - the HVAC control panel should only have climate controls, the security panel should only have security controls, the lighting panel should only have lighting controls. You wouldn't want one massive control panel with 200 switches for everything in the building.

**What it means in code**: Instead of one large interface with many methods, create smaller interfaces that group related functionality. Classes should only implement the interfaces they actually need, not be forced to implement methods they'll never use.

**Why you should care**:
- Classes only implement what they actually need
- Changes to unused features don't affect your classes
- Interfaces are easier to understand and implement

**Quick sanity check**: "Does this interface force implementers to add methods they'll never use?"

**How the bad example screws this up**
A typical ISP violation would be a giant interface that forces all implementations to handle everything:

```php
// Bad: Fat interface forces implementations to handle everything
interface QuestionHandler
{
    public function handleFileUpload(array $files): void;
    public function handleTextInput(string $text): void;
    public function handleMultipleChoice(array $choices): void;
    public function handleDateInput(DateTime $date): void;
    public function handleRating(int $rating): void;
}

// Implementations forced to implement unused methods
class FileUploadHandler implements QuestionHandler
{
    public function handleFileUpload(array $files): void
    {
        // This is the only method we actually need
    }
    
    // Forced to implement these even though we'll never use them
    public function handleTextInput(string $text): void { throw new NotImplementedException(); }
    public function handleMultipleChoice(array $choices): void { throw new NotImplementedException(); }
    public function handleDateInput(DateTime $date): void { throw new NotImplementedException(); }
    public function handleRating(int $rating): void { throw new NotImplementedException(); }
}
```

**How we fix this in the better example**
Small, focused interfaces that only define what's actually needed:

```php
// Good: Small, focused interface from the actual codebase
interface AnswerAction
{
    public function canHandle(Question $question): bool;
    public function handle(Question $question, array $data): void;
}

// If we needed more specific interfaces, we'd create them separately:
interface FileProcessor
{
    public function processFiles(array $files): array;
}

interface TextValidator
{
    public function validateText(string $text): bool;
}

// Real implementation only depends on what it actually uses
class UploadAction extends AnswerAction
{
    // Only implements the two methods it actually needs
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

// If an action needed file processing, it could implement both
class AdvancedUploadAction extends AnswerAction implements FileProcessor
{
    // Chooses which interfaces it needs, implements both
    public function canHandle(Question $question): bool { /* ... */ }
    public function handle(Question $question, array $data): void { /* ... */ }
    public function processFiles(array $files): array { /* ... */ }
}
```

Each interface is focused on one specific concern.

### 5. Dependency Inversion Principle (DIP)

**The idea**: Don't depend on specific implementations - depend on contracts (interfaces) instead. This lets you swap out implementations easily.

**Simpler explanation for beginners**: Instead of your code saying "I need a MySQL database," it should say "I need something that can store data." This way you can later switch to PostgreSQL, MongoDB, or even a test database without changing your main code.

**Real-world analogy**: A building's electrical system uses standardized interfaces - outlets, junction boxes, and panels follow standard specifications. The lighting doesn't care whether power comes from the main grid, backup generator, or solar panels - it just needs to connect to the standard electrical interface. The power source can change without affecting the building's electrical systems.

**What it means in code**: Your classes should depend on interfaces (contracts) rather than concrete implementations. Instead of saying "I need a MySQL database," your code should say "I need something that can store data." This makes testing easier and allows you to swap implementations.

**Why you should care**:
- You can swap implementations easily (database, payment processor, etc.)
- Testing becomes much easier with mock objects
- Your code is more flexible and less brittle

**Quick sanity check**: "Am I depending on concrete classes, or on interfaces/abstractions?"

**Simple progression example for beginners:**
```php
// Step 1: Tightly coupled (bad) - depends on specific implementations
class OrderProcessor
{
    public function processOrder($order)
    {
        $emailSender = new EmailSender();  // Directly creates dependency
        $paymentGateway = new StripePayment();  // Locked into Stripe
        
        // Processing logic...
    }
}

// Step 2: Use interfaces (better)
interface PaymentGateway { public function charge($amount); }
interface NotificationSender { public function send($message); }

class OrderProcessor
{
    public function __construct(
        private PaymentGateway $payment,      // Depends on interface
        private NotificationSender $notifications  // Can be any implementation
    ) {}
    
    public function processOrder($order)
    {
        // Same logic, but now we can swap implementations
        $this->payment->charge($order->total);
        $this->notifications->send("Order processed");
    }
}

// Step 3: Now you can easily swap implementations
$processor = new OrderProcessor(
    new PayPalPayment(),  // Different payment gateway
    new SMSNotifier()     // Different notification method
);
```

**How the bad example screws this up**
The bad controller directly creates and depends on concrete classes:

```php
// Bad: Depends on concrete implementations
class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        // Directly depends on concrete User model
        $user = auth()->user();
        
        // Directly depends on Laravel's Gate
        Gate::authorize('update', [$application, $user->organisation->id]);
        
        // Directly depends on specific validation implementation
        $request->validate(['files' => 'required|array']);
        
        // Directly depends on GivenAnswer model
        $given_answer = GivenAnswer::updateOrCreate([...]);
        
        // Hard to test, hard to change implementations
    }
}
```

**How we fix this in the better example**
The better example depends on abstractions and uses dependency injection:

```php
// Good: Depends on abstractions via dependency injection
class FileController extends Controller
{
    public function store(
        StoreFileRequest $request,           // Injected dependency (abstraction)
        #[CurrentUser] User $user,          // Injected dependency
        AnswerService $answerService        // Injected dependency (abstraction)
    ) {
        $validatedData = $request->validated();
        
        // Depends on AnswerService abstraction, not concrete implementations
        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );
        
        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}

// Service depends on collection of action interfaces, not concrete classes
class AnswerService
{
    public function __construct(private Collection $handlers) {}  // Depends on abstraction
    
    public function answerQuestion(Question $question, array $data): void
    {
        // Works with any implementation of AnswerAction interface
        $this->handlers->first(fn($h) => $h->canHandle($question))
            ->handle($question, $data);
    }
}

// Easy to test with mock implementations
class AnswerServiceTest extends TestCase
{
    public function test_answers_question()
    {
        $mockHandler = Mockery::mock(AnswerAction::class);
        $service = new AnswerService(collect([$mockHandler]));
        // Test with mock instead of real implementations
    }
}
```

The high-level controller doesn't know or care about low-level implementation details.

## Result

Following SOLID principles transforms the chaotic 80+ line controller into a clean, professional system where:

- **Single Responsibility**: Each class has one job and does it well
- **Open/Closed**: New features can be added without modifying existing code
- **Liskov Substitution**: All implementations honor their contracts reliably
- **Interface Segregation**: Interfaces are small and focused on specific needs
- **Dependency Inversion**: Classes depend on abstractions, making them flexible and testable

The result is code that's not just working, but truly professional - maintainable, extensible, and reliable.

## Quick Reference for Junior Developers

### Before Writing Any Class, Ask:

1. **Single Responsibility**: "Does this class have more than one reason to change?"
2. **Open/Closed**: "Can I add new behavior without modifying existing code?"
3. **Liskov Substitution**: "Can I replace this implementation with another without breaking anything?"
4. **Interface Segregation**: "Is this interface forcing implementations to add unused methods?"
5. **Dependency Inversion**: "Am I depending on concrete classes or on abstractions?"

### Red Flags in Your Code:

- **Classes with multiple responsibilities** → Split them up (SRP)
- **Adding features by modifying existing classes** → Use interfaces and new implementations (OCP)
- **Subclasses that change expected behavior** → Fix the contract violations (LSP)
- **Large interfaces with many methods** → Split into smaller, focused interfaces (ISP)
- **Classes that directly instantiate their dependencies** → Use dependency injection (DIP)

### Common Junior Developer Mistakes:

1. **Creating "god classes" that do everything** - SRP violation
2. **Adding features by modifying working code** - OCP violation  
3. **Breaking contracts in subclasses** - LSP violation
4. **Creating interfaces with too many methods** - ISP violation
5. **Hard-coding dependencies instead of injecting them** - DIP violation

## How to Spot These Problems in Your Own Code

### SOLID Violation Warning Signs:

**Your class is getting really long** (50+ lines)
- Probably violates Single Responsibility
- Ask: "What are all the different things this class does?"

**You keep modifying the same class for new features**
- Violates Open/Closed Principle
- Ask: "Can I add this feature with a new class instead?"

**Your tests break when you swap implementations**
- Probably violates Liskov Substitution
- Ask: "Do all my implementations really follow the same contract?"

**You're implementing empty methods with exceptions**
- Violates Interface Segregation
- Ask: "Does this interface force me to implement things I don't need?"

**Your classes are hard to test**
- Probably violates Dependency Inversion
- Ask: "Am I depending on concrete classes I can't easily mock?"

### 💡 **Simple SOLID Refactoring Steps for Beginners:**

**Week 1**: Start with Single Responsibility
- Look for classes that do multiple things
- Extract one responsibility at a time
- Don't try to perfect everything immediately

**Week 2**: Add basic interfaces
- Create interfaces for your main service classes
- Practice dependency injection in constructors
- Start small - maybe just one or two interfaces

**Week 3**: Apply Open/Closed
- When adding new features, try creating new classes instead of modifying existing ones
- Use the action pattern from our examples

**Week 4**: Clean up your interfaces
- Split large interfaces into smaller, focused ones
- Make sure classes only implement what they actually use

**Week 5**: Master dependency injection
- Stop creating dependencies inside your methods
- Pass everything through constructors or method parameters
- Practice with simple examples first
