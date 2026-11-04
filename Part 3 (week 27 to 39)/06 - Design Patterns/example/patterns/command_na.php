<?php

declare(strict_types=1);

class Catalog
{
    private array $books = ['Misery'];

    public function add(string $title): void
    {
        $this->books[] = $title;
    }

    public function remove(string $title): void
    {
        $this->books = array_values(array_diff($this->books, [$title]));
    }

    public function list(): string
    {
        return implode(', ', $this->books);
    }
}

// een actie is nu een object met een execute() en een undo()
interface Command
{
    public function execute(): void;

    public function undo(): void;
}

class AddBook implements Command
{
    private Catalog $catalog;
    private string $title;

    public function __construct(Catalog $catalog, string $title)
    {
        $this->catalog = $catalog;
        $this->title = $title;
    }

    public function execute(): void
    {
        $this->catalog->add($this->title);
        echo "{$this->title} toegevoegd\n";
    }

    public function undo(): void
    {
        $this->catalog->remove($this->title);
        echo "{$this->title} weer verwijderd\n";
    }
}

class RemoveBook implements Command
{
    private Catalog $catalog;
    private string $title;

    public function __construct(Catalog $catalog, string $title)
    {
        $this->catalog = $catalog;
        $this->title = $title;
    }

    public function execute(): void
    {
        $this->catalog->remove($this->title);
        echo "{$this->title} verwijderd\n";
    }

    public function undo(): void
    {
        $this->catalog->add($this->title);
        echo "{$this->title} weer toegevoegd\n";
    }
}

// de geschiedenis onthoudt wat er is uitgevoerd, zodat we het ongedaan kunnen maken
class History
{
    private array $commands = [];

    public function run(Command $command): void
    {
        $command->execute();
        $this->commands[] = $command;
    }

    public function undoLast(): void
    {
        $command = array_pop($this->commands);
        $command?->undo();
    }
}

$catalog = new Catalog();
$history = new History();

$history->run(new AddBook($catalog, 'It'));
$history->run(new RemoveBook($catalog, 'Misery'));
echo $catalog->list() . "\n";

$history->undoLast();
echo $catalog->list() . "\n";
