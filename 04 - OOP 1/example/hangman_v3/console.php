<?php
/**
 * This script runs a Hangman game in the console.
 */

require_once './game/generator/RandomWordGenerator.php';
require_once './game/generator/Words.php';
require_once './game/GameService.php';
require_once './game/GameStatus.php';

$randomWordGenerator = new RandomWordGenerator(new Words());
$gameService = new GameService($randomWordGenerator);

$input = null;
while (!$gameService->isGameOver()) {
    echo PHP_EOL;
    if ($input) {
        if ($gameService->userInput($input)) {
            echo 'You guessed correctly!' . PHP_EOL;
        } else {
            echo 'Your guess was incorrect!' . PHP_EOL;
        }
    }

    echo $gameService->getDrawnHangman() . PHP_EOL;
    echo 'Attempts: ' . $gameService->getAttempts() . PHP_EOL;
    echo 'Word: ' . $gameService->getDisplayWord() . PHP_EOL;

    if (!$gameService->isGameOver()) {
        $input = readline('Enter a letter or a word: ');
    } else {
        echo 'You have ' . ($gameService->getStatus() == GameStatus::WON ? 'won' : 'lost') . '!' . PHP_EOL;
    }
}

echo 'Game over!' . PHP_EOL;
