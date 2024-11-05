<?php
class MovieRepository
{
    private readonly DAO $dao;


    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    public function add(Movie $movie): int
    {
        return $this->dao->add($movie);
    }

    public function remove(int $id): void
    {
        $this->dao->remove($id);
    }
}
