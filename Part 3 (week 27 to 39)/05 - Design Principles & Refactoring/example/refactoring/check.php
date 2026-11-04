<?php

declare(strict_types=1);

// dit script is de "test" van de refactoring: na elke stap moet de uitvoer gelijk zijn aan die van het begin
// uitvoeren: php check.php (met -d xdebug.mode=off onderdrukken we de Xdebug melding van de scripts)
$steps = [
    'lend_1_demeter.php',
    'lend_2_tell_dont_ask.php',
    'lend_3_separation.php',
    'lend_4_dry.php',
];

// we leggen vast wat het beginpunt als uitvoer geeft
$expected = shell_exec(escapeshellarg(PHP_BINARY) . ' -d xdebug.mode=off ' . escapeshellarg(__DIR__ . '/lend_0_start.php'));

foreach ($steps as $step) {
    $actual = shell_exec(escapeshellarg(PHP_BINARY) . ' -d xdebug.mode=off ' . escapeshellarg(__DIR__ . '/' . $step));

    echo ($actual === $expected ? 'ok   ' : 'FOUT ') . $step . "\n";
}
