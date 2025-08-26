# GRASP Principles Demonstration

The questionnaire website example shows a clear violation and proper application of GRASP (General Responsibility Assignment Software Patterns) principles. Users upload filled-in templates that are stored on the server and related to specific questions. The original implementation in [Bad example](../example/bad/app) violates multiple GRASP principles, while the refactored version in [Better example](../example/better/app) demonstrates proper application.

## What is GRASP?

GRASP consists of nine fundamental principles that guide object-oriented design decisions. These patterns help determine **where to assign responsibilities** in a system, leading to more maintainable, flexible, and understandable code.

Think of GRASP as a set of questions to ask yourself when designing code:
- "Which class should be responsible for this task?"
- "How can I minimize dependencies between classes?"
- "How can I make my code easier to change and extend?"

## Learning Path for Juniors

**Start here (Core principles):**
1. **Information Expert** - Who has the data needed for this task?
2. **High Cohesion** - Does this class have a single, focused purpose?
3. **Low Coupling** - Are my classes too dependent on each other?

**Build on these (Design patterns):**
4. **Controller** - How should I handle user interactions?
5. **Creator** - Who should create new objects?

**Master these (Advanced concepts):**
6. **Pure Fabrication** - Sometimes I need classes that don't represent real things
7. **Indirection** - Using middleman objects to reduce dependencies
8. **Polymorphism** - Different objects, same interface
9. **Protected Variations** - Preparing for future changes

## GRASP Principles Analysis

### 1. Information Expert
**Principle**: Assign responsibility to the class that has the information needed to fulfill it.

**Real-world analogy**: If you want to know how much flour is left in the pantry, you ask the kitchen manager, not the waiter. The kitchen manager has the inventory information, so they're the "expert" for that question.

**What it means**: When you need to assign a responsibility to an object, look for the class that has the most information required to fulfill that responsibility. This class becomes the "expert" for that particular operation. The Information Expert pattern helps maintain encapsulation and ensures that data and the operations on that data stay together.

**Why it matters**: 
- Reduces coupling by keeping related data and behavior together
- Improves maintainability by making it clear where certain logic belongs
- Enhances encapsulation by preventing external classes from accessing internal data directly

**Quick question to ask**: "Which class already has the data needed to do this job?"

**Violation in Bad Example**
The [`FileController`](../example/bad/app/Http/Controllers/FileController.php) calculates file sizes and validates upload limits, even though it doesn't contain the relevant information:

```php
// MISTAKE 4: Upload limit validation in controller instead of with the expert (User model)
// Calculated total attachment size
$totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);

// Check if uploadlimit has been exceeded
if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
    throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
}
```

The controller is doing calculations and validations that should belong to the [`User`](../example/better/app/Models/User.php) model (which has the upload limit information) or a dedicated validation class.

**Proper Application in Better Example**
- The [`User`](../example/better/app/Models/User.php) model handles upload limit logic since it contains the upload information
- The [`FileSize`](../example/better/app/ValueObjects/FileSize.php) value object encapsulates file size calculations
- The [`UploadLimit`](../example/better/app/Rules/UploadLimit.php) rule validates upload constraints using the information it has access to

```php
/**
 * Run the validation rule.
 */
public function validate(string $attribute, int $value, Closure $fail): void
{
    if (request()->user()->getUploadLimit() < $value + request()->user()->getTotalUploadSize()) {
        $fail('Attachments total size exceeds upload limit.');
    }
}
```

### 2. Creator
**Principle**: Assign class B the responsibility to create an instance of class A if B aggregates, contains, records, or closely uses A.

**Real-world analogy**: A kitchen creates its own dishes because it has the ingredients, knows the recipes, and manages what meals it serves. You wouldn't ask the dining room staff to create a meal - they don't have the right relationship with the cooking process.

**What it means**: Determine which class should be responsible for creating instances of other classes. A class should create objects of another class if it has one or more of these relationships:
- Contains or compositely aggregates the other class
- Records instances of the other class  
- Closely uses the other class
- Has the initializing data that will be passed to the other class

**Why it matters**:
- Reduces coupling by avoiding scattered object creation throughout the system
- Makes object creation predictable and centralized
- Ensures that classes that need certain objects are responsible for creating them
- Facilitates dependency injection and testing

