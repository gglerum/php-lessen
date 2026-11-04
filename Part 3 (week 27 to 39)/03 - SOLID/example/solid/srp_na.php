<?php

declare(strict_types=1);

// elke class heeft nu één reden om te veranderen

// verandert alleen als de validatieregels veranderen
class BookValidator
{
    // geeft een foutmelding terug, of null als alles klopt
    public function validate(array $input): ?string
    {
        if ($input['title'] === '') {
            return 'De titel ontbreekt';
        }

        if ((int)$input['pages'] < 1) {
            return 'Een boek heeft minimaal 1 pagina';
        }

        return null;
    }
}

// verandert alleen als de manier van opslaan verandert (bijvoorbeeld een database)
class BookRepository
{
    private array $books = [];

    public function add(array $book): void
    {
        $this->books[] = $book;
    }
}

// verandert alleen als de uitvoer verandert (bijvoorbeeld HTML in plaats van tekst)
class BookConsolePrinter
{
    public function printSaved(array $book): void
    {
        echo "Boek opgeslagen: " . $book['title'] . ' (' . $book['pages'] . " pagina's)\n";
    }
}

// de handler verbindt de onderdelen en bepaalt de volgorde, de rest doen de andere classes
class BookHandler
{
    private BookValidator $validator;
    private BookRepository $repository;
    private BookConsolePrinter $printer;

    public function __construct(BookValidator $validator, BookRepository $repository, BookConsolePrinter $printer)
    {
        $this->validator = $validator;
        $this->repository = $repository;
        $this->printer = $printer;
    }

    public function handle(array $input): void
    {
        $error = $this->validator->validate($input);

        if ($error !== null) {
            echo $error . "\n";
            return;
        }

        $book = ['title' => $input['title'], 'pages' => (int)$input['pages']];

        $this->repository->add($book);
        $this->printer->printSaved($book);
    }
}

$handler = new BookHandler(new BookValidator(), new BookRepository(), new BookConsolePrinter());
$handler->handle(['title' => '', 'pages' => '300']);
$handler->handle(['title' => 'Misery', 'pages' => '0']);
$handler->handle(['title' => 'Misery', 'pages' => '320']);
