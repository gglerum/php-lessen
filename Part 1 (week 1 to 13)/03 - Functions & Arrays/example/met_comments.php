<?php

$stdin = fopen('php://stdin', 'r');

echo "Welcome to the number guessing game!\n";

// Ask the player how high the number can be.
echo "Choose the maximum number (N): ";
$maximumNumber = (int) trim(fgets($stdin));
$secretNumber = rand(0, $maximumNumber);
$maximumAttempts = 10;
$attemptsUsed = 0;
$hasGuessedNumber = false;

echo "Guess a number between 0 and $maximumNumber.\n";

// Keep asking for guesses until the player wins or runs out of attempts.
while ($attemptsUsed < $maximumAttempts && !$hasGuessedNumber) {
    $attemptsUsed++;
    echo "Attempt $attemptsUsed of $maximumAttempts. Your guess: ";
    $guess = (int) trim(fgets($stdin));

    // Check whether the guess is correct and give the player a hint.
    if ($guess === $secretNumber) {
        echo "Correct! You guessed the number.\n";
        $hasGuessedNumber = true;
    } elseif ($guess < $secretNumber) {
        echo "Too low. Try again.\n";
    } else {
        echo "Too high. Try again.\n";
    }
}

// Reveal the number if the player used all attempts without winning.
if (!$hasGuessedNumber) {
    echo "No attempts left. The number was $secretNumber.\n";
}

fclose($stdin);
