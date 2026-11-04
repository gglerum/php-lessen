<?php

declare(strict_types=1);

$books = ['Misery'];

// de menu keuzes worden met een switch uitgevoerd, de acties zelf zijn geen losse dingen
function runAction(string $action, string $title, array &$books): void
{
    switch ($action) {
        case 'add':
            $books[] = $title;
            echo "$title toegevoegd\n";
            break;
        case 'remove':
            $books = array_values(array_diff($books, [$title]));
            echo "$title verwijderd\n";
            break;
    }
}

runAction('add', 'It', $books);
runAction('remove', 'Misery', $books);

echo implode(', ', $books) . "\n";

// ongedaan maken is lastig: we hebben niet bewaard wat er gedaan is, en voor elke nieuwe actie
// moeten we de switch uitbreiden
