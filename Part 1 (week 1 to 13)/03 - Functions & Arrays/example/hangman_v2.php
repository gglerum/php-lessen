<?php

/**
 * HANGMAN GAME V2 - FUNCTIONS & ARRAYS LEARNING EXAMPLE
 * 
 * ============================================================
 * 🎯 EDUCATIONAL PURPOSE: This example demonstrates how to organize code using FUNCTIONS and ARRAYS
 * ============================================================
 * 
 * 📚 KEY LEARNING CONCEPTS DEMONSTRATED:
 * 
 * 1. FUNCTIONS - Organizing code into reusable blocks
 *    ✓ Each function has ONE specific job (Single Responsibility Principle)
 *    ✓ Functions make code easier to read, test, and maintain
 *    ✓ Functions can take parameters and return values
 * 
 * 2. ARRAYS - Storing collections of related data
 *    ✓ Simple array: $words contains our word collection
 *    ✓ Array functions: count(), rand(), in_array()
 * 
 * 3. GLOBAL VARIABLES - Sharing data between functions
 *    ✓ Game state variables ($word, $guessedLetters, $attempts, $gameOver)
 *    ✓ Using 'global' keyword to access shared data
 * 
 * 4. ERROR HANDLING - Dealing with invalid input gracefully
 *    ✓ Try/catch blocks to handle exceptions
 *    ✓ Throwing exceptions for invalid input
 * 
 * 🔄 COMPARE WITH hangman_v1.php:
 * - V1: All code mixed together in one long script
 * - V2: Organized into logical functions with clear purposes
 * 
 * @file hangman_v2.php - Functions & Arrays demonstration
 * @version 2.0 - Educational Enhanced Version
 * @author Educational Example for PHP Course
 */

// ============================================================
// 🗃️ GLOBAL VARIABLES SECTION
// ============================================================
// These variables store the game's state and are shared between functions
// Using global variables is one way to share data between functions

$words = ["apple", "banana", "cherry", "date", "elderberry", "fig", "grape", "honeydew", "kiwi", "lemon", "mango", "nectarine", "orange", "pear", "quince", "raspberry", "strawberry", "tangerine", "ugli", "vanilla", "watermelon", "xigua", "yellow", "zucchini"];
// ^ ARRAY EXAMPLE: This array stores our collection of possible words

$word = "";              // String: The word the player needs to guess
$guessedLetters = [];    // Array: Stores all letters the player has guessed
$attempts = 7;           // Integer: How many wrong guesses the player can make
$gameOver = false;       // Boolean: Whether the game has ended

// ============================================================
// 🎮 GAME SETUP FUNCTIONS
// ============================================================

/**
 * 🎲 FUNCTION: getRandomWord()
 * 
 * PURPOSE: Selects a random word from our words array
 * 
 * 📚 LEARNING POINTS:
 * - Uses 'global' keyword to access the $words array
 * - Demonstrates array functions: count() and rand()
 * - Returns a value that other functions can use
 * 
 * 🔧 HOW IT WORKS:
 * 1. count($words) gets the total number of words in the array
 * 2. rand(0, count-1) generates a random number between 0 and array length
 * 3. Uses that random number as an index to pick a word
 * 
 * @return string The randomly selected word from the words array
 */
function getRandomWord()
{
    global $words;  // Access the global $words array

    // Generate random index between 0 and (array length - 1)
    $randomIndex = rand(0, count($words) - 1);

    // Return the word at that random position
    return $words[$randomIndex];
}

/**
 * 📺 FUNCTION: displayWord()
 * 
 * PURPOSE: Shows the word with guessed letters revealed and unknown letters as underscores
 * 
 * 📚 LEARNING POINTS:
 * - Uses a for loop to examine each character in the word
 * - Demonstrates string manipulation with strlen() and character access
 * - Uses in_array() function to check if a letter has been guessed
 * - Builds a string character by character using concatenation (.)
 * 
 * 🔧 HOW IT WORKS:
 * 1. Loop through each character position in the word
 * 2. Check if that character has been guessed (is in $guessedLetters array)
 * 3. If guessed: add the actual letter to display
 * 4. If not guessed: add an underscore to display
 * 5. Return the complete display string
 * 
 * EXAMPLE: If word is "apple" and guessed letters are ["a", "p"]
 * Result would be: "a pp_ _"
 *
 * @return string The word with guessed letters shown and unknown letters as underscores
 */
function displayWord()
{
    global $word, $guessedLetters;  // Access global game state variables

    $display = "";  // Start with empty string

    // Loop through each character position in the word
    for ($i = 0; $i < strlen($word); $i++) {
        $currentLetter = $word[$i];  // Get the letter at position $i

        // Check if this letter has been guessed
        if (in_array($currentLetter, $guessedLetters)) {
            $display .= $currentLetter;  // Show the actual letter
        } else {
            $display .= "_";  // Show underscore for unknown letters
        }
    }

    return $display;
}

