<?php
$words = array("apple", "banana", "cherry", "date", "elderberry", "fig", "grape", "honeydew", "kiwi", "lemon", "mango", "nectarine", "orange", "pear", "quince", "raspberry", "strawberry", "tangerine", "ugli", "vanilla", "watermelon", "xigua", "yellow", "zucchini");
$word = "";
$guessedLetters = [];
$attempts = 7;
$gameOver = false;

$word = $words[rand(0, count($words) - 1)];

echo "Welcome to hangman!\n";

while (!$gameOver) {
    $display = "";
    for ($i = 0; $i < strlen($word); $i++) {
        if (in_array($word[$i], $guessedLetters)) {
            $display .= $word[$i];
        } else {
            $display .= "_";
        }
    }

    echo $display . "\n\n";

    echo "Guess a letter or a word: ";
    $input = trim(fgets(STDIN));

    if (strlen($input) == 1) {
        $guessedLetters[] = $input;
        if (strpos($word, $input) === false) {
            echo "Nope! $input is not in the word.\n";
            $attempts--;
        }

        $display = "";
        for ($i = 0; $i < strlen($word); $i++) {
            if (in_array($word[$i], $guessedLetters)) {
                $display .= $word[$i];
            } else {
                $display .= "_";
            }
        }

        if ($display == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;
        }
    } else {
        if ($input == $word) {
            echo "Congratulations! You guessed the word!\n";
            $gameOver = true;
        } else {
            echo "Nope! $input is not the word.\n\n";
            $attempts--;
        }
    }
    
    if(!$gameOver){
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
                echo "Sorry, you're out of guesses! The word was $word.\n";
                $gameOver = true;
                break;
        }

        echo "You have $attempts attempts left.\n\n";
    }
}

echo "Game Over\n\n";