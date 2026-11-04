<?php

declare(strict_types=1);

class Member
{
    private string $name;
    private array $books;

    public function __construct(string $name, array $books = [])
    {
        $this->name = $name;
        $this->books = $books;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasBook(string $book): bool
    {
        return in_array($book, $this->books);
    }

    public function receive(string $book): void
    {
        $this->books[] = $book;
    }

    public function give(string $book): void
    {
        $this->books = array_values(array_diff($this->books, [$book]));
    }
}

// de regel hoort bij twee leden tegelijk, dus niet bij één van beide: een domain service met een naam uit de domeintaal
class BookTransfer
{
    public function transfer(string $book, Member $from, Member $to): void
    {
        if (!$from->hasBook($book)) {
            throw new DomainException($from->getName() . " heeft $book niet");
        }

        $from->give($book);
        $to->receive($book);
    }
}

class TransferController
{
    public function handle(Member $from, Member $to, string $book): void
    {
        try {
            (new BookTransfer())->transfer($book, $from, $to);
            echo "$book is doorgegeven van " . $from->getName() . ' aan ' . $to->getName() . "\n";
        } catch (DomainException $exception) {
            echo $exception->getMessage() . "\n";
        }
    }
}

$anna = new Member('Anna', ['Misery']);
$bram = new Member('Bram');

$controller = new TransferController();
$controller->handle($anna, $bram, 'Misery');
$controller->handle($anna, $bram, 'Misery');
