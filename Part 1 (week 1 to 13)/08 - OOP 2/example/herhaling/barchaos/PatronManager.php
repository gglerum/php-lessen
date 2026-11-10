<?php

class PatronManager
{
    public int $maxPatrons = 100;
    public int $counter = 0;

    public function addPatron(int $amount = 1)
    {
        if ($this->counter + $amount > $this->maxPatrons) {
            throw new \Exception("Can not allow patrons for safety reasons.");
        }
        $this->counter += $amount;
    }

    public function getPatronCount()
    {
        return $this->counter;
    }

    public function spaceLeftForPatrons()
    {
        return $this->maxPatrons - $this->counter;
    }
}
