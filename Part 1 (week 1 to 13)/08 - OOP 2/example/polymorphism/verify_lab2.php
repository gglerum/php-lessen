<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/predict_output.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab2.php (vanuit deze map). Alle regels moeten "ok" zeggen.
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
        '1 Eén functie, twee soorten',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

function announce(Combatant $combatant): void
{
    echo $combatant->getName() . ": " . $combatant->attack() . "\n";
}

announce(new Player("Aragorn", 100, 5));
announce(new Npc("Bob", 40, 0));
CODE,
        ['Aragorn: 20', 'Bob: 1'],
    ],
    [
        '2 Verschillende soorten in een array',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$combatants = [
    new Player("Aragorn", 100, 5),
    new Enemy("Goblin", 30, 2, "een dolk", 3),
    new Npc("Bob", 40, 0),
];

$total = 0;
foreach ($combatants as $combatant) {
    $total = $total + $combatant->attack();
}
echo $total;
CODE,
        ['33'],
    ],
    [
        '3 Een object dat geen Combatant is',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

class Potion
{
}

function announce(Combatant $combatant): void
{
    echo $combatant->getName() . ": " . $combatant->attack() . "\n";
}

announce(new Potion());
CODE,
        ['Fatal error: Uncaught TypeError: announce(): Argument #1 ($combatant) must be of type Combatant, Potion given'],
    ],
    [
        '4 Een method die alleen een child heeft',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

function showLoot(Combatant $combatant): void
{
    echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\n";
}

showLoot(new Enemy("Goblin", 30, 2, "een dolk", 3));
showLoot(new Player("Aragorn", 100, 5));
CODE,
        ['Goblin heeft een dolk', 'Fatal error: Uncaught Error: Call to undefined method Player::getLoot()'],
    ],
    [
        '5 instanceof bij een parent en bij een broer',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$player = new Player("Aragorn", 100, 5);

var_dump($player instanceof Combatant);
var_dump($player instanceof Enemy);
CODE,
        ['bool(true)', 'bool(false)'],
    ],
    [
        '6 instanceof en de echte class',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$combatant = new Enemy("Goblin", 30, 2, "een dolk", 3);

var_dump($combatant instanceof Enemy);
var_dump($combatant::class === Combatant::class);
CODE,
        ['bool(true)', 'bool(false)'],
    ],
    [
        '7 Een Combatant of niets',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

function describeTarget(?Combatant $target): string
{
    if ($target === null) {
        return "Geen doelwit";
    }

    return "Doelwit: " . $target->getName();
}

echo describeTarget(new Npc("Bob", 40, 0)) . "\n";
echo describeTarget(null);
CODE,
        ['Doelwit: Bob', 'Geen doelwit'],
    ],
    [
        '8 Een parameter met een child als type',
        <<<'CODE'
abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
}

class Npc extends Combatant
{
    public function attack(): int
    {
        return 1;
    }
}

class Enemy extends Combatant
{
    private string $loot;
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, string $loot, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
        $this->threatLevel = $threatLevel;
    }

    public function getLoot(): string
    {
        return $this->loot;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

function rest(Player $player): void
{
    echo $player->getName() . " rust uit\n";
}

rest(new Player("Aragorn", 100, 5));
rest(new Npc("Bob", 40, 0));
CODE,
        ['Aragorn rust uit', 'Fatal error: Uncaught TypeError: rest(): Argument #1 ($player) must be of type Player, Npc given'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