**Quick question to ask**: "Which class naturally contains, uses, or records this object?"

**Violation in Bad Example**
The controller creates and manages all objects directly, even though it doesn't have the proper relationship with them:

```php
// MISTAKE 6: Controller creating GivenAnswer for each file unnecessarily
$given_answer = GivenAnswer::updateOrCreate(
    [
        'application_id' => $application->id,
        'question_id' => $request->questionId,
    ],
    [
        'answer' => true
    ]
);

// MISTAKE 7: Controller creating file records individually
$given_answer->files()->updateOrCreate([
    'filename' => $file->getClientOriginalName(),
    'extension' => $file->getClientOriginalExtension(),
    'path' => $filePath,
    'uuid' => Str::uuid(),
    'user_id' => $user->id
]);
```

The controller is creating `GivenAnswer` and `File` objects, but it doesn't have the proper aggregation or composition relationship with these entities.

**Proper Application in Better Example**
The [`AnswerService`](../example/better/app/Services/AnswerService.php) creates and manages related objects since it orchestrates the answer process:

```php
public function answerQuestion(Question $question, array $data): void
{
    $this->handlers->first(fn($h) => $h->canHandle($question))
        ->handle($question, $data);
}
```

### 3. Controller
**Principle**: Assign responsibility for handling system events to a controller class, but don't make it do everything.

**Real-world analogy**: A head waiter coordinates orders and tells the kitchen what to cook, but doesn't actually prepare the food, wash dishes, or handle payments. They just direct the flow and let others do the specialized work.

**What it means**: A controller is responsible for receiving or handling system events and coordinating the response. However, the controller should delegate the actual work to other objects rather than doing everything itself.

**Common mistakes**:
- "Bloated controllers" that contain business logic
- Controllers that directly manipulate data
- Controllers that handle validation, authorization, and business rules all in one place

**Why it matters**:
- Separates concerns between system interface and business logic
- Makes controllers thin and focused on coordination
- Improves testability by isolating system event handling
- Enables reuse of business logic in different contexts

**Quick question to ask**: "Is my controller just coordinating, or is it doing the actual work?"

**Violation in Bad Example**
The [`FileController`](../example/bad/app/Http/Controllers/FileController.php) handles system events but also performs business logic, validation, and data manipulation - violating the Single Responsibility Principle with 80+ lines of mixed concerns:

```php
public function store(StoreFileRequest $request)
{
    // MISTAKE 1: Complex application logic in controller
    $application = $user->application;
    if ($application->id != $request->applicationId) {
        $application = $application->where('id', $request->applicationId)->first();
    }

    // MISTAKE 2: Authorization logic in controller
    Gate::authorize('update', [$application, $user->organisation->id]);

    // MISTAKE 3: Additional validation in controller
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'mimes:pdf|min:1|max:5120',
    ]);

    // MISTAKE 4: Business logic for upload limits
    $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
    if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
        throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
    }

    // MISTAKE 5: File processing and database operations
    foreach ($request->file('files') as $file) {
        // ... complex file handling logic
    }
}
```

The controller is doing everything: authentication, validation, business logic, file handling, and database operations.

**Proper Application in Better Example**
The [`FileController`](../example/better/app/Http/Controllers/FileController.php) only coordinates between components:

```php
public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
{
    $validatedData = $request->validated();
    
    $answerService->answerQuestionWithFiles(
        $validatedData['questionId'],
        $request->file('files')
    );
    
    $user->updateUploadSizeTotal($validatedData['total_file_size']);
}
```

### 4. Low Coupling
**Principle**: Keep dependencies between classes minimal and well-defined.

**Real-world analogy**: The kitchen stations (prep, grill, salad, dessert) work independently - the grill station doesn't need to know how the salad station works. They only communicate through simple handoffs (finished dishes), not internal details.

**What it means**: Coupling refers to how strongly one class depends on another. Low coupling means that classes have minimal dependencies on each other and communicate through well-defined interfaces. When classes are loosely coupled, changes in one class are less likely to force changes in other classes.

