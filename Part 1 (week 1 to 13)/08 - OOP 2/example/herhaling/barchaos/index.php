<?php

require_once 'PatronManager.php';

$patronManager = new PatronManager();

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

// handmatig bepalen dat er minder patrons zijn zodat wij er meer binnen kunnen proppen
$patronManager->counter = 50;
echo "Current patron count: " . $patronManager->getPatronCount() . "\n";
echo "Space left for patrons: " . $patronManager->spaceLeftForPatrons() . "\n";

// handmatig bepalen dat er in eens magisch meer ruimte is voor patrons
$patronManager->maxPatrons = 200;
$patronManager->addPatron(100);

echo "Current patron count: " . $patronManager->getPatronCount() . "\n";
echo "Space left for patrons: " . $patronManager->spaceLeftForPatrons() . "\n";
