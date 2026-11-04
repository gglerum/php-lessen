# Clean Code Principles - Junior Developer Companion

This guide prepares you to understand clean code principles by building from basic coding habits to professional-quality, readable code that communicates clearly with other developers.

## Before You Start

**Prerequisites**: 
- Basic PHP OOP understanding
- Laravel basics from `laravel-basics.md`
- Understanding of design principles from `general-principles-companion.md`

**What you'll gain**: The ability to write code that reads like well-written prose, making you a more valuable team member and improving your debugging and maintenance speed.

## What is Clean Code?

**Clean Code** = Code that is easy to read, understand, and modify.

Think about the difference between these two text messages:
- "hey wyd l8r? wanna hang? lmk"
- "Hi! What are you doing later? Would you like to spend time together? Let me know."

Both communicate the same information, but one is much easier to understand, especially for someone unfamiliar with texting shortcuts. Clean code follows the second approach.

## The Communication Problem

### Code as Communication
```php
// This code works, but what does it do?
class UC 
{
    private $d;
    private $ep;
    
    public function cU($dt) 
    {
        $u = new U();
        $u->n = $dt['n'];
        $u->e = $dt['e'];
        $u->p = hash('sha256', $dt['p']);
        $u->ca = date('Y-m-d H:i:s');
        
        if ($this->vE($u->e)) {
            $this->d->s($u);
            $this->ep->sWE($u);
            return true;
        }
        return false;
    }
    
    private function vE($e) 
    {
        return filter_var($e, FILTER_VALIDATE_EMAIL) && 
               !$this->d->fBE($e);
    }
}
```

**Problems**:
- **Meaningless names**: What is UC? What is cU? What is dt?
- **Single letter variables**: What is $d, $ep, $u?
- **Abbreviated methods**: What does vE do? sWE?
- **No context**: Why are we hashing passwords? What's the business logic?

### The Same Code, Written Cleanly
```php
class UserCreationService 
{
    private $userRepository;
    private $emailProcessor;
    
    public function createUser(array $userData): bool 
    {
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = hash('sha256', $userData['password']);
        $user->created_at = date('Y-m-d H:i:s');
        
        if ($this->isValidEmail($user->email)) {
            $this->userRepository->save($user);
            $this->emailProcessor->sendWelcomeEmail($user);
            return true;
        }
        return false;
    }
    
    private function isValidEmail(string $email): bool 
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && 
               !$this->userRepository->findByEmail($email);
    }
}
```

**Now it's clear**:
- **Purpose**: Creating users
- **Process**: Validate email, save user, send welcome email
- **Logic**: Email must be valid and not already exist

## The Core Clean Code Principles

### 1. 📝 Meaningful Names

**The Principle**: Names should reveal intention. A reader should understand what something does without needing comments.

#### Bad Names vs Good Names
```php
// BAD: Meaningless, abbreviated, misleading
class PM 
{
    private $d1;  // What is d1?
    private $d2;  // What is d2?
    
    public function calc($x) // Calculate what?
    {
        if ($x > $this->d1 && $x < $this->d2) {
            return $x * 0.1;
        }
        return 0;
    }
    
    public function process($data) // Process how?
    {
        foreach ($data as $item) {
            $temp = $this->calc($item['amount']); // What is temp?
            $item['fee'] = $temp;
        }
        return $data;
    }
}
```

```php
// GOOD: Self-documenting names
class PaymentCalculator 
{
    private $minimumChargeableAmount;
    private $maximumChargeableAmount;
    
    public function calculateProcessingFee(float $paymentAmount): float 
    {
        if ($this->isChargeableAmount($paymentAmount)) {
            return $paymentAmount * 0.1; // 10% processing fee
        }
        return 0.0;
    }
    
    public function addProcessingFeesToPayments(array $payments): array 
    {
        foreach ($payments as &$payment) {
            $processingFee = $this->calculateProcessingFee($payment['amount']);
            $payment['processing_fee'] = $processingFee;
        }
        return $payments;
    }
    
    private function isChargeableAmount(float $amount): bool 
    {
        return $amount >= $this->minimumChargeableAmount && 
               $amount <= $this->maximumChargeableAmount;
    }
}
```

#### Laravel Examples
```php
// BAD: Generic Laravel names that don't communicate purpose
class PostController 
{
    public function store(Request $r) 
    {
        $p = new Post();
        $p->t = $r->input('title');
        $p->c = $r->input('content');
        $p->u = auth()->id();
        $p->save();
        
        return redirect('/posts');
    }
}
```

