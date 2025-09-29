<?php

/**
 * Hangman Game - PHP Fundamentals Demonstration
 *
 * This console game demonstrates all the core PHP concepts covered in Module 02:
 * - Variables and data types (strings, integers, booleans, arrays)
 * - Control structures (if/else, switch, while, for loops)
 * - String manipulation and array functions
 * - User input handling and validation
 * - Professional code organization and commenting
 *
 * Study this code to see how fundamental concepts combine to create
 * an engaging, interactive console application.
 *
 * @author Glenn Glerum
 * @version 1.0 - Educational demonstration for PHP Basics module
 */

/* LESSON CONCEPT: Arrays - Collections of related data */
// Array of words for the game (demonstrates indexed array with string values)
$words = ["apple", "banana", "cherry", "date", "elderberry", "fig", "grape", "honeydew", "kiwi", "lemon", "mango", "nectarine", "orange", "pear", "quince", "raspberry", "strawberry", "tangerine", "ugli", "vanilla", "watermelon", "xigua", "yellow", "zucchini"];

/* LESSON CONCEPT: Arrays - Dynamic collections */
// Array to store the guessed letters (starts empty, grows during gameplay)
$guessedLetters = [];

/* LESSON CONCEPT: Variables - Integer data type */
// Number of attempts remaining (demonstrates numeric variable with decremental logic)
$attempts = 7;

/* LESSON CONCEPT: Variables - Boolean data type */
// Flag to control the main game loop (demonstrates boolean logic)
$gameOver = false;

/* LESSON CONCEPT: Functions and random numbers */
// Randomly select a word from the array (demonstrates array access and random generation)
$word = $words[rand(0, count($words) - 1)];

/* LESSON CONCEPT: Output - Displaying information to user */
// Display welcome message (demonstrates string output)
echo "Welcome to hangman!\n";

/* LESSON CONCEPT: Loops - While loop for game flow */
// Main game loop - continues until game ends (demonstrates while loop with boolean condition)
while (!$gameOver) {
    /* LESSON CONCEPT: String manipulation and building */
    // Create the display string by checking each letter against guessed letters
    $display = "";

    /* LESSON CONCEPT: For loop - Processing each character */
    // Loop through each letter in the word to build display
    for ($i = 0; $i < strlen($word); $i++) {
        /* LESSON CONCEPT: Conditional logic and array functions */
        // Check if this letter has been guessed (demonstrates in_array function)
        if (in_array($word[$i], $guessedLetters)) {
            $display .= $word[$i];  // Show the actual letter
        } else {
            $display .= "_";        // Show underscore for unguessed letters
        }
    }

    /* LESSON CONCEPT: Output formatting */
    // Display current progress to user
    echo $display . "\n\n";

    /* LESSON CONCEPT: User input handling */
    // Get user's guess (demonstrates readline function for console input)
    $input = readline("Guess a letter or a word: ");

    /* LESSON CONCEPT: Input validation and conditional logic */
    // Process the user's guess differently based on input length
    if (strlen($input) == 1) {
        /* LESSON CONCEPT: Array manipulation */
        // Single letter guess - add to guessed letters array
        $guessedLetters[] = $input;

        /* LESSON CONCEPT: String functions and strict comparison */
        // Check if letter exists in word (strpos returns false if not found)
        if (strpos($word, $input) === false) {
            echo "Nope! $input is not in the word.\n";
            $attempts--;  // Decrease attempts for wrong guess
        }

        /* LESSON CONCEPT: Code reuse and string building */
        // Rebuild display string to show any newly revealed letters
        $display = "";
        for ($i = 0; $i < strlen($word); $i++) {
            if (in_array($word[$i], $guessedLetters)) {
                $display .= $word[$i];
            } else {
                $display .= "_";
            }
        }

        /* LESSON CONCEPT: Win condition checking */
        // Check if the word has been fully guessed (no underscores left)
        if ($display == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;  // End the game loop
        }
    } else {
        /* LESSON CONCEPT: Alternative conditional path */
        // Full word guess - check for exact match
        if ($input == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;
        } else {
            echo "Nope! $input is not the word.\n\n";
            $attempts--;  // Wrong word guess costs an attempt
        }
    }

    /* LESSON CONCEPT: Game continuation logic */
    // Only continue if game hasn't ended (demonstrates nested conditionals)
    if (!$gameOver) {
        /* LESSON CONCEPT: Switch statement for multiple conditions */
        // Display progressive hangman drawing based on remaining attempts
        // This demonstrates switch statement with multiple cases
        switch ($attempts) {
            default:  // 6 or 7 attempts left - just the gallows
                echo "  +---+\n";
                echo "  |   |\n";
                echo "      |\n";
                echo "      |\n";
                echo "      |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 5:  // 5 attempts left - add head
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo "      |\n";
                echo "      |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 4:  // 4 attempts left - add body
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo "  |   |\n";
                echo "      |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 3:  // 3 attempts left - add left arm
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo " /|   |\n";
                echo "      |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 2:  // 2 attempts left - add right arm
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo " /|\  |\n";
                echo "      |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 1:  // 1 attempt left - add left leg
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo " /|\  |\n";
                echo " /    |\n";
                echo "      |\n";
                echo "=========\n";
                break;
            case 0:  // Game over - complete hangman
                echo "  +---+\n";
                echo "  |   |\n";
                echo "  O   |\n";
                echo " /|\  |\n";
                echo " / \  |\n";
                echo "      |\n";
                echo "=========\n";
                /* LESSON CONCEPT: String interpolation */
                // Display the correct word (demonstrates variable in string)
                echo "Sorry, you're out of guesses! The word was $word.\n";
                $gameOver = true;  // End game when attempts reach zero
                break;
        }

        /* LESSON CONCEPT: User feedback and output formatting */
        // Show remaining attempts (demonstrates string interpolation)
        echo "You have $attempts attempts left.\n\n";
    }
}  // End of main game loop

/* LESSON CONCEPT: Program conclusion */
// Display final message when game loop ends
echo "Game Over\n\n";

/*
 * EDUCATIONAL SUMMARY:
 * This game demonstrates all core PHP concepts from Module 02:
 * 
 * 1. VARIABLES: String ($word), integer ($attempts), boolean ($gameOver), arrays
 * 2. DATA TYPES: Mixing strings, numbers, and booleans in one program
 * 3. OPERATORS: Comparison (==, ===), assignment (=), increment/decrement
 * 4. CONDITIONALS: if/else for game logic, nested conditions for complexity
 * 5. LOOPS: while for game flow, for for character processing
 * 6. SWITCH: Multiple visual states based on single variable
 * 7. ARRAYS: Static word list, dynamic guessed letters collection
 * 8. FUNCTIONS: rand(), count(), strlen(), strpos(), in_array(), readline()
 * 9. STRING MANIPULATION: Building display string, checking characters
 * 10. USER INTERACTION: Console input/output for engaging experience
 * 
 * Study this code to see how these concepts work together!
 */
