<?php

declare(strict_types=1);

// een domain event is een feit dat is gebeurd, daarom staat de naam in de verleden tijd
class BookLent
{
    public readonly string $title;
    public readonly string $member;

    public function __construct(string $title, string $member)
    {
        $this->title = $title;
        $this->member = $member;
    }
}

class Library
{
    private array $events = [];

    // het domein meldt alleen wat er is gebeurd, en weet niet wie daarop reageert
    public function lend(string $title, string $member): void
    {
        echo "$title uitgeleend aan $member\n";
        $this->events[] = new BookLent($title, $member);
    }

    // de events worden opgehaald en daarna gewist
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}

// de applicatie bepaalt wie op welk event reageert (het Observer pattern uit les 06)
$listeners = [
    BookLent::class => [
        fn(BookLent $event) => print("E-mail naar {$event->member}: {$event->title} is uitgeleend\n"),
        fn(BookLent $event) => print("[LOG] {$event->title} uitgeleend\n"),
    ],
];

$library = new Library();
$library->lend('Misery', 'Anna');

foreach ($library->pullEvents() as $event) {
    foreach ($listeners[$event::class] ?? [] as $listener) {
        $listener($event);
    }
}

// een extra reactie is een regel erbij in $listeners, Library blijft ongewijzigd