```php
// GOOD: Clear, intentional names
class BlogPostController 
{
    public function publishNewPost(PublishPostRequest $request) 
    {
        $blogPost = new BlogPost();
        $blogPost->title = $request->input('title');
        $blogPost->content = $request->input('content');
        $blogPost->author_id = auth()->id();
        $blogPost->published_at = now();
        $blogPost->save();
        
        return redirect()->route('blog.index')
                        ->with('success', 'Blog post published successfully');
    }
}
```

### 2. 🎯 Small Functions

**The Principle**: Functions should be small and do one thing well. If you need to add "and" to describe what a function does, it's doing too much.

#### Large, Multi-Purpose Function
```php
// BAD: One function doing everything
class OrderProcessor 
{
    public function processOrder(Request $request) 
    {
        // Validation (15 lines)
        if (!$request->has('items') || count($request->items) == 0) {
            throw new Exception('No items');
        }
        foreach ($request->items as $item) {
            if (!isset($item['product_id']) || !is_numeric($item['product_id'])) {
                throw new Exception('Invalid product ID');
            }
            if (!isset($item['quantity']) || $item['quantity'] <= 0) {
                throw new Exception('Invalid quantity');
            }
        }
        
        // Calculate total (20 lines)
        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new Exception('Product not found');
            }
            $lineTotal = $product->price * $item['quantity'];
            $subtotal += $lineTotal;
        }
        
        $taxRate = 0.08;
        $tax = $subtotal * $taxRate;
        $shipping = $subtotal > 100 ? 0 : 15;
        $total = $subtotal + $tax + $shipping;
        
        // Create order (15 lines)
        $order = new Order();
        $order->user_id = auth()->id();
        $order->subtotal = $subtotal;
        $order->tax = $tax;
        $order->shipping = $shipping;
        $order->total = $total;
        $order->status = 'pending';
        $order->save();
        
        // Create order items (10 lines)
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $product->price;
            $orderItem->save();
        }
        
        // Send confirmation email (10 lines)
        $customerEmail = auth()->user()->email;
        $subject = 'Order Confirmation #' . $order->id;
        $body = 'Thank you for your order. Total: $' . $total;
        Mail::raw($body, function($message) use ($customerEmail, $subject) {
            $message->to($customerEmail)->subject($subject);
        });
        
        // Update inventory (10 lines)
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $product->stock -= $item['quantity'];
            if ($product->stock < 0) {
                throw new Exception('Insufficient stock for product: ' . $product->name);
            }
            $product->save();
        }
        
        return $order;
    }
}
```

