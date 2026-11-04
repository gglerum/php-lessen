<?php

declare(strict_types=1);

$books = ['Misery', 'It'];

abstract class Report
{
    // de template method: de volgorde van de stappen staat hier vast, final voorkomt dat een subclass hem verandert
    final public function render(array $books): string
    {
        $output = $this->header();

        foreach ($books as $book) {
            $output .= $this->line($book);
        }

        return $output . $this->footer(count($books));
    }

    // de stappen die per soort rapport verschillen, vullen de subclasses zelf in
    abstract protected function header(): string;

    abstract protected function line(string $book): string;

    abstract protected function footer(int $total): string;
}

class TextReport extends Report
{
    protected function header(): string
    {
        return "== Catalogus ==\n";
    }

    protected function line(string $book): string
    {
        return "- $book\n";
    }

    protected function footer(int $total): string
    {
        return "Totaal: $total\n";
    }
}

class HtmlReport extends Report
{
    protected function header(): string
    {
        return "<h1>Catalogus</h1>\n<ul>\n";
    }

    protected function line(string $book): string
    {
        return "<li>$book</li>\n";
    }

    protected function footer(int $total): string
    {
        return "</ul>\n<p>Totaal: $total</p>\n";
    }
}

echo (new TextReport())->render($books);
echo (new HtmlReport())->render($books);
