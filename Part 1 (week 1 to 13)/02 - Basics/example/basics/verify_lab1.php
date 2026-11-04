<?php

// Controleert de uitvoer in lab/loops_and_conditions.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// De uitvoerregels van het lab worden aan elkaar geplakt met een |, in plaats van een regeleinde.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// scenario 1
$word = "kiwi";
$attempts = 7;
$guessedLetters = [];
$guessedLetters[] = "i";
$attempts--;
$display = "";
$display .= "_";
check('1', "Nog " . $attempts . " pogingen", 'Nog 6 pogingen');
check('1 array', implode(',', $guessedLetters), 'i');
check('1 display', $display, '_');

// scenario 2
$word = "kiwi";
$guessedLetters = ["i"];
$display = "";
for ($i = 0; $i < strlen($word); $i++) {
    if (in_array($word[$i], $guessedLetters)) {
        $display .= $word[$i];
    } else {
        $display .= "_";
    }
}
check('2', $display, '_i_i');

// scenario 3
$attempts = 3;
$gameOver = false;
$rounds = 0;
while (!$gameOver) {
    $attempts--;
    $rounds++;
    if ($attempts == 0) {
        $gameOver = true;
    }
}
check('3', "Game Over na $rounds rondes", 'Game Over na 3 rondes');

// scenario 4 en 5
foreach (['4' => ['z', 'Een letter|6'], '5' => ['kiwi', 'Het hele woord|7']] as $name => [$input, $expected]) {
    ob_start();
    $word = "kiwi";
    $attempts = 7;
    if (strlen($input) == 1) {
        echo "Een letter|";
        if (strpos($word, $input) === false) {
            $attempts--;
        }
    } else {
        echo "Het hele woord|";
    }
    echo $attempts;
    check((string) $name, ob_get_clean(), $expected);
}

// scenario 6
ob_start();
$attempts = 3;
switch ($attempts) {
    case 5:
        echo "hoofd";
        break;
    case 4:
        echo "hoofd en romp";
        break;
    case 3:
        echo "hoofd, romp en arm";
        break;
    default:
        echo "nog niets";
}
echo "|Volgende ronde";
check('6', ob_get_clean(), 'hoofd, romp en arm|Volgende ronde');
