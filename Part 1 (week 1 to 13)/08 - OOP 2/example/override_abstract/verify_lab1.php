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
        '1 Een method overschrijven met parent::',
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
        '2 Vervangen of uitbreiden',
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

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)";
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
}

class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating);
        $this->loot = $loot;
    }

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich";
    }
}

$npc = new Npc("Bob", 40, 0, "Hallo");
$enemy = new Enemy("Goblin", 30, 2, "een dolk");
echo $npc->describe() . "\n";
echo $enemy->describe();
CODE,
        ['Bob zegt: "Hallo"', 'Goblin (30 health) en heeft een dolk bij zich'],
    ],
    [
        '3 Een abstracte class',
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

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10);
    }
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

$player = new Player("Aragorn", 100, 5);
$enemy = new Enemy("Goblin", 30, 2, 3);
echo $player->attack() . "\n";
echo $enemy->attack();
CODE,
        ['20', '12'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
