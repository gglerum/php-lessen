<?php

declare(strict_types=1);

// LET OP: dit script geeft met opzet een fout, er is geen base case
// de functie roept zichzelf steeds aan en geen enkel frame wordt ooit weggegooid
function countdown(int $number): void
{
    echo "countdown($number)\n";

    countdown($number - 1);
}

countdown(3);
