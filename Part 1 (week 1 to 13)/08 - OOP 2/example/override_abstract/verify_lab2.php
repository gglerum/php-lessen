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
        '1 Een method die vervangen is',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$npc = new Npc("Bob", 40, 0, "Hallo");
echo $npc->describe();
CODE,
        ['Bob zegt: "Hallo"'],
    ],
    [
        '2 Een method die wordt uitgebreid',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$enemy = new Enemy("Goblin", 30, 2, "een dolk", 3);
echo $enemy->describe();
CODE,
        ['Goblin (30 health) en heeft een dolk bij zich'],
    ],
    [
        '3 Een override met parent::',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$enemy = new Enemy("Goblin", 30, 2, "een dolk", 3);

$enemy->takeDamage(10);
echo $enemy->getHealth() . "\n";

$enemy->takeDamage(50);
echo $enemy->getHealth() . "\n";
CODE,
        ['22', 'Goblin is verslagen en laat een dolk vallen', '-26'],
    ],
    [
        '4 Elke child zijn eigen attack()',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$player = new Player("Aragorn", 100, 5);
$enemy = new Enemy("Goblin", 30, 2, "een dolk", 3);

echo $player->attack() + $enemy->attack();
CODE,
        ['32'],
    ],
    [
        '5 Een object van een abstracte class',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$combatant = new Combatant("Onbekend", 10, 0);
CODE,
        ['Fatal error: Uncaught Error: Cannot instantiate abstract class Combatant'],
    ],
    [
        '6 Een abstracte method vergeten',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

class Merchant extends Combatant
{
}
CODE,
        ['Fatal error: Class Merchant contains 1 abstract method and must therefore be declared abstract or implement the remaining method (Combatant::attack)'],
    ],
    [
        '7 Een extra verplichte parameter',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

class Boss extends Enemy
{
    public function attack(int $bonus): int
    {
        return parent::attack() + $bonus;
    }
}
CODE,
        ['Fatal error: Declaration of Boss::attack(int $bonus): int must be compatible with Enemy::attack(): int'],
    ],
    [
        '8 Een extra optionele parameter',
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

    public function getHealth(): int
    {
        return $this->health;
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating);
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating);
        $this->dialogue = $dialogue;
    }

    public function describe(): string
    {
        return $this->name . " zegt: \"" . $this->dialogue . "\"";
    }

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

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

class Boss extends Enemy
{
    public function attack(bool $critical = false): int
    {
        return parent::attack() * ($critical ? 2 : 1);
    }
}

$boss = new Boss("Smaug", 500, 20, "goud", 5);
echo $boss->attack() . "\n";
echo $boss->attack(true) . "\n";
CODE,
        ['20', '40'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
