<?php

declare(strict_types=1);

class Mailer
{
    public function send(string $to, string $message): void
    {
        echo "E-mail naar $to: $message\n";
    }
}

class Library
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    // het domein stuurt zelf een e-mail: Library weet van techniek en van wat er daarna moet gebeuren
    public function lend(string $title, string $member): void
    {
        echo "$title uitgeleend aan $member\n";
        $this->mailer->send($member, "$title is uitgeleend");
    }
}

(new Library(new Mailer()))->lend('Misery', 'Anna');

// een extra reactie, zoals een statistiek, betekent Library aanpassen