**Types of coupling** (from worst to best):
- **Content coupling**: One class modifies internal data of another
- **Common coupling**: Classes share global data
- **Control coupling**: One class controls the flow of another
- **Data coupling**: Classes share only necessary data through parameters
- **No coupling**: Classes are completely independent

**Benefits of low coupling**:
- Easier to understand, modify, and extend individual classes
- Better testability through dependency injection
- Improved reusability of components
- Reduced ripple effects when making changes

**Quick question to ask**: "If I change this class, how many other classes will break?"

**Violation in Bad Example**
High coupling with the controller directly manipulating multiple models, handling validation, and managing file operations:

```php
// MISTAKE 1: Tightly coupled to application retrieval logic
$application = $user->application;
if ($application->id != $request->applicationId) {
    $application = $application->where('id', $request->applicationId)->first();
}

// MISTAKE 2: Tightly coupled to authorization system
Gate::authorize('update', [$application, $user->organisation->id]);

// MISTAKE 3: Tightly coupled to validation framework
$request->validate([
    'files' => 'required|array',
    'files.*' => 'mimes:pdf|min:1|max:5120',
]);

// MISTAKE 4: Tightly coupled to user upload limit calculations
if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
    throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
}

// MISTAKE 5: Tightly coupled to file storage and database operations
foreach ($request->file('files') as $file) {
    $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
    $given_answer = GivenAnswer::updateOrCreate([...]);
    $given_answer->files()->updateOrCreate([...]);
}
```

Changes in validation rules, file storage, authorization, or database schema would require modifications throughout the controller.

**Proper Application in Better Example**
- The [`FileController`](../example/better/app/Http/Controllers/FileController.php) only depends on the request, user, and service
- Dependencies are injected through the service container
- Each class has minimal, well-defined dependencies

### 5. High Cohesion
**Principle**: Keep related responsibilities together within a class.

**Real-world analogy**: A good chef focuses on cooking, not managing reservations or handling payments. Each person in the restaurant should have a focused, related set of responsibilities.

**What it means**: Cohesion measures how closely related the responsibilities of a class are. High cohesion means that a class has a focused purpose and all its methods work together toward that single purpose. Each class should have one reason to change (Single Responsibility Principle).

**Types of cohesion** (from worst to best):
- **Coincidental**: Unrelated functionality grouped together
- **Logical**: Related functionality but different operations (e.g., all validation in one class)
- **Temporal**: Operations performed at the same time
- **Procedural**: Operations that follow a sequence
- **Communicational**: Operations that work on the same data
- **Sequential**: Operations where output of one is input to another
- **Functional**: All operations contribute to a single task

**Benefits of high cohesion**:
- Classes are easier to understand and maintain
- Better code organization and readability
- Increased reusability of focused components
- Simplified testing of well-defined functionality

**Quick question to ask**: "If I had to explain what this class does in one sentence, would it be clear and simple?"

**Violation in Bad Example**
Low cohesion with the controller mixing HTTP handling, validation logic, business rules, and data persistence in a single method:

```php
public function store(StoreFileRequest $request)
{
    // HTTP/Authentication concern
    $user = auth()->user();
    
    // Application retrieval concern  
    $application = $user->application;
    if ($application->id != $request->applicationId) {
        $application = $application->where('id', $request->applicationId)->first();
    }
    
    // Authorization concern
    Gate::authorize('update', [$application, $user->organisation->id]);
    
    // Validation concern
    $request->validate(['files' => 'required|array', 'files.*' => 'mimes:pdf|min:1|max:5120']);
    
    // Business logic concern
    $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);
    if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
        throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
    }
    
    // File storage concern
    foreach ($request->file('files') as $file) {
        $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
        
        // Database persistence concern
        $given_answer = GivenAnswer::updateOrCreate([...]);
        $given_answer->files()->updateOrCreate([...]);
    }
    
    // User state update concern
    $user->updateUploadSizeTotal($totalUploadedSize);
}
```

The method handles at least 7 different concerns, making it difficult to understand, test, and maintain.

**Proper Application in Better Example**
- [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php) handles all validation concerns
- [`AnswerService`](../example/better/app/Services/AnswerService.php) manages business logic for answering questions
- [`FileSize`](../example/better/app/ValueObjects/FileSize.php) value object encapsulates file size operations
- Each class has a single, focused responsibility

