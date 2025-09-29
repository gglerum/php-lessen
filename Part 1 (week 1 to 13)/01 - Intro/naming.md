# Professional PHP Naming Conventions

> **📍 Learning Path**: Step 5 of 6 → [Step 6: First Program](README.md#💡-practical-exercise) | [Back to Learning Path](README.md#🎯-your-learning-path)

Proper naming is one of the most important skills in professional development. Good names make code self-documenting, reduce bugs, and enable effective teamwork. Poor naming creates technical debt that compounds over time.

> **Why This Matters for Beginners**: When you're learning to code, you might think "I'll remember what this variable does." But even after a few days, `$x` or `$data` becomes meaningless. Good naming helps you understand your own code!

## 🎯 What You'll Master

By following these conventions, you'll:

- **Write self-documenting code** that explains its purpose clearly
- **Follow industry standards** that all PHP developers recognize  
- **Reduce cognitive load** for yourself and your teammates
- **Prevent naming-related bugs** and confusion
- **Build professional habits** from day one

This isn't just about syntax—it's about communicating clearly through code.

## 📚 PHP Variable Fundamentals

### Basic Syntax Rules
PHP variables have specific syntax requirements that enable the language's flexibility:

```php
<?php
// Valid variable names
$userName;          // Standard camelCase
$_privateData;      // Leading underscore allowed
$user1;            // Numbers allowed (not at start)
$_a1_valid;        // Complex but valid

// Invalid variable names
// $1invalid;       // Cannot start with number
// $user-name;      // Hyphens not allowed
// $user name;      // Spaces not allowed
?>
```

**Critical Note**: PHP is case-sensitive. `$numberOfStudents` and `$numberofstudents` are completely different variables.

## 🎨 Professional Naming Standards

### CamelCase Convention
PHP uses camelCase for variables and functions—first letter lowercase, subsequent words capitalized:

```php
<?php
// Professional variable naming
$interestRate = 0.05;
$numberOfStudents = 150;
$userAccountBalance = 2500.75;
$isEmailVerified = true;
$lastLoginTimestamp = time();

// Function naming follows same pattern
function calculateMonthlyPayment(float $principal, float $rate): float 
{
    return $principal * $rate / 12;
}

function validateEmailAddress(string $email): bool 
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>
```

### Function Naming Best Practices

> **What's a Function?** A function is like a recipe – it takes some ingredients (parameters) and produces a result. The name should tell you what the recipe makes!

**The Golden Rule**: A function's name should completely describe what it does. If the name gets too long, the function probably does too much.

**Simple Example - Good Function Names**:
```php
<?php
// Clear, descriptive names
function calculateTax(float $price): float
{
    return $price * 0.08;
}

function isValidAge(int $age): bool
{
    return $age >= 18;
}

function getUserName(int $userId): string
{
    // Code to get username from database
    return "John Doe";
}
?>
```

**Warning Signs - Function Doing Too Much**:
```php
<?php
// This name is too long and suggests the function does multiple things
function getSumOfAllEvenNumbersBetween(int $start, int $end): int 
{
    $sum = 0;
    for ($i = $start; $i <= $end; $i++) {
        if ($i % 2 == 0) {
            $sum += $i;
        }
    }
    return $sum;
}
?>
```

**Better Approach - Split the Work**:
```php
<?php
// Each function has one clear job
function getEvenNumbers(int $start, int $end): array 
{
    $evenNumbers = [];
    for ($i = $start; $i <= $end; $i++) {
        if ($i % 2 == 0) {
            $evenNumbers[] = $i;
        }
    }
    return $evenNumbers;
}

function sumNumbers(array $numbers): int 
{
    return array_sum($numbers);
}

// Now we can combine them easily
function calculateEvenSum(int $start, int $end): int 
{
    $evenNumbers = getEvenNumbers($start, $end);
    return sumNumbers($evenNumbers);
}
?>
```

### Descriptive vs Abbreviated Names

> **Rule of Thumb**: If someone else (or you in a week) can't immediately understand what a variable represents, make the name longer and clearer.

**When short names are OK** (meaning is obvious from context):
```php
<?php
// In simple loops, everyone understands these
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// Short but clear in context
$age = 25;
$name = "John";
$price = 9.99;
?>
```

**When you need longer names** (meaning isn't obvious):
```php
<?php
// Clear business intent - anyone can understand these
$monthlyInterestRate = $annualRate / 12;
$customerBirthDate = $user['birth_date'];
$orderTotalPrice = $basePrice + $tax + $shipping;

// Avoid confusing abbreviations
$userAuthenticationStatus = 'verified';  // Not $uAuthStat
$shoppingCartItems = [];                 // Not $scItems
?>
```

**Beginner Tip**: When in doubt, choose the longer, clearer name. You can always shorten it later if needed, but unclear code causes bugs!

## 🚀 Special Naming Patterns

### Boolean Variables (True/False Values)

> **What's a Boolean?** A variable that can only be `true` or `false`. Think of it like a yes/no question.

Booleans should clearly indicate their true/false nature:

```php
<?php
// Excellent boolean naming - you immediately know what true/false means
$isLoggedIn = true;           // Is the user logged in? Yes!
$hasPermission = false;       // Does user have permission? No!
$canVote = ($age >= 18);      // Can this person vote? Depends on age!
$isEmailValid = true;         // Is the email address valid? Yes!

// Poor boolean names - what does true mean?
$status = true;        // Status of what? What does true mean?
$flag = false;         // What flag? What is false?
$check = true;         // Check what? True means what?
?>
```

**Common Boolean Prefixes**:
- `is...` - "Is the user active?" → `$isActive`
- `has...` - "Has the user paid?" → `$hasPaid`  
- `can...` - "Can the user edit?" → `$canEdit`
- `should...` - "Should we send email?" → `$shouldSendEmail`

### Arrays and Collections

> **What's a Collection?** A variable that holds multiple items, like a shopping list or a list of student names.

Make it obvious what your collection contains:

```php
<?php
// Clear collection naming - you know what's inside
$studentNames = ["Alice", "Bob", "Charlie"];
$shoppingItems = ["apples", "bread", "milk"];
$testScores = [85, 92, 78, 96];
$userEmails = [];

// Functions that work with collections
function calculateAverage(array $numbers): float 
{
    return array_sum($numbers) / count($numbers);
}

function printNames(array $nameList): void 
{
    foreach ($nameList as $name) {
        echo "Hello, " . $name . "!\n";
    }
}
?>
```

**Naming Tips for Arrays**:
- Use plural names: `$users` not `$user` for a list
- Be specific: `$activeUsers` is better than `$users`
- Include the type: `$userNames` vs `$userIds` vs `$userEmails`

## 📝 Common Naming Mistakes to Avoid

### Don't Do This - Poor Names
```php
<?php
// ❌ Poor naming examples
$d = "2024-01-15";           // What does 'd' represent?
$temp = "John Doe";          // Temp what? Why temporary?
$flag = true;                // What flag? What does true mean?
$data = ["apple", "banana"];  // All variables contain data
$stuff = 25;                 // Stuff is meaningless
$thing = "Hello";            // Thing tells us nothing
?>
```

### Do This Instead - Clear Names
```php
<?php
// ✅ Professional alternatives
$orderDate = "2024-01-15";
$customerName = "John Doe";
$isOrderComplete = true;
$fruitList = ["apple", "banana"];
$studentAge = 25;
$welcomeMessage = "Hello";
?>
```

### Context Matters
```php
<?php
// In a small function, short names can be OK
function calculateTax(float $amount): float 
{
    $rate = 0.08;  // Obviously the tax rate in this context
    return $amount * $rate;
}

// In larger code, be more specific
$stateTaxRate = 0.08;
$federalTaxRate = 0.15;
$localTaxRate = 0.02;
$totalTaxRate = $stateTaxRate + $federalTaxRate + $localTaxRate;
?>
```

## 🚀 Success Indicators

You're doing great with PHP naming when you can:

- [ ] Choose variable names that explain what they contain
- [ ] Write function names that describe what they do
- [ ] Use camelCase consistently (firstName, not firstname or first_name)
- [ ] Name boolean variables so you know what true/false means
- [ ] Look at your code from yesterday and understand it immediately
- [ ] Avoid single letters except for simple loop counters

**Don't worry about perfection!** Good naming is a skill that improves with practice.

## 📚 Future Learning: Advanced Concepts

As you progress in PHP, you'll encounter these advanced naming concepts:

- **Classes and Objects**: Naming conventions for object-oriented programming
- **PSR Standards**: Industry-wide PHP naming and coding standards
- **Namespaces**: Organizing code in larger applications
- **Design Patterns**: Standard naming for common programming solutions

**For Now**: Focus on clear variable and function names. The advanced concepts will make more sense once you're comfortable with PHP basics!

## ➡️ Next Steps

With solid naming conventions established, you're ready for [Step 6: Your First PHP Program](README.md#💡-practical-exercise) where you'll apply these professional standards to build your first PHP application!

**Remember**: Good naming is an investment. The few extra seconds to choose a clear name save hours of confusion later. Your future self—and your teammates—will thank you!
