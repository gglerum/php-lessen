<?php
interface DAO
{
    public function add(Movie $movie): int;

    public function remove(int $id): void;
}
