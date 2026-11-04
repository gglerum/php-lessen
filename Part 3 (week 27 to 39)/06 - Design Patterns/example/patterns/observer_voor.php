<?php

declare(strict_types=1);

class Mailer
{
    public function sendLoanMail(string $member, string $title): void
    {
        echo "E-mail naar $member: $title is uitgeleend\n";
    }
}

class Logger
{
    public function log(string $message): void
    {
        echo "[LOG] $message\n";
    }
}

class Statistics
{
    private int $loans = 0;

    public function countLoan(): void
    {
        $this->loans++;
        echo "Aantal uitleningen: {$this->loans}\n";
    }
}

class Library
{
    private Mailer $mailer;
    private Logger $logger;
    private Statistics $statistics;

    // Library kent alle drie de onderdelen en weet wat er na een uitlening moet gebeuren
    public function __construct(Mailer $mailer, Logger $logger, Statistics $statistics)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->statistics = $statistics;
    }

    public function lend(string $title, string $member): void
    {
        $this->mailer->sendLoanMail($member, $title);
        $this->logger->log("$title uitgeleend aan $member");
        $this->statistics->countLoan();
    }
}

$library = new Library(new Mailer(), new Logger(), new Statistics());
$library->lend('Misery', 'Anna');

// een vierde reactie (bijvoorbeeld een sms) betekent dat we Library moeten aanpassen