### 6. Polymorphism
**Principle**: Use polymorphic operations instead of explicit type checking.

**Real-world analogy**: Different types of kitchen staff (chef, sous chef, prep cook) all respond to "prepare this dish" but each does it in their own way based on their role. You don't need to know who specifically will handle it - you just give the order and they handle it appropriately.

**What it means**: When behavior varies by type, assign responsibility for that behavior to the types themselves using polymorphism, rather than using conditional logic based on type. This eliminates the need for switch statements or if-else chains that check object types.

**How it works**:
- Define a common interface or abstract class
- Implement specific behavior in concrete classes
- Client code works with the interface, not concrete implementations
- New types can be added without modifying existing code

**Benefits**:
- **Open/Closed Principle**: Open for extension, closed for modification
- Eliminates complex conditional logic
- Makes code more flexible and extensible
- Easier to add new types without changing existing code
- Better separation of concerns

**Quick question to ask**: "Am I checking types with if/switch statements, or letting objects handle their own behavior?"

**Missing in Bad Example**
While the bad example doesn't show explicit type checking violations, it lacks polymorphic design. The controller handles all file operations the same way, but different question types might require different handling strategies:

```php
// All files are processed identically, regardless of question type
foreach ($request->file('files') as $file) {
    $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');
    
    $given_answer = GivenAnswer::updateOrCreate(
        [
            'application_id' => $application->id,
            'question_id' => $request->questionId,
        ],
        [
            'answer' => true  // Same processing for all question types
        ]
    );
    
    $given_answer->files()->updateOrCreate([...]);  // Same file handling for all types
}
```

This approach doesn't allow for different processing strategies based on question types, file types, or other varying factors.

**Proper Application in Better Example**
The [`AnswerAction`](../example/better/app/Contracts/AnswerAction.php) interface enables polymorphic behavior:

```php
interface AnswerAction
{
    public function canHandle(Question $question): bool;
    public function handle(Question $question, array $data): void;
}
```

Different implementations like [`UploadAction`](../example/better/app/Actions/Answer/UploadAction.php) can handle various question types without conditional logic in the controller.

### 7. Pure Fabrication
**Principle**: Create classes that don't represent domain concepts when needed for good design.

**Real-world analogy**: A kitchen coordinator helps organize workflow and timing between stations, but isn't actually a cooking station or food ingredient themselves. Sometimes you need helper roles that support the core restaurant operations without being part of the actual food domain.

**What it means**: Sometimes, to achieve low coupling and high cohesion, you need to create classes that don't represent real-world domain objects. These "fabricated" classes serve specific design purposes like coordination, service provision, or algorithm encapsulation.

**When to use Pure Fabrication**:
- When assigning responsibility to a domain class would break low coupling or high cohesion
- When you need a service layer to coordinate between domain objects
- When algorithms or utilities don't naturally belong to any domain class
- When you need to isolate external dependencies

**Common examples**:
- Service classes (like `AnswerService`)
- Repository classes for data access
- Factory classes for object creation
- Utility classes for algorithms
- Adapter classes for external integrations

**Benefits**:
- Maintains clean domain models
- Provides flexibility in system architecture
- Supports better separation of concerns
- Enables easier testing and mocking

**Quick question to ask**: "Does this complex logic naturally belong to any of my domain objects, or do I need a helper class?"

**Violation in Bad Example**
The controller contains complex business logic that doesn't naturally belong to any domain object:

```php
// MISTAKE 5: The logic to update or create the answer should be moved to a service class
// Business logic does not belong in the controller
foreach ($request->file('files') as $file) {
    $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');

    // MISTAKE 6: Needless database queries for each file
    $given_answer = GivenAnswer::updateOrCreate(
        [
            'application_id' => $application->id,
            'question_id' => $request->questionId,
        ],
        [
            'answer' => true
        ]
    );

    // MISTAKE 7: Separate query for each file, poor optimization
    $given_answer->files()->updateOrCreate([
        'filename' => $file->getClientOriginalName(),
        'extension' => $file->getClientOriginalExtension(),
        'path' => $filePath,
        'uuid' => Str::uuid(),
        'user_id' => $user->id
    ]);
}
```