// ============================================================
// 🎯 USER INPUT HANDLING FUNCTIONS
// ============================================================

/**
 * 🔤 FUNCTION: handleLetter($input)
 * 
 * PURPOSE: Processes when the user guesses a single letter
 * 
 * 📚 LEARNING POINTS:
 * - Function parameters: takes $input as a parameter
 * - Input validation using ctype_alpha() function
 * - Exception throwing for error handling
 * - Uses strpos() to check if letter is in the word
 * - Calls other functions to handle mistakes and check win condition
 * 
 * 🔧 HOW IT WORKS:
 * 1. Validate input (not empty and is a letter)
 * 2. Add the letter to guessed letters array
 * 3. Check if letter is in the word using strpos()
 * 4. If not in word: call handleMistake()
 * 5. Check if word is completely guessed (win condition)
 * 
 * 🚨 ERROR HANDLING EXAMPLE:
 * - Throws exception for invalid input (numbers, empty, symbols)
 * - This exception is caught in askForInput() function
 *
 * @param string $input The letter the user guessed
 * @return void This function doesn't return a value, it modifies global state
 * @throws InvalidArgumentException When input is empty or not a letter
 */
function handleLetter($input)
{
    global $word, $guessedLetters, $gameOver;

    // INPUT VALIDATION: Check if input is valid
    if ($input == "" || !ctype_alpha($input)) {
        // Throw an exception - this will be caught by the try/catch block
        throw new InvalidArgumentException("Invalid input. Please enter a valid letter.");
    }

    // Add the guessed letter to our array of guessed letters
    $guessedLetters[] = $input;

    // Check if the letter is NOT in the word
    if (strpos($word, $input) === false) {
        echo "Nope! $input is not in the word.\n";
        handleMistake();  // Call function to handle wrong guess
    }

    // Check if player has guessed the complete word
    if (displayWord() == $word) {
        echo "Congratulations! You guessed the word!\n";
        $gameOver = true;  // End the game
    }
}

/**
 * 📝 FUNCTION: handleWord($input)
 * 
 * PURPOSE: Processes when the user guesses the entire word
 * 
 * 📚 LEARNING POINTS:
 * - Simple string comparison using == operator
 * - Conditional logic (if/else) to handle success vs failure
 * - Function calls to handle different outcomes
 * 
 * 🔧 HOW IT WORKS:
 * 1. Compare user's guess with the actual word
 * 2. If correct: congratulate and end game
 * 3. If wrong: inform user and call handleMistake()
 *
 * @param string $input The complete word the user guessed
 * @return void This function doesn't return a value, it modifies global state
 */
function handleWord($input)
{
    global $word, $gameOver;

    // Check if the guessed word matches the actual word
    if ($input == $word) {
        echo "Congratulations! You guessed the word!\n";
        $gameOver = true;  // Player wins - end the game
    } else {
        echo "Nope! $input is not the word.\n\n";
        handleMistake();  // Wrong guess - handle as mistake
    }
}

// ============================================================
// ⚠️ GAME STATE MANAGEMENT FUNCTIONS
// ============================================================

/**
 * 💥 FUNCTION: handleMistake()
 * 
 * PURPOSE: Handles what happens when the player makes a wrong guess
 * 
 * 📚 LEARNING POINTS:
 * - Modifies global variables to update game state
 * - Calls other functions to handle related tasks
 * - Demonstrates function composition (functions calling other functions)
 * 
 * 🔧 HOW IT WORKS:
 * 1. Decrease the number of attempts remaining
 * 2. Draw the hangman (visual feedback)
 * 3. Tell player how many attempts they have left
 * 
 * This function is called by both handleLetter() and handleWord()
 * when the player makes an incorrect guess.
 *
 * @return void This function doesn't return a value, it modifies global state
 */
function handleMistake()
{
    global $attempts;

    $attempts--;  // Reduce attempts by 1
    drawHangman();  // Show updated hangman drawing
    echo "You have $attempts attempts left.\n\n";
}

/**
 * 🎨 FUNCTION: drawHangman()
 * 
 * PURPOSE: Displays the hangman drawing based on remaining attempts
 * 
 * 📚 LEARNING POINTS:
 * - Switch statement for handling multiple conditions
 * - Using global variables to access game state
 * - ASCII art creation using echo statements
 * - Progressive visual feedback based on game state
 * 
 * 🔧 HOW IT WORKS:
 * - Each case in the switch represents a different stage of the hangman
 * - As attempts decrease, more body parts are added to the drawing
 * - case 7: Empty gallows
 * - case 6: Gallows with rope
 * - case 5: Head added
 * - case 4: Body added
 * - case 3: Left arm added
 * - case 2: Right arm added  
 * - case 1: Left leg added
 * - case 0: Right leg added (game over)
 * 
 * NOTE: This function could be improved by using constants for repeated strings,
 * but for learning purposes, it shows the basic concept clearly.
 *
 * @return void This function only displays output, doesn't return anything
 */
