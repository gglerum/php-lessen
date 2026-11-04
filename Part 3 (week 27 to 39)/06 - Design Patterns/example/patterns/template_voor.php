<?php

declare(strict_types=1);

$books = ['Misery', 'It'];

// twee rapporten met bijna dezelfde stappen: kop, regels, voet
class TextReport
{
    public function render(array $books): string
    {
        $output = "== Catalogus ==\n";

        foreach ($books as $book) {
            $output .= "- $book\n";
        }

        $output .= 'Totaal: ' . count($books) . "\n";

        return $output;
    }
}

class HtmlReport
{
    public function render(array $books): string
    {
        $output = "<h1>Catalogus</h1>\n<ul>\n";

        foreach ($books as $book) {
            $output .= "<li>$book</li>\n";
        }

        $output .= "</ul>\n<p>Totaal: " . count($books) . "</p>\n";

        return $output;
    }
}

echo (new TextReport())->render($books);
echo (new HtmlReport())->render($books);

// de volgorde van de stappen staat in beide classes: past die volgorde aan, dan moeten we dat twee keer doen
