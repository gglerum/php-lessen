<?php

/**
 * HANGMAN GAME V3 - OBJECT-ORIENTED PROGRAMMING EXAMPLE
 * 
 * This is the same hangman game from previous modules, but now completely
 * reorganized using Object-Oriented Programming principles. Compare this
 * to hangman_v2.php to see how OOP transforms chaotic function-based code
 * into clean, organized classes.
 * 
 * 🎯 LEARNING OBJECTIVES:
 * - See how OOP organizes code into logical, reusable components
 * - Understand dependency injection and loose coupling
 * - Learn file organization and include/require usage
 * - Observe single responsibility principle in action
 * 
 * 🏗️ ARCHITECTURE OVERVIEW:
 * This file is now ONLY responsible for:
 * - Including necessary class files
 * - Setting up initial objects (dependency injection)
 * - Running the main game loop
 * - Handling user input/output
 * 
 * All game logic has been moved to specialized classes!
 */

// 📁 FILE ORGANIZATION & INCLUDES
// In OOP, each class gets its own file for better organization.
// We use require_once to include all the classes we need.
// This prevents duplicate inclusions and keeps our code organized.

require_once './game/generator/RandomWordGenerator.php';  // Provides random words for the game
require_once './game/generator/Words.php';                // Contains the word list
require_once './game/GameService.php';                    // Main game logic coordinator
require_once './game/GameStatus.php';                     // Game state management

// 🔧 DEPENDENCY INJECTION EXAMPLE
// Instead of creating objects inside other classes (tight coupling),
// we create them here and pass them where needed (loose coupling).
// This makes our code more flexible and testable.

// Step 1: Create the word provider
$words = new Words();

// Step 2: Create the word generator and inject the word provider
$randomWordGenerator = new RandomWordGenerator($words);

// Step 3: Create the main game service and inject the word generator
$gameService = new GameService($randomWordGenerator);

// 🎮 MAIN GAME LOOP
// Notice how simple this loop is now! All the complex logic
// has been moved to appropriate classes with clear responsibilities.

$input = null; // Initialize input variable

// Keep the game running until it's over
while (!$gameService->isGameOver()) {
    echo PHP_EOL; // Empty line for better readability

    // Process user input if we have any
    if ($input) {
        if ($gameService->userInput($input)) {
            echo '🎉 You guessed correctly!' . PHP_EOL;
        } else {
            echo '❌ Your guess was incorrect!' . PHP_EOL;
        }
    }

    // Display current game state
    // Notice: We don't manage the hangman drawing or game state here!
    // We just ask the GameService for the current information.
    echo $gameService->getDrawnHangman() . PHP_EOL;
    echo 'Attempts left: ' . $gameService->getAttempts() . PHP_EOL;
    echo 'Word: ' . $gameService->getDisplayWord() . PHP_EOL;

    // Get user input if game is still active
    if (!$gameService->isGameOver()) {
        // Simple user input - no complex validation needed here!
        // The GameService handles all the validation and processing.
        $input = readline('Enter a letter or a word: ');
    } else {
        // Game is over - show final result
        // Notice: We don't determine win/loss here, we ask the GameService
        if ($gameService->getStatus() == GameStatus::WON) {
            echo '🎉 Congratulations! You won!' . PHP_EOL;
        } else {
            echo '💀 Game over! You lost!' . PHP_EOL;
        }
    }
}

echo '👋 Thanks for playing!' . PHP_EOL;

/**
 * 🎓 WHAT YOU LEARNED FROM THIS EXAMPLE:
 * 
 * 1. **Separation of Concerns**: This file only handles user interface.
 *    All game logic is in other classes.
 * 
 * 2. **Dependency Injection**: Objects are created here and passed to
 *    classes that need them, making the code flexible and testable.
 * 
 * 3. **File Organization**: Each class is in its own file, making the
 *    codebase easy to navigate and maintain.
 * 
 * 4. **Single Responsibility**: This file has ONE job - run the game loop
 *    and handle user input/output. Nothing else.
 * 
 * 5. **Professional Structure**: Compare this to hangman_v2.php to see
 *    how OOP transforms chaotic code into organized, maintainable software.
 * 
 * 💡 NEXT STEPS:
 * - Examine GameService.php to see how game logic is organized
 * - Look at Word.php to understand how data and behavior are combined
 * - Study the Repository pattern in your library system assignment
 */