This complex file handling and answer coordination logic doesn't belong to the controller, nor does it fit naturally into any domain model. It needs a fabricated service class.

**Proper Application in Better Example**
- [`AnswerService`](../example/better/app/Services/AnswerService.php) - a fabricated class to handle answer coordination
- [`FileSize`](../example/better/app/ValueObjects/FileSize.php) - a value object for file size operations
- [`UploadLimit`](../example/better/app/Rules/UploadLimit.php) - a validation rule class

These classes don't represent real-world entities but provide better design structure.

### 8. Indirection
**Principle**: Use intermediate objects to reduce direct coupling between components.

**Real-world analogy**: When you place an order, you don't talk directly to the chef. You tell the waiter who routes your order to the kitchen. This "middleman" reduces the need for customers to know about all the restaurant's internal operations.

**What it means**: To support low coupling between two elements, assign responsibility to an intermediate object that mediates between them. This intermediate object acts as a "middleman" that handles communication and coordination.

**How it works**:
- Instead of Class A directly calling Class B
- Class A calls Mediator, which then calls Class B
- The mediator handles the interaction details
- Classes A and B don't need to know about each other directly

**Common examples**:
- Service layers mediating between controllers and models
- Facades providing simplified interfaces to complex subsystems
- Event dispatchers handling communication between components
- Dependency injection containers managing object creation
- Request/Response objects mediating between HTTP and application logic

**Benefits**:
- Reduces direct dependencies between classes
- Makes systems more flexible and configurable
- Easier to change implementations without affecting clients
- Supports better testing through mock intermediaries
- Enables cross-cutting concerns (logging, caching, etc.)

**Quick question to ask**: "Are my classes talking directly to each other, or do I have mediators to reduce coupling?"

**Missing in Bad Example**
The bad example lacks proper indirection, with the controller directly accessing and manipulating everything:

```php
// Direct access to authentication
$user = auth()->user();

// Direct access to models and their relationships
$application = $user->application;

// Direct authorization calls
Gate::authorize('update', [$application, $user->organisation->id]);

// Direct validation calls  
$request->validate([...]);

// Direct file storage operations
$filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');

// Direct database operations
$given_answer = GivenAnswer::updateOrCreate([...]);
$given_answer->files()->updateOrCreate([...]);

// Direct user state updates
$user->updateUploadSizeTotal($totalUploadedSize);
```

The controller is directly coupled to all these subsystems without any mediating objects to reduce the coupling.

**Proper Application in Better Example**
- [`AnswerService`](../example/better/app/Services/AnswerService.php) mediates between the controller and business logic
- [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php) mediates between HTTP input and validation
- The service container mediates object creation and dependency injection

### 9. Protected Variations
**Principle**: Shield components from variations in other components using stable interfaces.

**Real-world analogy**: The restaurant menu works the same way whether the kitchen uses gas stoves, electric ovens, or wood-fired grills. The menu interface (dishes and prices) stays the same even though the cooking equipment varies. You're protected from the variations in kitchen implementations.

**What it means**: Identify points of predicted variation or instability in your system and assign responsibilities to create a stable interface around them. This protects the rest of the system from changes in these unstable areas.

**How to apply**:
1. **Identify variation points**: Areas likely to change (algorithms, data formats, external services)
2. **Create stable interfaces**: Define contracts that won't change even if implementations do
3. **Encapsulate variations**: Hide implementation details behind the interface
4. **Use configuration**: Make variations configurable rather than hard-coded

**Common techniques**:
- **Interfaces and abstract classes**: Define contracts for varying implementations
- **Strategy pattern**: Encapsulate algorithms behind a common interface
- **Configuration files**: External configuration for varying behavior
- **Dependency injection**: Inject different implementations
- **Factory patterns**: Create objects based on configuration

**Examples of variation points**:
- Database implementations (MySQL, PostgreSQL, MongoDB)
- Payment processors (Stripe, PayPal, Square)
- File storage (local, S3, Google Cloud)
- Notification methods (email, SMS, push notifications)
- Algorithm implementations (sorting, search, validation)

**Benefits**:
- System remains stable when implementations change
- New variations can be added without modifying existing code
- Better testability through interface-based testing
- Supports different deployment configurations

