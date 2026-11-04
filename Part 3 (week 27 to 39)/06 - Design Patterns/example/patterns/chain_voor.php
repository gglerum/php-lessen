<?php

declare(strict_types=1);

// alle controles voor het uitlenen staan in één functie, in een vaste volgorde
function validateLoan(array $request, array $members, array $availableBooks): string
{
    if (!in_array($request['member'], $members)) {
        return 'Onbekend lid';
    }

    if ($request['loans'] >= 2) {
        return 'Maximaal aantal uitleningen bereikt';
    }

    if (!in_array($request['title'], $availableBooks)) {
        return 'Boek is niet beschikbaar';
    }

    return 'ok';
}

$members = ['Anna', 'Bram'];
$availableBooks = ['Misery', 'It'];

echo validateLoan(['member' => 'Chris', 'title' => 'It', 'loans' => 0], $members, $availableBooks) . "\n";
echo validateLoan(['member' => 'Anna', 'title' => 'It', 'loans' => 2], $members, $availableBooks) . "\n";
echo validateLoan(['member' => 'Anna', 'title' => 'Carrie', 'loans' => 0], $members, $availableBooks) . "\n";
echo validateLoan(['member' => 'Anna', 'title' => 'It', 'loans' => 0], $members, $availableBooks) . "\n";

// een nieuwe controle of een andere volgorde (bijvoorbeeld voor een uitleenbalie en een zelfscan) betekent
// dat we deze functie moeten aanpassen of kopiëren
