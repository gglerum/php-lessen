# GRASP Principles - Junior Developer Companion

This guide prepares you to understand the GRASP (General Responsibility Assignment Software Patterns) principles by building from simple responsibility concepts to sophisticated architectural decisions.

## Before You Start

**Prerequisites**: 
- Basic OOP understanding
- Laravel basics from `laravel-basics.md`
- Advanced OOP concepts from `advanced-oop.md`
- Understanding of refactoring from `design-principles-companion.md`

**What you'll gain**: A systematic framework for deciding "what class should be responsible for what?" - the core skill of good software architecture.

## What is GRASP?

GRASP stands for **General Responsibility Assignment Software Patterns**. It's not about grasping concepts (though you will!), but about nine principles that help you decide which class should handle which responsibility.

**The Core Question**: When you need to add new behavior to your system, which existing class should you modify, or should you create a new class?

## The Responsibility Assignment Problem

### Before GRASP: Random Decisions
```php
// Where should we put the logic for calculating order totals?

// Option 1: In the Controller?
class OrderController 
{
    public function create(Request $request) 
    {
        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            $total += $product->price * $item['quantity'];
            
            // Apply taxes
            if ($product->taxable) {
                $total += $total * 0.1;
            }
        }
        // ...
    }
}

// Option 2: In the Order model?
class Order extends Model 
{
    public function calculateTotal() 
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->product->price * $item->quantity;
            if ($item->product->taxable) {
                $total += $total * 0.1;
            }
        }
        return $total;
    }
}

// Option 3: Somewhere else?
```

**The Problem**: Without principles, these decisions are random and lead to inconsistent, hard-to-maintain code.

### After GRASP: Systematic Decisions
GRASP gives you a framework to make these decisions consistently and logically.

## The 9 GRASP Principles (Simple to Complex)

### 1. 📋 Information Expert
**"Give responsibility to the class that has the information needed to fulfill it"**

#### Simple Example
```php
// BAD: Class without the information does the work
class OrderController 
{
    public function getOrderTotal(Order $order) 
    {
        $total = 0;
        // Controller has to fetch order items and calculate
        foreach ($order->items as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total;
    }
}
```

```php
// GOOD: The class with the information does the work
class Order extends Model 
{
    public function getTotal(): float 
    {
        // Order has direct access to its items
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}

class OrderController 
{
    public function show(Order $order) 
    {
        return response()->json([
            'order' => $order,
            'total' => $order->getTotal() // Order calculates its own total
        ]);
    }
}
```

#### Laravel Example
```php
// User has information about their own permissions
class User extends Model 
{
    public function canEditPost(Post $post): bool 
    {
        return $this->id === $post->author_id || $this->hasRole('admin');
    }
    
    public function getFullName(): string 
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

// Usage
if (auth()->user()->canEditPost($post)) {
    // Allow editing
}
```

### 2. 🎯 Creator
**"Assign class A the responsibility to create class B if A aggregates, contains, records, or closely uses B"**

#### Simple Example
```php
// BAD: Unrelated class creates objects
class OrderController 
{
    public function addItem(Order $order, Request $request) 
    {
        // Controller shouldn't create OrderItems
        $item = new OrderItem([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => Product::find($request->product_id)->price
        ]);
        
        $order->items()->save($item);
    }
}
```

```php
// GOOD: Order creates its own items
class Order extends Model 
{
    public function addItem(int $productId, int $quantity): OrderItem 
    {
        $product = Product::findOrFail($productId);
        
        // Order is responsible for creating its items
        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $product->price
        ]);
    }
}

class OrderController 
{
    public function addItem(Order $order, Request $request) 
    {
        $item = $order->addItem(
            $request->product_id,
            $request->quantity
        );
        
        return response()->json($item);
    }
}
```

