<?php

// Controleert de uitvoer in lab/scope.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab2.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// Een PHP waarschuwing wordt opgevangen en net als in het lab als "Warning: ..." voor de uitvoer gezet.

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
function printGreetingWithoutParameter(): void
{
    echo "Hallo " . $name;
}
$name = "Sam";
ob_start();
printGreetingWithoutParameter();
check('1 uitvoer', ob_get_clean(), 'Hallo ');
check('1 waarschuwing', implode('|', $warnings), 'Warning: Undefined variable $name');

// scenario 2
function printGreeting(string $name): void
{
    echo "Hallo " . $name;
}
ob_start();
printGreeting($name);
check('2', ob_get_clean(), 'Hallo Sam');

// scenario 3
function printGreetingGlobal(): void
{
    global $name;
    echo "Hallo " . $name;
}
ob_start();
printGreetingGlobal();
check('3', ob_get_clean(), 'Hallo Sam');

// scenario 4
function describe(string $name): string
{
    $name = $name . "!";
    return "Hallo " . $name;
}
ob_start();
echo describe($name) . " en " . $name;
check('4', ob_get_clean(), 'Hallo Sam! en Sam');
