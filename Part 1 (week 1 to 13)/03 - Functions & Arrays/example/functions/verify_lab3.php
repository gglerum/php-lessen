<?php

// Controleert de vooraf bepaalde antwoorden van lab/predict_output.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab3.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// De scenario's met strict_types staan in aparte bestanden, omdat declare(strict_types=1) per bestand geldt.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// scenario 1 en 2
function applyDiscount(int $price): int
{
    $price = $price - 10;
    return $price;
}
$price = 50;
applyDiscount($price);
check('1', (string) $price, '50');
$price = applyDiscount($price);
check('2', (string) $price, '40');

// scenario 3
function showDouble(int $number): void
{
    echo $number * 2;
}
ob_start();
$result = showDouble(4);
var_dump($result);
check('3', trim(ob_get_clean()), '8NULL');

// scenario 4
function addBonus(int &$score): void
{
    $score = $score + 10;
}
$score = 50;
addBonus($score);
check('4', (string) $score, '60');

// scenario 5 en 6: een tijdelijk bestand per variant
$base = "function triple(int \$number): int\n{\n    return \$number * 3;\n}\n\necho triple(\"5\");\n";
$cases = [
    '5' => ["<?php\n\ndeclare(strict_types=1);\n\n" . $base, 'TypeError'],
    '6' => ["<?php\n\n" . $base, '15'],
];
foreach ($cases as $name => [$source, $expected]) {
    $file = tempnam(sys_get_temp_dir(), 'lab3');
    file_put_contents($file, $source);
    $output = shell_exec('php -d xdebug.mode=off ' . escapeshellarg($file) . ' 2>&1');
    unlink($file);
    $actual = str_contains((string) $output, 'TypeError') ? 'TypeError' : trim((string) $output);
    check($name, $actual, $expected);
}