#### Small, Focused Functions
```php
// GOOD: Each function has one clear purpose
class OrderProcessor 
{
    public function processOrder(array $orderData): Order 
    {
        $this->validateOrderData($orderData);
        
        $orderCalculation = $this->calculateOrderTotals($orderData['items']);
        $order = $this->createOrder($orderCalculation);
        $this->createOrderItems($order, $orderData['items']);
        $this->sendOrderConfirmation($order);
        $this->updateInventory($orderData['items']);
        
        return $order;
    }
    
    private function validateOrderData(array $orderData): void 
    {
        if (empty($orderData['items'])) {
            throw new InvalidOrderException('Order must contain items');
        }
        
        foreach ($orderData['items'] as $item) {
            $this->validateOrderItem($item);
        }
    }
    
    private function validateOrderItem(array $item): void 
    {
        if (!$this->isValidProductId($item['product_id'] ?? null)) {
            throw new InvalidOrderException('Invalid product ID');
        }
        
        if (!$this->isValidQuantity($item['quantity'] ?? null)) {
            throw new InvalidOrderException('Invalid quantity');
        }
    }
    
    private function calculateOrderTotals(array $items): OrderCalculation 
    {
        $subtotal = $this->calculateSubtotal($items);
        $tax = $this->calculateTax($subtotal);
        $shipping = $this->calculateShipping($subtotal);
        
        return new OrderCalculation($subtotal, $tax, $shipping);
    }
    
    private function calculateSubtotal(array $items): float 
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal += $product->price * $item['quantity'];
        }
        return $subtotal;
    }
    
    private function calculateTax(float $subtotal): float 
    {
        return $subtotal * config('order.tax_rate', 0.08);
    }
    
    private function calculateShipping(float $subtotal): float 
    {
        return $subtotal > config('order.free_shipping_threshold', 100) ? 0 : 15;
    }
    
    private function createOrder(OrderCalculation $calculation): Order 
    {
        return Order::create([
            'user_id' => auth()->id(),
            'subtotal' => $calculation->getSubtotal(),
            'tax' => $calculation->getTax(),
            'shipping' => $calculation->getShipping(),
            'total' => $calculation->getTotal(),
            'status' => 'pending'
        ]);
    }
    
    private function createOrderItems(Order $order, array $items): void 
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);
        }
    }
    
    private function sendOrderConfirmation(Order $order): void 
    {
        Mail::to($order->user->email)
            ->send(new OrderConfirmationMail($order));
    }
    
    private function updateInventory(array $items): void 
    {
        foreach ($items as $item) {
            $this->decrementProductStock($item['product_id'], $item['quantity']);
        }
    }
    
    private function decrementProductStock(int $productId, int $quantity): void 
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock < $quantity) {
            throw new InsufficientStockException("Not enough stock for {$product->name}");
        }
        
        $product->decrement('stock', $quantity);
    }
    
    // Helper methods
    private function isValidProductId($productId): bool 
    {
        return is_numeric($productId) && Product::where('id', $productId)->exists();
    }
    
    private function isValidQuantity($quantity): bool 
    {
        return is_numeric($quantity) && $quantity > 0;
    }
}

// Value object for calculations
class OrderCalculation 
{
    public function __construct(
        private float $subtotal,
        private float $tax,
        private float $shipping
    ) {}
    
    public function getSubtotal(): float { return $this->subtotal; }
    public function getTax(): float { return $this->tax; }
    public function getShipping(): float { return $this->shipping; }
    public function getTotal(): float { return $this->subtotal + $this->tax + $this->shipping; }
}
```

### 3. 📝 Comments vs Self-Documenting Code

**The Principle**: Code should be self-explanatory. Comments should explain WHY, not WHAT.

#### Bad Comments (Explain What)
```php
// BAD: Comments that repeat the code
class UserService 
{
    public function createUser(array $userData) 
    {
        // Create a new user object
        $user = new User();
        
        // Set the user's name
        $user->name = $userData['name'];
        
        // Set the user's email
        $user->email = $userData['email'];
        
        // Hash the password
        $user->password = Hash::make($userData['password']);
        
        // Set created at timestamp
        $user->created_at = now();
        
        // Save the user to database
        $user->save();
        
        // Return the user
        return $user;
    }
    
    public function calculateAge($birthDate) 
    {
        // Get current date
        $now = new DateTime();
        
        // Create datetime from birth date
        $birth = new DateTime($birthDate);
        
        // Calculate difference
        $age = $now->diff($birth);
        
        // Return years
        return $age->y;
    }
}
```

#### Good Comments (Explain Why) + Self-Documenting Code
```php
// GOOD: Self-documenting code with meaningful WHY comments
class UserRegistrationService 
{
    public function registerNewUser(array $registrationData): User 
    {
        // Using separate method because registration might need additional
        // steps like email verification, welcome bonus, etc. in the future
        return $this->createUserAccount($registrationData);
    }
    
    private function createUserAccount(array $userData): User 
    {
        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        
        // Using Laravel's Hash facade instead of PHP's password_hash() 
        // to maintain consistency with Laravel's authentication system
        $user->password = Hash::make($userData['password']);
        
        $user->save();
        
        return $user;
    }
    
    public function calculateUserAge(string $birthDateString): int 
    {
        $currentDate = new DateTime();
        $birthDate = new DateTime($birthDateString);
        
        return $currentDate->diff($birthDate)->y;
    }
    
    public function calculateLoyaltyBonus(User $user): float 
    {
        $membershipYears = $this->calculateUserAge($user->created_at);
        
        // Business rule: 5% bonus per year of membership, capped at 50%
        // This encourages long-term customer retention
        return min($membershipYears * 0.05, 0.50);
    }
}
```

