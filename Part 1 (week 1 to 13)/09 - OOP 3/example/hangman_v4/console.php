<?php

namespace Hacklabfrl\Hangman;

use Hacklabfrl\Hangman\Generator\RandomWordGenerator;
use Hacklabfrl\Hangman\Generator\DbWords;

require 'vendor/autoload.php';

/**
 * This script runs a Hangman game in the console.
 */
$randomWordGenerator = new RandomWordGenerator(new DbWords());
$gameService = new GameService($randomWordGenerator);

$input = null;
//keep running as long as the game is not over
while (!$gameService->isGameOver()) {
    echo PHP_EOL;
    //we ask for input at the end of the loop and process it at the begin of the loop
    //the reason for this is that we want to show the "interface" before asking for input
    if ($input) {
        if ($gameService->userInput($input)) {
            echo 'You guessed correctly!' . PHP_EOL;
        } else {
            echo 'Your guess was incorrect!' . PHP_EOL;
        }
    }
    //show the interface: the drawn hangman, the attempts and the word with the guessed letters
    echo $gameService->getDrawnHangman() . PHP_EOL;
    echo 'Attempts left: ' . $gameService->getAttempts() . PHP_EOL;
    echo 'Word: ' . $gameService->getDisplayWord() . PHP_EOL;

    //if the game is not over, ask for input else show the result
    if (!$gameService->isGameOver()) {
        $input = readline('Enter a letter or a word: ');
    } else {
        echo 'You have ' . ($gameService->getStatus() == GameStatus::WON ? 'won' : 'lost') . '!' . PHP_EOL;
    }
}

echo 'Game over!' . PHP_EOL;
