<?php

declare(strict_types=1);

// Controleert de uitvoer in lab/bughunt.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab3.php (vanuit deze map). Alle regels moeten "ok" zeggen.
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
        '1 De vijand gaat nooit dood',
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
        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\n";
        }
    }
}

$enemy = new Enemy("Goblin", 10, 2, "een dolk");
$enemy->takeDamage(12);
echo "Health: " . $enemy->getHealth();
CODE,
        ['Health: 10'],
    ],
    [
        '1 (gecorrigeerd) De vijand gaat nooit dood',
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
echo "Health: " . $enemy->getHealth();
CODE,
        ['Goblin is verslagen en laat een dolk vallen', 'Health: 0'],
    ],
    [
        '2 Een object van een abstracte class',
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

    abstract public function attack(): int;
}

class Enemy extends Combatant
{
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->threatLevel = $threatLevel;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$enemy = new Combatant("Goblin", 30, 2);
echo $enemy->attack();
CODE,
        ['Fatal error: Uncaught Error: Cannot instantiate abstract class Combatant'],
    ],
    [
        '2 (gecorrigeerd) Een object van een abstracte class',
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

    abstract public function attack(): int;
}

class Enemy extends Combatant
{
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating);
        $this->threatLevel = $threatLevel;
    }

    public function attack(): int
    {
        return $this->threatLevel * 4;
    }
}

$enemy = new Enemy("Goblin", 30, 2, 3);
echo $enemy->attack();
CODE,
        ['12'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