**Quick question to ask**: "What parts of my system are likely to change, and have I protected the rest of the code from those changes?"

**Missing in Bad Example**
The bad example has several unprotected variation points that make the system fragile to changes:

```php
// VARIATION POINT 1: File storage location is hard-coded
$filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');

// VARIATION POINT 2: File validation rules are scattered and hard-coded
$request->validate([
    'files' => 'required|array',
    'files.*' => 'mimes:pdf|min:1|max:5120',  // Hard-coded file size and types
]);

// VARIATION POINT 3: Upload limit calculation logic is embedded in controller
if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
    throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
}

// VARIATION POINT 4: Database operations are directly embedded
$given_answer = GivenAnswer::updateOrCreate([...]);
$given_answer->files()->updateOrCreate([...]);
```

Changes to file storage systems, validation rules, upload limit calculations, or database schemas would require modifications throughout the controller, making the system unstable to variations.

**Proper Application in Better Example**
- The [`AnswerAction`](../example/better/app/Contracts/AnswerAction.php) interface protects against changes in question handling logic
- Service configuration in [`answer.php`](../example/better/config/answer.php) allows flexible handler registration
- Dependency injection protects against implementation changes

## Key Improvements Through GRASP

### Dependency Injection
The better example uses Laravel's service container to manage dependencies automatically, reducing coupling and improving testability.

### Form Request Optimization
Authorization, validation, and data preparation are properly delegated to the [`StoreFileRequest`](../example/better/app/Http/Requests/StoreFileRequest.php):

```php
protected function prepareForValidation(): void
{
    $this->merge([
        'total_file_size' => array_reduce($this->file('files'), fn(int $carry, $file) => $carry + $file->getSize(), 0),
    ]);
}
```

### Service Layer Introduction
Business logic is extracted to the [`AnswerService`](../example/better/app/Services/AnswerService.php), following the Creator and Information Expert principles by placing responsibilities with the classes that have the necessary information and relationships.

## Result

The refactored code demonstrates proper GRASP application with a clean, focused controller that delegates responsibilities appropriately. Each class has a single, well-defined purpose, making the system more maintainable, testable, and extensible. The transformation from an 80+ line controller method to a concise 8-line method shows the power of applying GRASP principles correctly.

## Quick Reference for Junior Developers

### Before Writing Any Class, Ask:
1. **Information Expert**: "Which class has the data needed for this?"
2. **Creator**: "Which class naturally contains, uses, or records this object?"
3. **Controller**: "Am I keeping my controllers thin and just coordinating?"
4. **Low Coupling**: "Can this class work independently without breaking others?"
5. **High Cohesion**: "Does this class have one clear purpose?"
6. **Polymorphism**: "Am I checking types with if/switch statements, or letting objects handle their own behavior?"
7. **Pure Fabrication**: "Does this complex logic naturally belong to any of my domain objects, or do I need a helper class?"
8. **Indirection**: "Are my classes talking directly to each other, or do I have mediators to reduce coupling?"
9. **Protected Variations**: "What parts of my system are likely to change, and have I protected the rest of the code from those changes?"

### Red Flags in Your Code:
- **Controller doing business logic** → Extract to service classes (Controller)
- **Classes with multiple responsibilities** → Split them up (High Cohesion)
- **Classes knowing too much about each other** → Add mediators (Low Coupling, Indirection)
- **Wrong class creating objects** → Move to proper creator (Creator)
- **Logic in classes without the data** → Move to information expert (Information Expert)
- **Repetitive if/switch statements** → Use polymorphism (Polymorphism)
- **Complex coordination in domain classes** → Create service classes (Pure Fabrication)
- **Hard-coded values scattered everywhere** → Protect variations (Protected Variations)

### Common Junior Developer Mistakes:
1. **Putting everything in the controller** - Controllers should only coordinate, not do the work
2. **Creating "god classes"** - Classes that do everything violate high cohesion
3. **Tight coupling everywhere** - Classes depending on too many other classes
4. **Not using dependency injection** - Makes testing and flexibility impossible
5. **Ignoring the domain model** - Business logic scattered instead of in domain objects