function drawHangman()
{
    global $attempts;  // Access the attempts counter

    // Use switch statement to show different hangman stages
    switch ($attempts) {
        case 6:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 5:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 4:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo "  |   |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 3:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo " /|   |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 2:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo " /|\  |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 1:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo " /|\  |\n";
            echo " /    |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        case 0:
            echo "  +---+\n";
            echo "  |   |\n";
            echo "  O   |\n";
            echo " /|\  |\n";
            echo " / \  |\n";
            echo "      |\n";
            echo "=========\n";
            break;
        default:
            echo "  +---+\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "      |\n";
            echo "=========\n";
            break;
    }
}

// ============================================================
// 💬 USER INTERACTION FUNCTIONS
// ============================================================

/**
 * 🎤 FUNCTION: askForInput()
 * 
 * PURPOSE: Gets input from the user and routes it to appropriate handler
 * 
 * 📚 LEARNING POINTS:
 * - Uses readline() to get user input from command line
 * - Demonstrates try/catch exception handling
 * - Shows how to route different types of input to different functions
 * - String length checking with strlen()
 * 
 * 🔧 HOW IT WORKS:
 * 1. Ask user for input using readline()
 * 2. Put the input handling in a try block
 * 3. If input is 1 character: treat as letter guess
 * 4. If input is longer: treat as word guess
 * 5. Catch any InvalidArgumentException and show friendly error
 * 
 * 🚨 EXCEPTION HANDLING EXAMPLE:
 * - If handleLetter() throws an exception, we catch it here
 * - This prevents the program from crashing on invalid input
 * - Shows user a friendly error message instead
 *
 * @return void This function handles user interaction, doesn't return anything
 */
function askForInput()
{
    $input = readline("Guess a letter or a word: ");

    // Try to process the input, catch any validation errors
    try {
        // Route input based on length
        if (strlen($input) == 1) {
            handleLetter($input);  // Single character = letter guess
        } else {
            handleWord($input);    // Multiple characters = word guess
        }

        // Catch validation errors thrown by handleLetter()
    } catch (InvalidArgumentException) {
        echo "Your input was incorrect. Please try again.\n";
        // Note: We catch the exception but don't assign it to a variable
        // In real applications, you might want to log the error details
    }
}

// ============================================================
// 🎮 GAME STATE CHECKING FUNCTIONS
// ============================================================

/**
 * 🏁 FUNCTION: checkIfGameOver()
 * 
 * PURPOSE: Checks if the player has run out of attempts (game lost)
 * 
 * 📚 LEARNING POINTS:
 * - Simple conditional logic to check game state
 * - Accessing multiple global variables
 * - Setting game state variables to control program flow
 * 
 * 🔧 HOW IT WORKS:
 * 1. Check if attempts counter has reached 0
 * 2. If so: reveal the correct word and end game
 * 3. Set $gameOver flag to stop the main game loop
 * 
 * This function is called after each guess to check for game over condition.
 *
 * @return void This function only checks and modifies game state
 */
function checkIfGameOver()
{
    global $word, $gameOver, $attempts;

    // Check if player has run out of attempts
    if ($attempts == 0) {
        echo "Sorry, you're out of guesses! The word was $word.\n";
        $gameOver = true;  // End the game
    }
}

// ============================================================
// 🚀 MAIN GAME INITIALIZATION & CONTROL
// ============================================================

/**
 * 🎯 FUNCTION: init()
 * 
 * PURPOSE: Initializes and runs the complete hangman game
 * 
 * 📚 LEARNING POINTS:
 * - Main function that controls the entire program flow
 * - While loop for game continuation
 * - Function calls to organize game logic
 * - Global variable initialization
 * 
 * 🔧 HOW IT WORKS:
 * 1. Initialize the game by selecting a random word
 * 2. Display welcome message
 * 3. Enter main game loop (while not game over):
 *    - Show current word state
 *    - Ask for user input
 *    - Check if game should end
 * 4. Display final message when loop ends
 * 
 * This function demonstrates how to structure a complete program
 * using functions to organize different responsibilities.
 *
 * @return void This is the main program function
 */
function init()
{
    global $word, $gameOver;

    // Initialize game state
    $word = getRandomWord();

    echo "Welcome to hangman ♥!\n";

    // Main game loop - continues until $gameOver becomes true
    while (!$gameOver) {
        echo displayWord() . "\n\n";  // Show current progress
        askForInput();                // Get and process user input
        checkIfGameOver();            // Check if game should end
    }

    echo "Game Over";
}

// ============================================================
// 🎬 START THE GAME
// ============================================================

// This single line starts the entire game!
// Everything happens through function calls from here.

init();
