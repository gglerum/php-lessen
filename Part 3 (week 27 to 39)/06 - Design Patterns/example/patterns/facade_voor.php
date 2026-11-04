<?php

declare(strict_types=1);

class MemberRepository
{
    public function exists(string $member): bool
    {
        return in_array($member, ['Anna', 'Bram']);
    }
}

class Library
{
    private array $loans = [];

    public function lend(string $title, string $member): void
    {
        $this->loans[] = [$title, $member];
    }
}

class Notifier
{
    public function send(string $member, string $message): void
    {
        echo "Bericht aan $member: $message\n";
    }
}

class Logger
{
    public function log(string $message): void
    {
        echo "[LOG] $message\n";
    }
}

$members = new MemberRepository();
$library = new Library();
$notifier = new Notifier();
$logger = new Logger();

// elke plek die een boek wil uitlenen moet deze vier stappen in de goede volgorde kennen
if ($members->exists('Anna')) {
    $library->lend('Misery', 'Anna');
    $notifier->send('Anna', 'Misery is uitgeleend');
    $logger->log('Misery uitgeleend aan Anna');
}

// en een tweede plek (een andere pagina, een import) herhaalt ze
if ($members->exists('Bram')) {
    $library->lend('It', 'Bram');
    $notifier->send('Bram', 'It is uitgeleend');
    $logger->log('It uitgeleend aan Bram');
}
