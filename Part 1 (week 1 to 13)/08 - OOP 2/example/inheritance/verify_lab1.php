<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/objects.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// Elk scenario draait in een eigen PHP proces, zodat de classes van de scenario's elkaar niet in de weg zitten.


function runScript(string $code): array
{
    $file = tempnam(sys_get_temp_dir(), 'lab');
    file_put_contents($file, "<?php\n\ndeclare(strict_types=1);\n\n" . $code . "\n");
    $output = shell_exec(escapeshellarg(PHP_BINARY) . ' -d xdebug.mode=off -d log_errors=0 -d display_errors=1 -d html_errors=0 -d error_reporting=-1 ' . escapeshellarg($file) . ' 2>&1');
    unlink($file);

    $lines = [];
    foreach (explode("\n", (string) $output) as $line) {
        // "Warning: tekst in /pad/bestand.php on line 12" wordt "Warning: tekst"
        $line = preg_replace('/ in \S+ on line \d+$/', '', $line);
        // "PHP Fatal error:  Uncaught Error: tekst in /pad:12" wordt "Fatal error: Uncaught Error: tekst"
        $line = preg_replace('/^(PHP )?(Fatal error|Warning):\s+/', '$2: ', $line);
        $line = preg_replace('/ in \S+:\d+$/', '', $line);
        if ($line === '' || str_starts_with($line, 'Stack trace:') || str_starts_with($line, '#') || str_starts_with($line, '  thrown')) {
            continue;
        }
        $lines[] = $line;
    }

    return $lines;
}

function check(string $name, array $actual, array $expected): void
{
    // bij een Fatal error mag de melding langer zijn dan wat het lab laat zien
    $ok = count($actual) === count($expected);
    foreach ($expected as $i => $line) {
        if (!isset($actual[$i]) || !(str_starts_with($actual[$i], $line))) {
            $ok = false;
        }
    }
    echo $ok ? "ok    " : "FOUT  ", $name, $ok ? "" : " (kreeg '" . implode('|', $actual) . "', verwacht '" . implode('|', $expected) . "')", "\n";
}

$scenarios = [
    [
        '1 Een Player erft van Combatant',
        <<<'CODE'
class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function getHealth(): int
    {
        return $this->health;
    }
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
    }
}

$player = new Player("Aragorn", 100, 5);
$player->takeDamage(20);
$player->levelUp();
echo $player->getHealth();
CODE,
        ['85'],
    ],
    [
        '2 De constructor van de parent',
        <<<'CODE'
class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function talk(): string
    {
        return $this->dialogue;
    }
}

$npc = new Npc("Bob", 40, 0, "Welkom in de Rode Draak!");
echo $npc->talk();
CODE,
        ['Welkom in de Rode Draak!'],
    ],
    [
        '3 Een method overschrijven',
        <<<'CODE'
class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name;
        $this->health = $health;
        $this->armorRating = $armorRating;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function getHealth(): int
    {
        return $this->health;
    }
}

class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }
}

$enemy = new Enemy("Goblin", 10, 2, "een dolk");
$enemy->takeDamage(12);
echo $enemy->getHealth();
CODE,
        ['Goblin is verslagen en laat een dolk vallen', '0'],
    ],
    [
        '4 Een boom van parents',
        <<<'CODE'
class Entity
{
    protected string $name;
    protected int $rank;

    public function __construct(string $name, int $rank)
    {
        $this->name = $name;
        $this->rank = $rank;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class Participant extends Entity
{
    protected int $dice;

    public function __construct(string $name, int $rank, int $dice)
    {
        parent::__construct($name, $rank);
        $this->dice = $dice;
    }
}

class Combatant extends Participant
{
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $rank, int $dice, int $health, int $armorRating)
    {
        parent::__construct($name, $rank, $dice);
        $this->health = $health;
        $this->armorRating = $armorRating;
    }
}

class Player extends Combatant
{
    private int $experiencePoints = 0;
}

$player = new Player("Aragorn", 1, 20, 100, 5);
echo $player->getName();
CODE,
        ['Aragorn'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