#### Laravel Factory Example
```php
// Order creates its related objects
class Order extends Model 
{
    public function createInvoice(): Invoice 
    {
        return Invoice::create([
            'order_id' => $this->id,
            'amount' => $this->getTotal(),
            'due_date' => now()->addDays(30)
        ]);
    }
    
    public function createShipment(array $trackingInfo): Shipment 
    {
        return $this->shipments()->create($trackingInfo);
    }
}
```

### 3. 🎮 Controller
**"Assign the responsibility to a class that coordinates and controls the activity"**

This is NOT about Laravel Controllers, but about classes that coordinate complex operations.

#### Simple Example
```php
// BAD: Business logic scattered across multiple places
class OrderController 
{
    public function checkout(Request $request) 
    {
        // Payment processing
        $payment = new PaymentProcessor();
        $result = $payment->charge($request->total, $request->card);
        
        // Inventory update
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            $product->quantity -= $item['quantity'];
            $product->save();
        }
        
        // Order creation
        $order = Order::create([...]);
        
        // Email notification
        Mail::to($request->email)->send(new OrderConfirmation($order));
    }
}
```

```php
// GOOD: Dedicated controller (coordinator) class
class CheckoutService 
{
    public function __construct(
        private PaymentProcessor $paymentProcessor,
        private InventoryService $inventoryService,
        private NotificationService $notificationService
    ) {}
    
    public function processCheckout(CheckoutData $data): Order 
    {
        // Coordinate the entire checkout process
        $payment = $this->paymentProcessor->charge($data->total, $data->card);
        
        $this->inventoryService->reserveItems($data->items);
        
        $order = Order::createFromCheckout($data, $payment);
        
        $this->notificationService->sendOrderConfirmation($order);
        
        return $order;
    }
}

class OrderController 
{
    public function checkout(CheckoutRequest $request, CheckoutService $checkoutService) 
    {
        $order = $checkoutService->processCheckout(
            CheckoutData::fromRequest($request)
        );
        
        return response()->json($order);
    }
}
```

### 4. 🔗 Low Coupling
**"Assign responsibilities to minimize dependencies between classes"**

#### High Coupling (Bad)
```php
class OrderProcessor 
{
    public function processOrder(Order $order) 
    {
        // Directly dependent on specific email class
        $emailer = new SMTPEmailService();
        $emailer->sendOrderConfirmation($order->customer_email, $order);
        
        // Directly dependent on specific payment class
        $payment = new StripePaymentProcessor();
        $payment->charge($order->total, $order->payment_method);
        
        // Directly dependent on specific inventory class
        $inventory = new MySQLInventoryService();
        $inventory->decrementStock($order->items);
    }
}
```

#### Low Coupling (Good)
```php
class OrderProcessor 
{
    public function __construct(
        private EmailServiceInterface $emailService,
        private PaymentProcessorInterface $paymentProcessor,
        private InventoryServiceInterface $inventoryService
    ) {}
    
    public function processOrder(Order $order) 
    {
        // Depends only on interfaces, not concrete classes
        $this->emailService->sendOrderConfirmation($order->customer_email, $order);
        $this->paymentProcessor->charge($order->total, $order->payment_method);
        $this->inventoryService->decrementStock($order->items);
    }
}
```

### 5. ⬆️ High Cohesion
**"Assign responsibilities so that classes are focused and manageable"**

#### Low Cohesion (Bad)
```php
class UserManager 
{
    // User authentication
    public function login(string $email, string $password) { /* ... */ }
    public function logout(User $user) { /* ... */ }
    
    // File upload
    public function uploadAvatar(User $user, UploadedFile $file) { /* ... */ }
    public function deleteFile(string $path) { /* ... */ }
    
    // Email sending
    public function sendWelcomeEmail(User $user) { /* ... */ }
    public function sendPasswordReset(string $email) { /* ... */ }
    
    // Report generation
    public function generateUserReport() { /* ... */ }
    public function exportToPDF(array $data) { /* ... */ }
}
```

