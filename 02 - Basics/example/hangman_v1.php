<?php

/**
 * Hangman Game
 *
 * This script allows the user to play a game of Hangman.
 * The script randomly selects a word from an array of words,
 * and the user has to guess the letters or the entire word.
 * The user has a limited number of attempts to guess the word
 * before the game is over.
 *
 * @author Glenn Glerum
 * @version 1.0
 *
 * @see https://github.com/example/hangman_v1.php
 */

// FILEPATH: /c:/Users/ggler/Documents/PHP/php-lessen/02 - Basics/example/hangman_v1.php

// Array of words for the game
$words = ["apple", "banana", "cherry", "date", "elderberry", "fig", "grape", "honeydew", "kiwi", "lemon", "mango", "nectarine", "orange", "pear", "quince", "raspberry", "strawberry", "tangerine", "ugli", "vanilla", "watermelon", "xigua", "yellow", "zucchini"];

// Array to store the guessed letters
$guessedLetters = [];

// Number of attempts remaining
$attempts = 7;

// Flag to indicate if the game is over
$gameOver = false;

// Randomly select a word from the array
$word = $words[rand(0, count($words) - 1)];

// Display welcome message
echo "Welcome to hangman!\n";

// Main game loop
while (!$gameOver) {
    /* Display the guessed letters */
    $display = "";
    //loop for each letter in the word
    for ($i = 0; $i < strlen($word); $i++) {
        //check if the letter has been guessed so we can display it, else we display a _
        if (in_array($word[$i], $guessedLetters)) {
            $display .= $word[$i];
        } else {
            $display .= "_";
        }
    }

    echo $display . "\n\n";

    // Prompt the user for a guess
    $input = readline("Guess a letter or a word: ");

    // Process the user's guess
    if (strlen($input) == 1) {
        // If the input is a single letter
        $guessedLetters[] = $input;
        if (strpos($word, $input) === false) {
            echo "Nope! $input is not in the word.\n";
            $attempts--;
        }

        // Update the display
        $display = "";
        for ($i = 0; $i < strlen($word); $i++) {
            if (in_array($word[$i], $guessedLetters)) {
                $display .= $word[$i];
            } else {
                $display .= "_";
            }
        }

        // Check if the word has been fully guessed
        if ($display == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;
        }
    } else {
        // If the input is the entire word
        if ($input == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;
        } else {
            echo "Nope! $input is not the word.\n\n";
            $attempts--;
        }
    }

    // Check if the game is over
    if (!$gameOver) {
        // Display the hangman based on the number of attempts remaining
        switch ($attempts) {
            default:
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
                echo "Sorry, you're out of guesses! The word was $word.\n";
                $gameOver = true;
                break;
        }

        echo "You have $attempts attempts left.\n\n";
    }
}

// Display game over message
echo "Game Over\n\n";
