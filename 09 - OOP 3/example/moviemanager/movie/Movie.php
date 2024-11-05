<?php
class Movie
{
    static int $movieCount = 1;
    public readonly int $id;

    public function __construct(
        public readonly string $title,
        public readonly string $director
    ) {
        $this->id = static::$movieCount++;
    }
}
