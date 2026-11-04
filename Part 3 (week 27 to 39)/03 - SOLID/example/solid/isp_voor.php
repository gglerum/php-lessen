<?php

declare(strict_types=1);

// één grote interface voor alles wat een repository kan
interface Repository
{
    public function find(int $id): ?array;

    public function save(array $item): void;

    public function delete(int $id): void;
}

class BookRepository implements Repository
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

// het archief is alleen-lezen, maar moet toch save() en delete() hebben
class ArchiveRepository implements Repository
{
    private array $books = [1 => ['title' => 'Het geheime dagboek']];

    public function find(int $id): ?array
    {
        return $this->books[$id] ?? null;
    }

    public function save(array $item): void
    {
        throw new LogicException('Het archief is alleen-lezen');
    }

    public function delete(int $id): void
    {
        throw new LogicException('Het archief is alleen-lezen');
    }
}

// deze functie leest alleen, maar vraagt om een Repository met ook methods die ze nooit gebruikt
function printTitle(Repository $repository, int $id): void
{
    echo $repository->find($id)['title'] . "\n";
}

printTitle(new BookRepository(), 1);
printTitle(new ArchiveRepository(), 1);

// en wie een ArchiveRepository als Repository gebruikt, ontdekt pas tijdens het draaien dat save() niet werkt
try {
    (new ArchiveRepository())->save(['title' => 'Nieuw boek']);
} catch (LogicException $exception) {
    echo 'Fout: ' . $exception->getMessage() . "\n";
}