#### High Cohesion (Good)
```php
// Each class has a single, focused purpose
class AuthenticationService 
{
    public function login(string $email, string $password): User { /* ... */ }
    public function logout(User $user): void { /* ... */ }
    public function resetPassword(string $email): void { /* ... */ }
}

class FileUploadService 
{
    public function uploadAvatar(User $user, UploadedFile $file): string { /* ... */ }
    public function deleteFile(string $path): bool { /* ... */ }
    public function validateFile(UploadedFile $file): bool { /* ... */ }
}

class UserNotificationService 
{
    public function sendWelcomeEmail(User $user): void { /* ... */ }
    public function sendPasswordReset(string $email): void { /* ... */ }
    public function sendAccountSuspension(User $user): void { /* ... */ }
}

class UserReportService 
{
    public function generateUserReport(): array { /* ... */ }
    public function exportToPDF(array $data): string { /* ... */ }
    public function generateCSV(array $data): string { /* ... */ }
}
```

### 6. 🔄 Polymorphism
**"Use polymorphic operations instead of conditional logic based on type"**

#### Without Polymorphism (Bad)
```php
class PaymentProcessor 
{
    public function processPayment(Payment $payment) 
    {
        if ($payment->type === 'credit_card') {
            // Credit card processing logic
            $this->chargeCreditCard($payment);
        } elseif ($payment->type === 'paypal') {
            // PayPal processing logic  
            $this->chargePayPal($payment);
        } elseif ($payment->type === 'bank_transfer') {
            // Bank transfer logic
            $this->processBankTransfer($payment);
        }
        // Adding new payment type = modifying this method
    }
}
```

#### With Polymorphism (Good)
```php
interface PaymentProcessorInterface 
{
    public function process(Payment $payment): PaymentResult;
}

class CreditCardProcessor implements PaymentProcessorInterface 
{
    public function process(Payment $payment): PaymentResult 
    {
        // Credit card specific logic
    }
}

class PayPalProcessor implements PaymentProcessorInterface 
{
    public function process(Payment $payment): PaymentResult 
    {
        // PayPal specific logic
    }
}

class BankTransferProcessor implements PaymentProcessorInterface 
{
    public function process(Payment $payment): PaymentResult 
    {
        // Bank transfer specific logic  
    }
}

class PaymentService 
{
    public function __construct(private array $processors) {}
    
    public function processPayment(Payment $payment) 
    {
        $processor = $this->processors[$payment->type];
        return $processor->process($payment);
        // Adding new payment type = just add new processor class
    }
}
```

### 7. 🛡️ Pure Fabrication
**"Create a class that doesn't represent a real-world entity but serves a specific design purpose"**

#### Problem: No natural place for logic
```php
// Where should we put database persistence logic?
// User class? Too many responsibilities.
// Controller? Wrong layer.
// Service? What kind of service?

class User extends Model 
{
    // Should User know how to save itself to different databases?
    public function saveToMySQL() { /* ... */ }
    public function saveToRedis() { /* ... */ }
    public function saveToAPI() { /* ... */ }
}
```

#### Solution: Create artificial class
```php
// Pure fabrication - doesn't exist in real world but solves design problem
class UserRepository 
{
    public function save(User $user): void { /* ... */ }
    public function findById(int $id): ?User { /* ... */ }
    public function findByEmail(string $email): ?User { /* ... */ }
    public function delete(User $user): void { /* ... */ }
}

class UserCacheRepository 
{
    public function remember(string $key, User $user): void { /* ... */ }
    public function forget(string $key): void { /* ... */ }
    public function get(string $key): ?User { /* ... */ }
}
```

### 8. 🔒 Indirection
**"Add a level of indirection to reduce direct coupling"**

#### Direct Coupling (Hard to change)
```php
class OrderService 
{
    public function createOrder(array $data) 
    {
        $order = Order::create($data);
        
        // Direct dependency on specific email service
        $emailer = new SendGridEmailService();
        $emailer->send($order->customer_email, 'Order Confirmation', $this->buildEmailBody($order));
        
        return $order;
    }
}
```

