<?php

declare(strict_types=1);

// het domein: Library weet niets van opslag
class Library
{
    private array $loans = [];

    public function lend(string $title, string $member): void
    {
        $this->loans[] = [$title, $member];
    }

    public function count(): int
    {
        return count($this->loans);
    }
}

// de afspraak staat in het domein: het domein bepaalt wat het nodig heeft ...
interface LibraryRepository
{
    public function get(): Library;

    public function save(Library $library): void;
}

// ... en de uitwerking hoort bij de infrastructuur, hier een array, later een database
class InMemoryLibraryRepository implements LibraryRepository
{
    private ?Library $library = null;

    public function get(): Library
    {
        return $this->library ?? new Library();
    }

    public function save(Library $library): void
    {
        $this->library = $library;
    }
}

// de applicatie praat alleen met de interface
class LendBook
{
    private LibraryRepository $repository;

    public function __construct(LibraryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $title, string $member): void
    {
        $library = $this->repository->get();
        $library->lend($title, $member);
        $this->repository->save($library);
    }
}

$repository = new InMemoryLibraryRepository();
(new LendBook($repository))->execute('Misery', 'Anna');

echo $repository->get()->count() . " uitlening(en) geladen\n";

// een database is een andere class die LibraryRepository implementeert, Library en LendBook veranderen niet
