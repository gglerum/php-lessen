<?php

declare(strict_types=1);

require_once 'PatronManager.php';

$patronManager = new PatronManager(100);

$patronManager->addPatron(5);
echo "Current patron count: " . $patronManager->getPatronCount() . "\n";
echo "Space left for patrons: " . $patronManager->spaceLeftForPatrons() . "\n";

try {
    $patronManager->addPatron(100);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "Current patron count: " . $patronManager->getPatronCount() . "\n";
echo "Space left for patrons: " . $patronManager->spaceLeftForPatrons() . "\n";

// in barchaos zetten we hier handmatig de teller op 50. Nu geeft PHP een Error, want counter is private
try {
    $patronManager->counter = 50;
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// en ook de capaciteit kunnen we niet meer "magisch" vergroten
try {
    $patronManager->maxPatrons = 200;
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// het aantal mensen binnen is dus nog steeds 5
echo "Current patron count: " . $patronManager->getPatronCount() . "\n";
echo "Space left for patrons: " . $patronManager->spaceLeftForPatrons() . "\n";
