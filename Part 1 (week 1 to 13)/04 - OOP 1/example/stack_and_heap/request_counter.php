<?php

declare(strict_types=1);

// start met: php -S localhost:8090 (vanuit deze map) en open http://localhost:8090/request_counter.php
// laad de pagina meerdere keren opnieuw

class VisitCounter
{
    private int $count = 0;

    public function increment(): void
    {
        $this->count = $this->count + 1;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}

// bij elk request begint PHP opnieuw: dit script wordt van boven naar beneden uitgevoerd
// en er wordt een nieuw object gemaakt, de vorige is al weggegooid
$counter = new VisitCounter();
$counter->increment();

// een sessie wordt wel bewaard tussen requests (PHP bewaart de data in een bestand op de server)
session_start();
$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;

header('Content-Type: text/plain');
echo "Object (heap): " . $counter->getCount() . "\n";
echo "Sessie:        " . $_SESSION['visits'] . "\n";
