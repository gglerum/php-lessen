<?php

declare(strict_types=1);

class PatronManager
{
    // readonly: de maximale capaciteit wordt bij het openen bepaald en verandert daarna niet meer
    private readonly int $maxPatrons;

    // private: alleen deze class bepaalt hoeveel mensen er binnen zijn
    private int $counter = 0;

    public function __construct(int $maxPatrons = 100)
    {
        $this->maxPatrons = $maxPatrons;
    }

    public function addPatron(int $amount = 1): void
    {
        // private helpt alleen als de methods zelf ook goed controleren: met -10 zou de teller anders omlaag gaan
        if ($amount < 1) {
            throw new \Exception("You have to let in at least one patron.");
        }

        // dezelfde controle als in barchaos, maar nu kan niemand hem meer omzeilen
        if ($this->counter + $amount > $this->maxPatrons) {
            throw new \Exception("Can not allow patrons for safety reasons.");
        }

        $this->counter = $this->counter + $amount;
    }

    public function getPatronCount(): int
    {
        return $this->counter;
    }

    public function spaceLeftForPatrons(): int
    {
        return $this->maxPatrons - $this->counter;
    }
}