#### When Comments ARE Needed
```php
class PaymentProcessor 
{
    public function processRefund(Payment $payment, float $amount): RefundResult 
    {
        // VISA requires refunds to be processed within 180 days of original transaction
        // MasterCard allows 120 days. We use the stricter limit for consistency.
        $maxRefundDays = 120;
        
        if ($payment->created_at->diffInDays(now()) > $maxRefundDays) {
            throw new RefundTooLateException('Refund window has expired');
        }
        
        // Stripe charges a $0.30 fee for each refund, regardless of amount
        // We absorb this cost for customer satisfaction
        $refundAmount = $amount; // Not reducing for Stripe fee
        
        return $this->processStripeRefund($payment->stripe_id, $refundAmount);
    }
    
    private function calculateDynamicFee(float $amount): float 
    {
        // Complex fee structure based on partner agreements:
        // - Under $10: flat $0.50 fee (partner requirement from FirstData)
        // - $10-$100: 3% (industry standard)
        // - Over $100: 2.5% (volume discount from processor)
        // These rates were negotiated in Q3 2024 contract
        
        if ($amount < 10) {
            return 0.50;
        } elseif ($amount <= 100) {
            return $amount * 0.03;
        } else {
            return $amount * 0.025;
        }
    }
}
```

### 4. 🏗️ Consistent Formatting and Structure

**The Principle**: Code should follow consistent patterns that make it easy to scan and understand.

#### Inconsistent Structure
```php
// BAD: Inconsistent formatting makes code hard to read
class OrderService{
    private $tax_rate=0.08;
    private $free_shipping_min = 100;
    
public function calculateTotal($items)
{
$subtotal=0;
    foreach($items as $item){
        $subtotal+=$item['price']*$item['qty'];
    }
    
    $tax=$subtotal*$this->tax_rate;
    
        if($subtotal>$this->free_shipping_min){
    $shipping=0;
        } else {
            $shipping = 15;
        }
    
return $subtotal+$tax+$shipping;
}

    public function processOrder($orderData) {
        $total = $this->calculateTotal($orderData['items']);
        
        $order = Order::create(['total'=>$total,'user_id'=>auth()->id()]);
        
        foreach($orderData['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id'=>$item['product_id'],
                'quantity'=>$item['qty'],
                'price' => $item['price']
            ]);
        }
        
        return$order;
    }
}
```

#### Consistent Structure
```php
// GOOD: Consistent formatting makes code easy to scan
class OrderService 
{
    private float $taxRate = 0.08;
    private float $freeShippingMinimum = 100.00;
    
    public function calculateTotal(array $items): float 
    {
        $subtotal = $this->calculateSubtotal($items);
        $tax = $this->calculateTax($subtotal);
        $shipping = $this->calculateShipping($subtotal);
        
        return $subtotal + $tax + $shipping;
    }
    
    public function processOrder(array $orderData): Order 
    {
        $total = $this->calculateTotal($orderData['items']);
        
        $order = Order::create([
            'total' => $total,
            'user_id' => auth()->id(),
            'status' => 'pending'
        ]);
        
        $this->createOrderItems($order, $orderData['items']);
        
        return $order;
    }
    
    private function calculateSubtotal(array $items): float 
    {
        $subtotal = 0.0;
        
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return $subtotal;
    }
    
    private function calculateTax(float $subtotal): float 
    {
        return $subtotal * $this->taxRate;
    }
    
    private function calculateShipping(float $subtotal): float 
    {
        return $subtotal >= $this->freeShippingMinimum ? 0.0 : 15.0;
    }
    
    private function createOrderItems(Order $order, array $items): void 
    {
        foreach ($items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }
    }
}
```

### 5. 🎯 Avoid Mental Mapping

**The Principle**: Don't make readers translate your variable names. Use names that directly represent what they contain.

#### Mental Mapping Required
```php
// BAD: Reader has to remember what each variable represents
class ReportGenerator 
{
    public function generateReport($s, $e, $t) 
    {
        $d = [];
        $c = 0;
        
        // What is $i? What is $r?
        for ($i = $s; $i <= $e; $i++) {
            $r = $this->getDataForDay($i);
            if ($r) {
                $d[] = $r;
                $c++;
            }
        }
        
        if ($t === 'summary') {
            return $this->createSummary($d, $c);
        } elseif ($t === 'detailed') {
            return $this->createDetailed($d);
        }
        
        return null;
    }
    
    private function processItems($items) 
    {
        $x = [];  // What is x?
        $y = 0;   // What is y?
        
        foreach ($items as $i) {  // What is i?
            if ($i['status'] === 'active') {
                $x[] = $i;
                $y += $i['value'];
            }
        }
        
        return ['items' => $x, 'total' => $y];
    }
}
```

