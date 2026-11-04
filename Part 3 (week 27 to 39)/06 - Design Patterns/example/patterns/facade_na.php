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

// de facade is één eenvoudige ingang voor een groep classes die samen iets doen
class LibraryFacade
{
    private MemberRepository $members;
    private Library $library;
    private Notifier $notifier;
    private Logger $logger;

    public function __construct(MemberRepository $members, Library $library, Notifier $notifier, Logger $logger)
    {
        $this->members = $members;
        $this->library = $library;
        $this->notifier = $notifier;
        $this->logger = $logger;
    }

    // de volgorde van de stappen staat op één plek
    public function lend(string $title, string $member): void
    {
        if (!$this->members->exists($member)) {
            return;
        }

        $this->library->lend($title, $member);
        $this->notifier->send($member, "$title is uitgeleend");
        $this->logger->log("$title uitgeleend aan $member");
    }
}

$facade = new LibraryFacade(new MemberRepository(), new Library(), new Notifier(), new Logger());

// wie een boek wil uitlenen hoeft alleen nog de facade te kennen
$facade->lend('Misery', 'Anna');
$facade->lend('It', 'Bram');

// de facade verbergt de classes niet voor altijd: wie meer nodig heeft kan ze nog steeds direct gebruiken
