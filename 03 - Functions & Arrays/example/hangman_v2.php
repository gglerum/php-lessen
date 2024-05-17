<?php

/**
 * Hangman game
 *
 * This script allows the user to play a game of Hangman in the command line.
 * The script randomly selects a word from an array of words and prompts the user to guess letters or the entire word.
 * The user has a limited number of attempts to guess the word before losing the game.
 * The script displays the hangman drawing and the partially guessed word after each guess.
 * If the user guesses the word correctly within the given attempts, they win the game.
 * If the user runs out of attempts without guessing the word, they lose the game.
 *
 * @file FILEPATH: /c:/Users/Glenn/Documents/PHP/commandline/index.php
 * @package Hangman
 * @version 1.0
 * @author GLenn Glerum
 */
$words = array("apple", "banana", "cherry", "date", "elderberry", "fig", "grape", "honeydew", "kiwi", "lemon", "mango", "nectarine", "orange", "pear", "quince", "raspberry", "strawberry", "tangerine", "ugli", "vanilla", "watermelon", "xigua", "yellow", "zucchini");
$word = "";
$guessedLetters = [];
$attempts = 7;
$gameOver = false;

/**
 * Retrieves a random word from the global $words array.
 *
 * @return string The randomly selected word.
 */
function getRandomWord()
{
    global $words;
    return $words[rand(0, count($words) - 1)];
}

/**
 * Displays the word with guessed letters filled in and unknown letters as underscores.
 *
 * @return string The word with guessed letters filled in and unknown letters as underscores.
 */
function displayWord()
{
    global $word, $guessedLetters;
    $display = "";
    for ($i = 0; $i < strlen($word); $i++) {
        if (in_array($word[$i], $guessedLetters)) {
            $display .= $word[$i];
        } else {
            $display .= "_";
        }
    }
    return $display;
}

/**
 * Handles a letter input in the word guessing game.
 *
 * @param string $input The letter input to handle.
 * @return void
 */
function handleLetter($input)
{
    global $word, $guessedLetters, $gameOver;
    $guessedLetters[] = $input;
    if (strpos($word, $input) === false) {
        echo "Nope! $input is not in the word.\n";
        handleMistake();
    }
    if (displayWord() == $word) {
        echo "Congratulations! You guessed the word!\n";
        $gameOver = true;
    }
}

/**
 * Handles the user input for guessing a word.
 *
 * @param string $input The user's input for guessing the word.
 * @return void
 */
function handleWord($input)
{
    global $word, $gameOver;
    if ($input == $word) {
        echo "Congratulations! You guessed the word!\n";
        $gameOver = true;
    } else {
        echo "Nope! $input is not the word.\n\n";
        handleMistake();
    }
}

/**
 * Decreases the number of attempts and displays the hangman drawing.
 *
 * @return void
 */
function handleMistake()
{
    global $attempts;
    $attempts--;
    drawHangman();
    echo "You have $attempts attempts left.\n\n";
}

/**
 * Draws the hangman based on the number of attempts remaining.
 *
 * @return void
 */
function drawHangman()
{
    global $attempts;
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

/**
 * Prompts the user to guess a letter or a word and handles the input accordingly.
 *
 * @return void
 */
function askForInput()
{
    $input = readline("Guess a letter or a word: ");
    if (strlen($input) == 1) {
        handleLetter($input);
    } else {
        handleWord($input);
    }
}

/**
 * Checks if the game is over by checking the number of attempts remaining.
 * If there are no attempts left, it displays a message with the correct word and sets the game over flag to true.
 *
 * @return void
 */
function checkIfGameOver()
{
    global $word, $gameOver, $attempts;
    if ($attempts == 0) {
        echo "Sorry, you're out of guesses! The word was $word.\n";
        $gameOver = true;
    }
}

/**
 * Initializes the hangman game.
 *
 * This function sets up the necessary variables and starts the game loop.
 * It generates a random word, displays the initial message, and continues
 * to ask for user input until the game is over.
 *
 * @return void
 */
function init()
{
    global $word, $gameOver;
    $word = getRandomWord();

    echo "Welcome to hangman!\n";

    while (!$gameOver) {
        echo displayWord() . "\n\n";
        askForInput();
        checkIfGameOver();
    }

    echo "Game Over";
}

init();
