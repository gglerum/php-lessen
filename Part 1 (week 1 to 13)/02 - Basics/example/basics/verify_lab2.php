<?php

// Controleert de vooraf bepaalde antwoorden van lab/predict_output.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab2.php (vanuit deze map). Alle regels moeten "ok" zeggen.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// scenario 1
$attempts = 7;
$attempts--;
$attempts = $attempts - 2;
check('1', (string) $attempts, '4');

// scenario 2
$display = "";
$display .= "k";
$display .= "_";
$display .= "w";
check('2', $display, 'k_w');

// scenario 3
$word = "kiwi";
$count = 0;
for ($i = 0; $i < strlen($word); $i++) {
    $count = $count + 1;
}
check('3', (string) $count, '4');

// scenario 4
ob_start();
$attempts = 3;
while ($attempts > 0) {
    echo $attempts;
    $attempts--;
}
check('4', ob_get_clean(), '321');

// scenario 5
ob_start();
$score = 55;
if ($score >= 80) {
    echo "Goed";
} elseif ($score >= 55) {
    echo "Voldoende";
} else {
    echo "Onvoldoende";
}
check('5', ob_get_clean(), 'Voldoende');

// scenario 6
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
check('6', ob_get_clean(), 'hoofd romp ');