#### Indirect Coupling (Flexible)
```php
class OrderService 
{
    // Indirection through interface
    public function __construct(private EmailServiceInterface $emailService) {}
    
    public function createOrder(array $data) 
    {
        $order = Order::create($data);
        
        // Indirect dependency - can be any implementation
        $this->emailService->send(
            $order->customer_email, 
            'Order Confirmation', 
            $this->buildEmailBody($order)
        );
        
        return $order;
    }
}

// Can easily switch implementations
$orderService = new OrderService(new SendGridEmailService());
// or
$orderService = new OrderService(new MailgunEmailService());
// or  
$orderService = new OrderService(new LogEmailService()); // for testing
```

### 9. 🛡️ Protected Variations
**"Identify points of predicted variation and assign responsibilities to create a stable interface"**

#### Problem: Code breaks when external systems change
```php
class ProductService 
{
    public function getProductData(int $productId) 
    {
        // Directly tied to specific API format
        $response = Http::get("https://api.supplier.com/v1/products/{$productId}");
        
        return [
            'name' => $response['product_name'],      // What if they change 'product_name' to 'name'?
            'price' => $response['price_usd'],        // What if they change currency format?
            'stock' => $response['inventory_count']   // What if they change field name?
        ];
    }
}
```

#### Solution: Protect against variations
```php
interface ProductDataProviderInterface 
{
    public function getProduct(int $productId): ProductData;
}

class SupplierAPIAdapter implements ProductDataProviderInterface 
{
    public function getProduct(int $productId): ProductData 
    {
        $response = Http::get("https://api.supplier.com/v1/products/{$productId}");
        
        // Adapter handles API format changes
        return new ProductData(
            name: $response['product_name'] ?? $response['name'] ?? 'Unknown',
            price: $this->parsePrice($response['price_usd'] ?? $response['price']),
            stock: $response['inventory_count'] ?? $response['stock'] ?? 0
        );
    }
    
    private function parsePrice($priceData): float 
    {
        // Handle different price formats
        if (is_array($priceData)) {
            return $priceData['amount'] ?? 0.0;
        }
        return (float) $priceData;
    }
}

class ProductService 
{
    public function __construct(private ProductDataProviderInterface $dataProvider) {}
    
    public function getProductData(int $productId): ProductData 
    {
        // Protected from API changes
        return $this->dataProvider->getProduct($productId);
    }
}
```

## Applying GRASP to Laravel

### Example: Blog Post Management System

Let's design a blog system using GRASP principles:

