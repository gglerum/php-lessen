<?php

// Controleert de uitvoer in lab/bughunt.html, voor de versie met de fout en de gecorrigeerde versie.
// Run met: php verify_lab3.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// Een PHP waarschuwing wordt opgevangen, zoals het lab die in de uitvoer toont.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

$warnings = [];
set_error_handler(function (int $level, string $message) use (&$warnings): bool {
    $warnings[] = 'Warning: ' . $message;
    return true;
});

// scenario 1
$word = "kiwi";
$display = "";
for ($i = 0; $i < strlen($word); $i++) {
    if (in_array($word[$i], ["i"])) {
        $display .= $word[$i];
    } else {
        $display .= "_";
    }
}
check('1 gecorrigeerd', $display, '_i_i');

// scenario 1 met de fout: <= en de waarschuwing
$warnings = [];
$word = "kiwi";
$display = "";
for ($i = 0; $i <= strlen($word); $i++) {
    if (in_array($word[$i], ["i"])) {
        $display .= $word[$i];
    } else {
        $display .= "_";
    }
}
check('1 met fout', $display, '_i_i_');
check('1 waarschuwing', implode('|', $warnings), 'Warning: Uninitialized string offset 4');

// scenario 2
$attempts = 3;
if ($attempts = 0) {
    echo "Game over";
}
check('2 met fout', "Nog " . $attempts . " pogingen", 'Nog 0 pogingen');

$attempts = 3;
if ($attempts == 0) {
    echo "Game over";
}
check('2 gecorrigeerd', "Nog " . $attempts . " pogingen", 'Nog 3 pogingen');

// scenario 3
ob_start();
$attempts = 5;
switch ($attempts) {
    case 5:
        echo "hoofd ";
    case 4:
        echo "romp ";
        break;
    case 3:
        echo "arm ";
        break;
}
check('3 met fout', ob_get_clean(), 'hoofd romp ');

ob_start();
switch ($attempts) {
    case 5:
        echo "hoofd ";
        break;
    case 4:
        echo "romp ";
        break;
    case 3:
        echo "arm ";
        break;
}
check('3 gecorrigeerd', ob_get_clean(), 'hoofd ');
