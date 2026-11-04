# General Design Principles - Junior Developer Companion

This guide prepares you to understand general design principles (KISS, DRY, YAGNI, etc.) by building from simple coding decisions to professional architectural choices.

## Before You Start

**Prerequisites**: 
- Basic PHP OOP understanding
- Laravel basics from `laravel-basics.md`
- Some exposure to refactoring concepts

**What you'll gain**: Intuitive understanding of fundamental design principles that guide every architectural decision in professional software development.

## Why Design Principles Matter

### The Problem: Code That Works But Hurts
```php
// This code works, but...
class UserController 
{
    public function register(Request $request) 
    {
        // Validate email (duplicated in 5 other places)
        if (!$request->has('email') || !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Email validation failed: Email field is required and must be a valid email address format like user@domain.com'], 400);
        }
        
        // Validate password (duplicated in 3 other places)  
        if (!$request->has('password') || strlen($request->password) < 8 || !preg_match('/[A-Z]/', $request->password) || !preg_match('/[0-9]/', $request->password)) {
            return response()->json(['error' => 'Password validation failed: Password must be at least 8 characters long and contain at least one uppercase letter and one number for security purposes'], 400);
        }
        
        // Complex user creation logic
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = null;
        $user->created_at = now();
        $user->updated_at = now();
        
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        // Future features we might need someday
        $user->loyalty_points = 0;
        $user->referral_code = $this->generateReferralCode();
        $user->premium_expires_at = null;
        $user->notification_preferences = json_encode([
            'email_marketing' => true,
            'sms_marketing' => false,
            'push_notifications' => true,
            'weekly_digest' => true
        ]);
        
        $user->save();
        
        // Send welcome email
        Mail::raw("Welcome! Your account has been created successfully. Please verify your email address by clicking the link we sent to your email. If you have any questions, please don't hesitate to contact our support team.", function($message) use ($user) {
            $message->to($user->email)->subject('Welcome to Our Platform - Please Verify Your Account');
        });
        
        return response()->json(['message' => 'User registration completed successfully. Please check your email for verification instructions.'], 201);
    }
}
```

**What's wrong?**
- **Duplicated code** everywhere  
- **Overly complex** logic in one place
- **Building features** we don't need yet
- **Long, unclear** variable names and messages
- **Hard to test** and modify

