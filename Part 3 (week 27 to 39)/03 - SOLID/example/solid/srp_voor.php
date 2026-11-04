<?php

declare(strict_types=1);

// deze class heeft vier redenen om te veranderen: de validatieregels, de opbouw van een boek,
// de manier van opslaan en de manier van tonen
class BookHandler
{
    private array $books = [];

    public function handle(array $input): void
    {
        // valideren
        if ($input['title'] === '') {
            echo "De titel ontbreekt\n";
            return;
        }

        if ((int)$input['pages'] < 1) {
            echo "Een boek heeft minimaal 1 pagina\n";
            return;
        }

        // het boek opbouwen
        $book = ['title' => $input['title'], 'pages' => (int)$input['pages']];

        // opslaan
        $this->books[] = $book;

        // tonen
        echo "Boek opgeslagen: " . $book['title'] . ' (' . $book['pages'] . " pagina's)\n";
    }
}

$handler = new BookHandler();
$handler->handle(['title' => '', 'pages' => '300']);
$handler->handle(['title' => 'Misery', 'pages' => '0']);
$handler->handle(['title' => 'Misery', 'pages' => '320']);