#### No Mental Mapping Needed
```php
// GOOD: Variables directly represent their purpose
class SalesReportGenerator 
{
    public function generateSalesReport(DateTime $startDate, DateTime $endDate, string $reportType): ?array 
    {
        $salesData = [];
        $totalDaysWithSales = 0;
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dailySales = $this->getSalesForDate($currentDate);
            
            if ($dailySales) {
                $salesData[] = $dailySales;
                $totalDaysWithSales++;
            }
            
            $currentDate->modify('+1 day');
        }
        
        return match($reportType) {
            'summary' => $this->createSummaryReport($salesData, $totalDaysWithSales),
            'detailed' => $this->createDetailedReport($salesData),
            default => null
        };
    }
    
    private function filterActiveItems(array $items): array 
    {
        $activeItems = [];
        $totalValue = 0.0;
        
        foreach ($items as $item) {
            if ($this->isActiveItem($item)) {
                $activeItems[] = $item;
                $totalValue += $item['value'];
            }
        }
        
        return [
            'active_items' => $activeItems,
            'total_value' => $totalValue
        ];
    }
    
    private function isActiveItem(array $item): bool 
    {
        return $item['status'] === 'active';
    }
}
```

### 6. 🔧 Error Handling

**The Principle**: Handle errors gracefully and provide meaningful error messages. Don't fail silently.

#### Poor Error Handling
```php
// BAD: Silent failures and unclear errors
class FileProcessor 
{
    public function processFile($filename) 
    {
        $content = file_get_contents($filename); // Could fail silently
        if (!$content) {
            return false; // What went wrong?
        }
        
        $data = json_decode($content); // Could fail silently
        if (!$data) {
            return false; // JSON invalid or file empty?
        }
        
        foreach ($data as $item) {
            $this->processItem($item); // What if this fails?
        }
        
        return true; // Success, but what actually happened?
    }
    
    private function processItem($item) 
    {
        // Assumes $item has required fields
        $result = $this->apiCall($item['id'], $item['data']);
        
        if ($result) {
            // Do something
        }
        // What if result is false? Silent failure.
    }
}
```

#### Proper Error Handling
```php
// GOOD: Clear error handling with meaningful messages
class FileProcessor 
{
    public function processFile(string $filename): ProcessingResult 
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException("File not found: {$filename}");
        }
        
        $content = $this->readFileContent($filename);
        $data = $this->parseJsonContent($content);
        $results = $this->processAllItems($data);
        
        return new ProcessingResult($results['processed'], $results['failed'], $results['errors']);
    }
    
    private function readFileContent(string $filename): string 
    {
        $content = file_get_contents($filename);
        
        if ($content === false) {
            throw new FileReadException("Unable to read file: {$filename}. Check file permissions.");
        }
        
        if (empty($content)) {
            throw new EmptyFileException("File is empty: {$filename}");
        }
        
        return $content;
    }
    
    private function parseJsonContent(string $content): array 
    {
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException("Invalid JSON format: " . json_last_error_msg());
        }
        
        if (!is_array($data)) {
            throw new InvalidDataFormatException("Expected JSON array, got: " . gettype($data));
        }
        
        return $data;
    }
    
    private function processAllItems(array $items): array 
    {
        $processed = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($items as $index => $item) {
            try {
                $this->processItem($item);
                $processed++;
            } catch (ItemProcessingException $e) {
                $failed++;
                $errors[] = "Item {$index}: " . $e->getMessage();
            }
        }
        
        return [
            'processed' => $processed,
            'failed' => $failed,
            'errors' => $errors
        ];
    }
    
    private function processItem(array $item): void 
    {
        if (!isset($item['id'])) {
            throw new ItemProcessingException('Missing required field: id');
        }
        
        if (!isset($item['data'])) {
            throw new ItemProcessingException('Missing required field: data');
        }
        
        $result = $this->apiCall($item['id'], $item['data']);
        
        if (!$result->isSuccessful()) {
            throw new ItemProcessingException("API call failed: " . $result->getErrorMessage());
        }
    }
}

// Clear result object
class ProcessingResult 
{
    public function __construct(
        private int $processedCount,
        private int $failedCount,
        private array $errors
    ) {}
    
    public function wasSuccessful(): bool 
    {
        return $this->failedCount === 0;
    }
    
    public function getProcessedCount(): int 
    {
        return $this->processedCount;
    }
    
    public function getFailedCount(): int 
    {
        return $this->failedCount;
    }
    
    public function getErrors(): array 
    {
        return $this->errors;
    }
    
    public function getSummary(): string 
    {
        return "Processed: {$this->processedCount}, Failed: {$this->failedCount}";
    }
}
```

