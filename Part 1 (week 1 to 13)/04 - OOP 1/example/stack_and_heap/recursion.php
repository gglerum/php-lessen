<?php

declare(strict_types=1);

// een recursieve functie roept zichzelf aan, bij elke aanroep hoort een eigen stack frame
function countdown(int $number): void
{
    echo str_repeat("  ", 3 - $number) . "push: countdown($number)\n";

    // base case: hier stoppen we, anders blijven we zichzelf aanroepen
    if ($number === 0) {
        echo str_repeat("  ", 3 - $number) . "base case bereikt\n";
        return;
    }

    countdown($number - 1);

    // pas als de diepere aanroep klaar is, komen we hier terug
    echo str_repeat("  ", 3 - $number) . "pop:  countdown($number)\n";
}

countdown(3);

echo "\n";

// bij factorial gebruiken we de return waarde van de diepere aanroep
// factorial(3) wacht op factorial(2), die wacht op factorial(1)
function factorial(int $number): int
{
    echo "push: factorial($number)\n";

    if ($number <= 1) {
        echo "pop:  factorial($number) geeft 1\n";
        return 1;
    }

    $result = $number * factorial($number - 1);
    echo "pop:  factorial($number) geeft $result\n";

    return $result;
}

echo "Resultaat: " . factorial(4) . "\n";
