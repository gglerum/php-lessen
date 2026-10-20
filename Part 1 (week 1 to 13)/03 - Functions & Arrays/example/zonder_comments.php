<?php
const MAXIMUM_ATTEMPTS = 10;

function readIntegerFromConsole($stdin, $prompt)
{
    echo $prompt;
    return (int) trim(fgets($stdin));
}

function provideGuessHint($guess, $secretNumber)
{
    if ($guess < $secretNumber) {
        echo "Too low. Try again.\n";
        return;
    }
    echo "Too high. Try again.\n";
}

function gameLoop($stdin, &$attemptsUsed, $maximumAttempts, &$hasGuessedNumber, $secretNumber)
{
    while ($attemptsUsed < $maximumAttempts && !$hasGuessedNumber) {
        $attemptsUsed++;
        $guess = readIntegerFromConsole(
            $stdin,
            "Attempt $attemptsUsed of $maximumAttempts. Your guess: "
        );

        if ($guess === $secretNumber) {
            echo "Correct! You guessed the number.\n";
            $hasGuessedNumber = true;
            continue;
        }
        provideGuessHint($guess, $secretNumber);
    }
}

function playGuessingGame($stdin)
{
    echo "Welcome to the number guessing game!\n";

    $maximumNumber = readIntegerFromConsole($stdin, "Choose the maximum number (N): ");
    $secretNumber = rand(0, $maximumNumber);
    $maximumAttempts = MAXIMUM_ATTEMPTS;
    $attemptsUsed = 0;
    $hasGuessedNumber = false;

    echo "Guess a number between 0 and $maximumNumber.\n";

    gameLoop($stdin, $attemptsUsed, $maximumAttempts, $hasGuessedNumber, $secretNumber);

    if (!$hasGuessedNumber) {
        echo "No attempts left. The number was $secretNumber.\n";
    }
}

$stdin = fopen('php://stdin', 'r');
playGuessingGame($stdin);
fclose($stdin);