## Laravel-Specific Clean Code Practices

### 1. Clean Controllers
```php
// BAD: Fat controller with mixed concerns
class PostController extends Controller 
{
    public function store(Request $request) 
    {
        // Validation in controller
        $this->validate($request, [
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'array'
        ]);
        
        // Business logic in controller
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->author_id = auth()->id();
        $post->slug = Str::slug($request->title);
        
        if (Post::where('slug', $post->slug)->exists()) {
            $post->slug = $post->slug . '-' . time();
        }
        
        $post->save();
        
        // Tag handling in controller
        if ($request->has('tags')) {
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $post->tags()->attach($tag->id);
            }
        }
        
        // Email sending in controller
        Mail::to($post->author->email)->send(new PostPublishedMail($post));
        
        return redirect()->route('posts.show', $post->slug);
    }
}
```

```php
// GOOD: Thin controller with clear responsibilities
class BlogPostController extends Controller 
{
    public function __construct(
        private BlogPostService $blogPostService
    ) {}
    
    public function publishNewPost(PublishPostRequest $request) 
    {
        $publishedPost = $this->blogPostService->publishPost(
            author: auth()->user(),
            postData: $request->validated()
        );
        
        return redirect()
            ->route('blog.show', $publishedPost->slug)
            ->with('success', 'Your blog post has been published successfully!');
    }
}

// Clean Form Request for validation
class PublishPostRequest extends FormRequest 
{
    public function authorize(): bool 
    {
        return auth()->check();
    }
    
    public function rules(): array 
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'tags' => ['sometimes', 'array', 'max:5'],
            'tags.*' => ['string', 'max:50']
        ];
    }
    
    public function messages(): array 
    {
        return [
            'content.min' => 'Blog post content should be at least 10 characters long.',
            'tags.max' => 'You can add a maximum of 5 tags to your post.',
        ];
    }
}

// Clean service with single responsibility
class BlogPostService 
{
    public function __construct(
        private TagService $tagService,
        private NotificationService $notificationService
    ) {}
    
    public function publishPost(User $author, array $postData): BlogPost 
    {
        $blogPost = $this->createBlogPost($author, $postData);
        
        if (!empty($postData['tags'])) {
            $this->tagService->attachTagsToPost($blogPost, $postData['tags']);
        }
        
        $this->notificationService->notifyAuthorOfPublication($blogPost);
        
        return $blogPost;
    }
    
    private function createBlogPost(User $author, array $postData): BlogPost 
    {
        return BlogPost::create([
            'title' => $postData['title'],
            'content' => $postData['content'],
            'author_id' => $author->id,
            'slug' => $this->generateUniqueSlug($postData['title']),
            'published_at' => now()
        ]);
    }
    
    private function generateUniqueSlug(string $title): string 
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        
        return $slug;
    }
}
```

### 2. Clean Models
```php
// BAD: Fat model with mixed concerns
class User extends Model 
{
    public function getFullNameAttribute() 
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    public function sendWelcomeEmail() 
    {
        Mail::to($this->email)->send(new WelcomeEmail($this));
    }
    
    public function calculateLoyaltyPoints($orderAmount) 
    {
        if ($this->is_premium) {
            return $orderAmount * 0.1;
        } else {
            return $orderAmount * 0.05;
        }
    }
    
    public function generateReport() 
    {
        $orders = $this->orders;
        $totalSpent = $orders->sum('total');
        $averageOrder = $totalSpent / $orders->count();
        
        return [
            'total_spent' => $totalSpent,
            'total_orders' => $orders->count(),
            'average_order' => $averageOrder
        ];
    }
}
```