```php
// Information Expert: Post knows about its own data
class Post extends Model 
{
    public function isPublished(): bool 
    {
        return $this->published_at !== null && $this->published_at <= now();
    }
    
    public function canBeEditedBy(User $user): bool 
    {
        return $this->author_id === $user->id || $user->hasRole('editor');
    }
    
    public function getReadingTime(): int 
    {
        return ceil(str_word_count($this->content) / 200); // 200 words per minute
    }
}

// Creator: Post creates its own comments
class Post extends Model 
{
    public function addComment(User $user, string $content): Comment 
    {
        return $this->comments()->create([
            'user_id' => $user->id,
            'content' => $content,
            'approved' => $this->auto_approve_comments
        ]);
    }
}

// Controller: Coordinates blog publishing workflow
class PostPublishingService 
{
    public function __construct(
        private PostRepository $postRepository,
        private NotificationService $notificationService,
        private CacheService $cacheService,
        private SearchIndexService $searchService
    ) {}
    
    public function publishPost(Post $post): void 
    {
        $post->publish();
        $this->postRepository->save($post);
        
        $this->cacheService->invalidatePostCache($post);
        $this->searchService->indexPost($post);
        $this->notificationService->notifySubscribers($post);
    }
}

// Low Coupling: Using interfaces
interface NotificationServiceInterface 
{
    public function notifySubscribers(Post $post): void;
}

class EmailNotificationService implements NotificationServiceInterface 
{
    public function notifySubscribers(Post $post): void 
    {
        // Email implementation
    }
}

// High Cohesion: Focused responsibilities
class PostStatisticsService 
{
    public function getViewCount(Post $post): int { /* ... */ }
    public function getShareCount(Post $post): int { /* ... */ }
    public function getEngagementRate(Post $post): float { /* ... */ }
    public function getPopularityScore(Post $post): float { /* ... */ }
}

// Polymorphism: Different content types
interface ContentRenderer 
{
    public function render(string $content): string;
}

class MarkdownRenderer implements ContentRenderer 
{
    public function render(string $content): string 
    {
        return $this->parseMarkdown($content);
    }
}

class HTMLRenderer implements ContentRenderer 
{
    public function render(string $content): string 
    {
        return $this->sanitizeHTML($content);
    }
}

// Pure Fabrication: Repository pattern
class PostRepository 
{
    public function findPublishedByTag(string $tag): Collection { /* ... */ }
    public function getRecentPosts(int $limit = 10): Collection { /* ... */ }
    public function searchByKeyword(string $keyword): Collection { /* ... */ }
}

// Protected Variations: External service adapter
class SocialMediaAdapter 
{
    public function shareToFacebook(Post $post): void { /* Handles Facebook API changes */ }
    public function shareToTwitter(Post $post): void { /* Handles Twitter API changes */ }
    public function shareToLinkedIn(Post $post): void { /* Handles LinkedIn API changes */ }
}
```

## GRASP Decision Framework

When adding new functionality, ask these questions in order:

1. **Information Expert**: Which class has the data needed for this responsibility?
2. **Creator**: Which class should create new objects for this functionality?
3. **Controller**: Do we need a coordinator class for this complex operation?
4. **Low Coupling**: How can we minimize dependencies?
5. **High Cohesion**: Does this responsibility fit with the class's existing purpose?
6. **Polymorphism**: Can we use inheritance/interfaces instead of if/else logic?
7. **Pure Fabrication**: Do we need to create an artificial class to solve this design problem?
8. **Indirection**: Should we add a layer to make this more flexible?
9. **Protected Variations**: What might change, and how can we protect against it?

## Common GRASP Mistakes

### 1. Ignoring Information Expert
```php
// BAD: Controller does work that belongs to the model
class UserController 
{
    public function show(User $user) 
    {
        $fullName = $user->first_name . ' ' . $user->last_name;
        $isActive = $user->last_login_at > now()->subDays(30);
        
        return view('user.show', compact('user', 'fullName', 'isActive'));
    }
}

// GOOD: Model does its own work
class User extends Model 
{
    public function getFullName(): string 
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    public function isActive(): bool 
    {
        return $this->last_login_at > now()->subDays(30);
    }
}

class UserController 
{
    public function show(User $user) 
    {
        return view('user.show', compact('user'));
    }
}
```

### 2. Over-using Controller Pattern
```php
// BAD: Everything goes through a "manager" or "service"
class UserManager 
{
    public function getUserName(User $user) { return $user->name; }
    public function getUserEmail(User $user) { return $user->email; }
    public function isUserActive(User $user) { return $user->active; }
}

// GOOD: Let objects manage themselves
class User extends Model 
{
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function isActive(): bool { return $this->active; }
}
```

## Ready for Advanced GRASP

**You're ready for the advanced GRASP material when you can**:

- [ ] Identify which class should handle a new responsibility
- [ ] Recognize when coupling is too tight
- [ ] Create focused, cohesive classes
- [ ] Use polymorphism instead of conditional logic
- [ ] Design stable interfaces that protect against changes
- [ ] Apply multiple GRASP principles together systematically

The advanced GRASP README shows these principles applied to complex Laravel architectures with multiple interacting systems. You'll see how GRASP decisions compound to create maintainable, flexible applications.
