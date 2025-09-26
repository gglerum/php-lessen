<?php
class MysqlDAO implements DAO
{
    private readonly PdoService $pdoService;

    public function __construct()
    {
        $this->pdoService = new PdoService();
    }

    public function add(Movie $movie): int
    {
        $this->pdoService->insert(
            "INSERT INTO movies(title, dirctor) VALUES (?, ?, ?, ?, ?)",
            [
                $movie->title,
                $movie->director
            ]
        );

        return $movie->id;
    }

    public function remove(int $id): void
    {
        $this->pdoService->delete($id, "movies");
    }
}