### The Solution: Design Principles
Design principles give us guidelines to write code that is:
- **Easy to understand** (KISS - Keep It Simple, Stupid)
- **Not repetitive** (DRY - Don't Repeat Yourself)  
- **Focused on current needs** (YAGNI - You Aren't Gonna Need It)
- **Well-organized** (Separation of Concerns)
- **Loosely connected** (Law of Demeter)

## The Core Principles

### 1. 🎯 KISS - Keep It Simple, Stupid

**The Principle**: Prefer simple solutions over complex ones. Complexity should only be added when absolutely necessary.

#### Simple vs Complex Example
```php
// COMPLEX: Over-engineered solution
class EmailValidator 
{
    private array $patterns;
    private ValidationEngine $engine;
    private LoggerInterface $logger;
    
    public function __construct() 
    {
        $this->patterns = [
            'basic' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'advanced' => '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            'strict' => '/^(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/i'
        ];
        $this->engine = new ValidationEngine($this->patterns);
        $this->logger = new FileLogger();
    }
    
    public function validate(string $email, string $level = 'basic'): ValidationResult 
    {
        $this->logger->info("Validating email with {$level} pattern");
        $result = $this->engine->validate($email, $this->patterns[$level]);
        $this->logger->info("Validation result: " . ($result->isValid() ? 'valid' : 'invalid'));
        return $result;
    }
}

// Usage (complicated)
$validator = new EmailValidator();
$result = $validator->validate($email, 'advanced');
if ($result->isValid()) {
    // proceed
}
```

```php
// SIMPLE: Straightforward solution
class EmailValidator 
{
    public static function isValid(string $email): bool 
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

// Usage (simple)
if (EmailValidator::isValid($email)) {
    // proceed
}

// Or even simpler for Laravel:
$request->validate([
    'email' => 'required|email'
]);
```

#### Laravel KISS Example
```php
// COMPLEX: Over-abstracted for simple use case
abstract class BaseFormProcessor 
{
    abstract protected function getRules(): array;
    abstract protected function getMessages(): array;
    abstract protected function processValidatedData(array $data): mixed;
    
    public function process(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), $this->getRules(), $this->getMessages());
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $result = $this->processValidatedData($validator->validated());
        
        return response()->json(['data' => $result], 200);
    }
}

class ContactFormProcessor extends BaseFormProcessor 
{
    protected function getRules(): array 
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ];
    }
    
    protected function getMessages(): array 
    {
        return [
            'name.required' => 'Please provide your name',
            'email.required' => 'Please provide your email',
            'message.required' => 'Please provide a message'
        ];
    }
    
    protected function processValidatedData(array $data): array 
    {
        Mail::to('support@company.com')->send(new ContactFormMail($data));
        return ['message' => 'Contact form submitted successfully'];
    }
}
```

```php
// SIMPLE: Direct approach for simple contact form
class ContactController 
{
    public function submit(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);
        
        Mail::to('support@company.com')->send(new ContactFormMail($request->validated()));
        
        return response()->json(['message' => 'Contact form submitted successfully']);
    }
}
```

### 2. 🔄 DRY - Don't Repeat Yourself

**The Principle**: Every piece of knowledge should have a single, unambiguous representation in the system.

#### Identifying Repetition
```php
// BAD: Repetitive validation logic
class UserController 
{
    public function register(Request $request) 
    {
        if (strlen($request->password) < 8 || !preg_match('/[A-Z]/', $request->password) || !preg_match('/[0-9]/', $request->password)) {
            return response()->json(['error' => 'Password must be at least 8 characters with uppercase and number'], 400);
        }
        // ... registration logic
    }
}

class ProfileController 
{
    public function updatePassword(Request $request) 
    {
        if (strlen($request->new_password) < 8 || !preg_match('/[A-Z]/', $request->new_password) || !preg_match('/[0-9]/', $request->new_password)) {
            return response()->json(['error' => 'Password must be at least 8 characters with uppercase and number'], 400);
        }
        // ... update logic
    }
}

class AdminController 
{
    public function createUser(Request $request) 
    {
        if (strlen($request->password) < 8 || !preg_match('/[A-Z]/', $request->password) || !preg_match('/[0-9]/', $request->password)) {
            return response()->json(['error' => 'Password must be at least 8 characters with uppercase and number'], 400);
        }
        // ... creation logic
    }
}
```

#### Eliminating Repetition
```php
// GOOD: Centralized validation
class PasswordValidator 
{
    public static function validate(string $password): bool 
    {
        return strlen($password) >= 8 
            && preg_match('/[A-Z]/', $password) 
            && preg_match('/[0-9]/', $password);
    }
    
    public static function getRequirements(): string 
    {
        return 'Password must be at least 8 characters with uppercase and number';
    }
}

// Or better yet, Laravel Form Request
class PasswordRequest extends FormRequest 
{
    public function rules(): array 
    {
        return [
            'password' => ['required', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/']
        ];
    }
    
    public function messages(): array 
    {
        return [
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => 'Password must contain uppercase letter and number'
        ];
    }
}

// Usage
class UserController 
{
    public function register(PasswordRequest $request) 
    {
        // Validation handled by form request
        // ... registration logic
    }
}
```

#### DRY with Laravel Features
```php
// BAD: Repeating API response format
class UserController 
{
    public function index() 
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully',
            'timestamp' => now()
        ]);
    }
    
    public function show(User $user) 
    {
        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User retrieved successfully',
            'timestamp' => now()
        ]);
    }
}
```

```php
// GOOD: Consistent response format
trait ApiResponse 
{
    protected function successResponse($data, string $message = 'Success', int $code = 200): JsonResponse 
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'timestamp' => now()
        ], $code);
    }
    
    protected function errorResponse(string $message, int $code = 400): JsonResponse 
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()
        ], $code);
    }
}

class UserController 
{
    use ApiResponse;
    
    public function index() 
    {
        return $this->successResponse(User::all(), 'Users retrieved successfully');
    }
    
    public function show(User $user) 
    {
        return $this->successResponse($user, 'User retrieved successfully');
    }
}
```

### 3. 🚫 YAGNI - You Aren't Gonna Need It

**The Principle**: Don't implement features until they are actually needed. Implement today's requirements, not tomorrow's guesses.

#### Over-Engineering Example
```php
// BAD: Building for imaginary future requirements
class User extends Model 
{
    // Current requirement: store user name and email
    protected $fillable = [
        'name', 'email', 'password',
        
        // "We might need these someday"
        'middle_name', 'suffix', 'nickname', 'display_name',
        'birth_date', 'gender', 'nationality', 'citizenship',
        'primary_language', 'secondary_language', 'timezone',
        'currency_preference', 'date_format_preference',
        'notification_email', 'notification_sms', 'notification_push',
        'marketing_email_opt_in', 'marketing_sms_opt_in',
        'referral_code', 'referrer_id', 'affiliate_id',
        'loyalty_points', 'membership_level', 'membership_expires_at',
        'last_activity_at', 'login_count', 'failed_login_count',
        'account_locked_at', 'password_changed_at',
        'gdpr_consent_at', 'terms_accepted_at', 'privacy_policy_accepted_at'
    ];
    
    // Complex relationships for features that don't exist
    public function preferences() { return $this->hasMany(UserPreference::class); }
    public function addresses() { return $this->hasMany(Address::class); }
    public function paymentMethods() { return $this->hasMany(PaymentMethod::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function loyaltyTransactions() { return $this->hasMany(LoyaltyTransaction::class); }
}
```

```php
// GOOD: Build only what you need now
class User extends Model 
{
    // Only current requirements
    protected $fillable = ['name', 'email', 'password'];
    
    protected $hidden = ['password', 'remember_token'];
    
    // Add features when actually needed
}
```

#### Feature Creep Example
```php
// BAD: Building a "flexible" configuration system nobody asked for
class ConfigurationManager 
{
    private array $configs = [];
    private array $environments = ['local', 'testing', 'staging', 'production'];
    private array $sources = ['file', 'database', 'api', 'cache'];
    private array $formats = ['json', 'yaml', 'ini', 'xml'];
    
    public function loadFromMultipleSources(array $sources): void 
    {
        foreach ($sources as $source) {
            $this->loadFromSource($source);
        }
    }
    
    public function loadFromSource(string $source): void 
    {
        switch($source) {
            case 'file': $this->loadFromFile(); break;
            case 'database': $this->loadFromDatabase(); break;
            case 'api': $this->loadFromAPI(); break;
            case 'cache': $this->loadFromCache(); break;
        }
    }
    
    public function mergeConfigurations(array $priorities): array 
    {
        // Complex merging logic for multiple config sources
    }
    
    public function validateConfigurationSchema(array $config): bool 
    {
        // Complex validation for configuration schemas
    }
}

// Current requirement: Get API URL from config
$manager = new ConfigurationManager();
$manager->loadFromMultipleSources(['file', 'database', 'cache']);
$config = $manager->mergeConfigurations(['database', 'file', 'cache']);
$apiUrl = $config['api']['url'] ?? null;
```

```php
// GOOD: Simple solution for actual requirement
$apiUrl = config('services.api.url');

// That's it. Laravel's config system already handles everything we need.
```

### 4. 🎯 Separation of Concerns

**The Principle**: Different concerns should be handled by different parts of the system.

#### Mixed Concerns Example
```php
// BAD: Controller doing everything
class OrderController 
{
    public function create(Request $request) 
    {
        // Concern 1: Input validation
        if (!$request->has('items') || empty($request->items)) {
            return response()->json(['error' => 'No items provided'], 400);
        }
        
        // Concern 2: Business logic
        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return response()->json(['error' => 'Insufficient stock'], 400);
            }
            $total += $product->price * $item['quantity'];
        }
        
        // Concern 3: Data persistence
        $order = new Order();
        $order->user_id = auth()->id();
        $order->total = $total;
        $order->save();
        
        foreach ($request->items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = Product::find($item['product_id'])->price;
            $orderItem->save();
        }
        
        // Concern 4: External service integration
        $payment = new PaymentProcessor();
        $payment->charge($total, $request->payment_method);
        
        // Concern 5: Email notifications
        Mail::to(auth()->user()->email)->send(new OrderConfirmation($order));
        
        // Concern 6: Inventory management
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $product->stock -= $item['quantity'];
            $product->save();
        }
        
        return response()->json($order, 201);
    }
}
```

#### Separated Concerns
```php
// GOOD: Each concern handled separately

// Concern 1: Input validation
class CreateOrderRequest extends FormRequest 
{
    public function rules(): array 
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string'
        ];
    }
}

// Concern 2: Business logic
class OrderService 
{
    public function createOrder(User $user, array $items, string $paymentMethod): Order 
    {
        $this->validateStock($items);
        
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $this->calculateTotal($items)
        ]);
        
        $this->createOrderItems($order, $items);
        
        return $order;
    }
    
    private function validateStock(array $items): void 
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                throw new InsufficientStockException();
            }
        }
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
    
    private function createOrderItems(Order $order, array $items): void 
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);
        }
    }
}

// Concern 3: External service integration
class PaymentService 
{
    public function processPayment(Order $order, string $paymentMethod): PaymentResult 
    {
        // Payment processing logic
    }
}

// Concern 4: Email notifications
class OrderNotificationService 
{
    public function sendConfirmation(Order $order): void 
    {
        Mail::to($order->user->email)->send(new OrderConfirmation($order));
    }
}

// Concern 5: Inventory management
class InventoryService 
{
    public function reserveItems(array $items): void 
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $product->decrement('stock', $item['quantity']);
        }
    }
}

// Concern 6: Coordination (this is the controller's job)
class OrderController 
{
    public function __construct(
        private OrderService $orderService,
        private PaymentService $paymentService,
        private OrderNotificationService $notificationService,
        private InventoryService $inventoryService
    ) {}
    
    public function create(CreateOrderRequest $request) 
    {
        $order = $this->orderService->createOrder(
            auth()->user(),
            $request->validated()['items'],
            $request->validated()['payment_method']
        );
        
        $this->paymentService->processPayment($order, $request->validated()['payment_method']);
        $this->inventoryService->reserveItems($request->validated()['items']);
        $this->notificationService->sendConfirmation($order);
        
        return response()->json($order, 201);
    }
}
```

### 5. 📬 Law of Demeter (Don't Talk to Strangers)

**The Principle**: A class should only communicate with its immediate dependencies, not with dependencies of dependencies.

#### Violating Law of Demeter
```php
// BAD: Reaching through multiple objects
class OrderController 
{
    public function show(Order $order) 
    {
        // Violation: controller -> order -> user -> profile -> address -> city
        $cityName = $order->user->profile->address->city->name;
        
        // Violation: controller -> order -> items -> product -> category -> name
        $categories = [];
        foreach ($order->items as $item) {
            $categories[] = $item->product->category->name;
        }
        
        return view('order.show', compact('order', 'cityName', 'categories'));
    }
}
```

#### Following Law of Demeter
```php
// GOOD: Each object provides what its clients need
class Order extends Model 
{
    public function getShippingCityName(): string 
    {
        return $this->user->getShippingCityName();
    }
    
    public function getCategoryNames(): array 
    {
        return $this->items->map(function ($item) {
            return $item->getCategoryName();
        })->unique()->values()->toArray();
    }
}

class User extends Model 
{
    public function getShippingCityName(): string 
    {
        return $this->profile?->getShippingCityName() ?? 'Unknown';
    }
}

class UserProfile extends Model 
{
    public function getShippingCityName(): string 
    {
        return $this->address?->getCityName() ?? 'Unknown';
    }
}

class Address extends Model 
{
    public function getCityName(): string 
    {
        return $this->city->name ?? 'Unknown';
    }
}

class OrderItem extends Model 
{
    public function getCategoryName(): string 
    {
        return $this->product->getCategoryName();
    }
}

class Product extends Model 
{
    public function getCategoryName(): string 
    {
        return $this->category->name ?? 'Uncategorized';
    }
}

class OrderController 
{
    public function show(Order $order) 
    {
        // Clean: only talk to immediate dependencies
        $cityName = $order->getShippingCityName();
        $categories = $order->getCategoryNames();
        
        return view('order.show', compact('order', 'cityName', 'categories'));
    }
}
```

### 6. 🔄 Tell, Don't Ask

**The Principle**: Instead of asking an object for data and then acting on it, tell the object what to do.

#### Asking Instead of Telling
```php
// BAD: Asking for data, then making decisions
class OrderController 
{
    public function process(Order $order) 
    {
        // Asking for status, then deciding what to do
        if ($order->status === 'pending') {
            $order->status = 'processing';
            $order->processed_at = now();
            $order->save();
            
            // Send notification
            Mail::to($order->user->email)->send(new OrderProcessingNotification($order));
            
            // Update inventory
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->stock -= $item->quantity;
                $product->save();
            }
        }
    }
}
```

#### Telling What to Do
```php
// GOOD: Telling the object what to do
class Order extends Model 
{
    public function process(): void 
    {
        if ($this->status !== 'pending') {
            throw new InvalidOrderStateException('Order can only be processed from pending state');
        }
        
        $this->status = 'processing';
        $this->processed_at = now();
        $this->save();
        
        $this->sendProcessingNotification();
        $this->reserveInventory();
    }
    
    private function sendProcessingNotification(): void 
    {
        Mail::to($this->user->email)->send(new OrderProcessingNotification($this));
    }
    
    private function reserveInventory(): void 
    {
        foreach ($this->items as $item) {
            $item->product->decrementStock($item->quantity);
        }
    }
}

class OrderController 
{
    public function process(Order $order) 
    {
        // Simply tell the order to process itself
        $order->process();
        
        return response()->json(['message' => 'Order processed successfully']);
    }
}
```

## Applying Principles Together

### Before: Violating All Principles
```php
class UserManager 
{
    public function createUserWithProfile(Request $request) 
    {
        // Violates KISS: Over-complicated for basic user creation
        $validationRules = $this->getComplexValidationRules();
        $validator = $this->createAdvancedValidator($validationRules);
        $result = $this->runValidationWithCustomEngine($validator, $request->all());
        
        // Violates DRY: Validation logic repeated in other methods
        if (strlen($request->password) < 8) {
            throw new ValidationException('Password too short');
        }
        if (!preg_match('/[A-Z]/', $request->password)) {
            throw new ValidationException('Password needs uppercase');
        }
        
        // Violates YAGNI: Building features we don't need
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->loyalty_points = 0; // Not needed yet
        $user->referral_code = Str::random(10); // Not needed yet
        $user->premium_expires_at = null; // Not needed yet
        $user->save();
        
        // Violates Separation of Concerns: Mixing user creation with profile creation
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->save();
        
        // Violates Law of Demeter: Reaching through objects
        $cityName = $user->profile->address->city->name;
        
        // Violates Tell Don't Ask: Asking for data instead of telling what to do
        if ($user->email_verified_at === null) {
            Mail::to($user->email)->send(new EmailVerification($user));
        }
        
        return $user;
    }
}
```

### After: Following All Principles
```php
// KISS: Simple validation using Laravel features
class CreateUserRequest extends FormRequest 
{
    public function rules(): array 
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|regex:/[A-Z]/',
            'first_name' => 'required|string',
            'last_name' => 'required|string'
        ];
    }
}

// Separation of Concerns: Each service has one responsibility
class UserService 
{
    public function __construct(
        private ProfileService $profileService,
        private NotificationService $notificationService
    ) {}
    
    public function createUser(array $userData): User 
    {
        // YAGNI: Only create what we need now
        $user = User::create([
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'])
        ]);
        
        $this->profileService->createProfile($user, $userData);
        
        // Tell Don't Ask: Tell user to send verification
        $user->sendVerificationEmail();
        
        return $user;
    }
}

class ProfileService 
{
    public function createProfile(User $user, array $profileData): UserProfile 
    {
        return $user->profile()->create([
            'first_name' => $profileData['first_name'],
            'last_name' => $profileData['last_name']
        ]);
    }
}

// Law of Demeter: User provides what clients need
class User extends Model 
{
    public function sendVerificationEmail(): void 
    {
        if ($this->hasVerifiedEmail()) {
            return;
        }
        
        Mail::to($this->email)->send(new EmailVerification($this));
    }
    
    public function getFullName(): string 
    {
        return $this->profile ? 
            $this->profile->getFullName() : 
            $this->email;
    }
}

// Simple controller that coordinates
class UserController 
{
    public function __construct(private UserService $userService) {}
    
    public function store(CreateUserRequest $request) 
    {
        $user = $this->userService->createUser($request->validated());
        
        return response()->json([
            'user' => $user,
            'message' => 'User created successfully'
        ], 201);
    }
}
```

## Common Principle Violations and Fixes

### 1. KISS Violation: Over-Engineering
```php
// BAD: Complex abstraction for simple task
interface DatabaseConnectionInterface {
    public function connect(): ConnectionInterface;
}

interface ConnectionInterface {
    public function execute(QueryInterface $query): ResultInterface;
}

class MySQLDatabaseConnection implements DatabaseConnectionInterface {
    // 50 lines of complex connection logic for simple query
}

// For executing: SELECT * FROM users WHERE id = 1

// GOOD: Use Laravel's built-in features
User::find(1);
```

### 2. DRY Violation: Repeated Logic
```php
// BAD: Same logic in multiple places
class OrderController {
    public function calculateShipping(Order $order) {
        if ($order->total > 100) return 0;
        if ($order->user->state === 'CA') return 15;
        return 10;
    }
}

class CheckoutController {
    public function getShippingCost(Request $request) {
        $total = $request->total;
        if ($total > 100) return 0;
        if ($request->state === 'CA') return 15;
        return 10;
    }
}

// GOOD: Centralized logic
class ShippingCalculator {
    public static function calculate(float $total, string $state): float {
        if ($total > 100) return 0.00;
        if ($state === 'CA') return 15.00;
        return 10.00;
    }
}
```

### 3. YAGNI Violation: Premature Features
```php
// BAD: Building features for unknown future
class User extends Model {
    protected $fillable = [
        'name', 'email', // Current need
        'points', 'level', 'badges', // "We might add gamification"
        'subscription_tier', 'billing_address', // "We might add subscriptions"
        'referral_code', 'referrer_id', // "We might add referrals"
    ];
}

// GOOD: Build what you need now
class User extends Model {
    protected $fillable = ['name', 'email'];
    
    // Add features when actually needed
}
```

## Ready for Advanced Design Principles

**You're ready for the advanced material when you can**:

- [ ] Recognize when code is unnecessarily complex (KISS)
- [ ] Identify and eliminate repeated logic (DRY)
- [ ] Resist building features before they're needed (YAGNI)
- [ ] Separate different concerns into different classes
- [ ] Design clean interfaces between objects (Law of Demeter)
- [ ] Tell objects what to do rather than asking for their data
- [ ] Apply multiple principles together to create clean, maintainable code

The advanced General Design Principles README shows these concepts applied to complex Laravel architectures where multiple systems interact. You'll see how these fundamental principles scale to enterprise-level applications.