```php
// GOOD: Focused model with clear boundaries
class User extends Model 
{
    protected $fillable = ['first_name', 'last_name', 'email'];
    
    protected $casts = [
        'is_premium' => 'boolean',
        'email_verified_at' => 'datetime'
    ];
    
    // Relationships
    public function orders(): HasMany 
    {
        return $this->hasMany(Order::class);
    }
    
    public function profile(): HasOne 
    {
        return $this->hasOne(UserProfile::class);
    }
    
    // Accessors (data presentation)
    public function getFullNameAttribute(): string 
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    public function getDisplayNameAttribute(): string 
    {
        return $this->getFullNameAttribute() ?: $this->email;
    }
    
    // Model-specific business logic
    public function isPremiumMember(): bool 
    {
        return $this->is_premium && $this->premium_expires_at?->isFuture();
    }
    
    public function hasVerifiedEmail(): bool 
    {
        return $this->email_verified_at !== null;
    }
    
    // Query scopes
    public function scopeActive(Builder $query): Builder 
    {
        return $query->where('is_active', true);
    }
    
    public function scopePremium(Builder $query): Builder 
    {
        return $query->where('is_premium', true)
                    ->where('premium_expires_at', '>', now());
    }
}

// External services handle complex business logic
class UserLoyaltyService 
{
    public function calculatePointsForOrder(User $user, float $orderAmount): int 
    {
        $multiplier = $user->isPremiumMember() ? 0.1 : 0.05;
        return (int) ($orderAmount * $multiplier);
    }
}

class UserReportService 
{
    public function generateSpendingReport(User $user): UserSpendingReport 
    {
        $orders = $user->orders()
                      ->where('status', 'completed')
                      ->get();
        
        $totalSpent = $orders->sum('total');
        $orderCount = $orders->count();
        $averageOrder = $orderCount > 0 ? $totalSpent / $orderCount : 0;
        
        return new UserSpendingReport($totalSpent, $orderCount, $averageOrder);
    }
}
```

## Common Clean Code Mistakes

### 1. Over-Commenting Obvious Code
```php
// BAD: Comments that just repeat the code
$user = new User(); // Create a new user
$user->name = $request->name; // Set the name
$user->save(); // Save to database
```

### 2. Using Meaningless Variable Names
```php
// BAD: What do these represent?
$d = $request->input('data');
$r = $this->process($d);
if ($r) {
    // What is r?
}

// GOOD: Clear intent
$userData = $request->input('user_data');
$validationResult = $this->validateUserData($userData);
if ($validationResult->isValid()) {
    // Clear what we're checking
}
```

### 3. Functions That Do Too Much
```php
// BAD: One function handles everything
public function handleUserRegistration($data) {
    // Validate (20 lines)
    // Create user (10 lines)
    // Send email (15 lines)
    // Update statistics (10 lines)
    // Log activity (5 lines)
}

// GOOD: Each function has one job
public function registerUser(array $userData): User {
    $validatedData = $this->validator->validate($userData);
    $user = $this->userCreator->create($validatedData);
    $this->emailService->sendWelcome($user);
    $this->analytics->recordRegistration($user);
    $this->logger->logUserRegistration($user);
    
    return $user;
}
```

## Clean Code Checklist

Before submitting code, ask yourself:

**Naming**:
- [ ] Do variable and function names clearly express their purpose?
- [ ] Can someone understand what the code does without comments?
- [ ] Are abbreviations avoided (except for well-known ones like `id`, `url`)?

**Functions**:
- [ ] Does each function do one thing well?
- [ ] Are functions small (ideally under 20 lines)?
- [ ] Do function names describe what they do?

**Structure**:
- [ ] Is the code consistently formatted?
- [ ] Are related concepts grouped together?
- [ ] Is there a logical flow from top to bottom?

**Comments**:
- [ ] Do comments explain WHY, not WHAT?
- [ ] Can the code be understood without the comments?
- [ ] Are there no dead or outdated comments?

**Error Handling**:
- [ ] Are errors handled gracefully with meaningful messages?
- [ ] Are there no silent failures?
- [ ] Are edge cases considered?

## Ready for Advanced Clean Code

**You're ready for the advanced Clean Code material when you can**:

- [ ] Write self-documenting code with meaningful names
- [ ] Keep functions small and focused on one task
- [ ] Organize code in a logical, consistent structure
- [ ] Handle errors gracefully with clear messaging
- [ ] Avoid mental mapping and abbreviations
- [ ] Refactor messy code into clean, readable code
- [ ] Apply these principles consistently across a Laravel application

The advanced Clean Code README shows these principles applied to complex Laravel applications with multiple developers, strict coding standards, and sophisticated business logic. You'll see how clean code practices scale to enterprise-level applications.
