<?php

// zonder gebruik van functies moeten we een expressie steeds weer herhalen
//math formula with multiple variables
$a = 5;
$b = 10;
$c = 15;
$result = ($a + $b) * $c;
echo $result;

echo "\n";

// herhaling van dezelfde expressie met andere waarden
$a = 2;
$b = 4;
$c = 6;
$result = ($a + $b) * $c;
echo $result;

echo "\n";

// herhaling van dezelfde expressie met nog andere waarden
$a = 1;
$b = 3;
$c = 5;
$result = ($a + $b) * $c;
echo $result;

echo "\n";

// nu doen we hetzelde met een functie
function calculate($a, $b, $c)
{
    return ($a + $b) * $c;
}

echo calculate(5, 10, 15);
echo "\n";
echo calculate(2, 4, 6);
echo "\n";
echo calculate(1, 3, 5);
echo "\n";

// dit scheelt een hoop regels code, maakt het makkelijker dezelfde berekening met andere waarden uit te voeren
// en zorgt er voor dat als de berekening ooit verandert, we dat maar op één plek hoeven aan te passen
