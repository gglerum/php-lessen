<?php

declare(strict_types=1);

// de interface is opgesplitst in kleine afspraken
interface ReadableRepository
{
    public function find(int $id): ?array;
}

interface WritableRepository
{
    public function save(array $item): void;

    public function delete(int $id): void;
}

// een class implementeert alleen wat hij ook echt kan
class BookRepository implements ReadableRepository, WritableRepository
{
    private array $books = [1 => ['title' => 'Misery']];

    public function find(int $id): ?array
    {
        return $this->books[$id] ?? null;
    }

    public function save(array $item): void
    {
        $this->books[] = $item;
    }

    public function delete(int $id): void
    {
        unset($this->books[$id]);
    }
}

// het archief is alleen-lezen en heeft nu geen methods meer die niet werken
class ArchiveRepository implements ReadableRepository
{
    private array $books = [1 => ['title' => 'Het geheime dagboek']];

    public function find(int $id): ?array
    {
        return $this->books[$id] ?? null;
    }
}

// de functie vraagt alleen om wat hij nodig heeft
function printTitle(ReadableRepository $repository, int $id): void
{
    echo $repository->find($id)['title'] . "\n";
}

printTitle(new BookRepository(), 1);
printTitle(new ArchiveRepository(), 1);
