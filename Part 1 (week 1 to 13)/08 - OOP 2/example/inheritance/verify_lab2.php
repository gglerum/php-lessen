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
        '1 Een method van de parent gebruiken',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$player = new Player("Aragorn", 100, 5);
$player->takeDamage(20);
echo $player->getHealth();
CODE,
        ['85'],
    ],
    [
        '2 Een method die vervangen is',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$npc = new Npc("Bob", 40, 0, "Hallo");
echo $npc->describe();
CODE,
        ['Bob zegt: "Hallo"'],
    ],
    [
        '3 Een method die wordt uitgebreid',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$enemy = new Enemy("Goblin", 30, 2, "een dolk");
echo $enemy->describe();
CODE,
        ['Goblin (30 health) en heeft een dolk bij zich'],
    ],
    [
        '4 Een protected property lezen',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$player = new Player("Aragorn", 100, 5);
echo $player->health;
CODE,
        ['Fatal error: Uncaught Error: Cannot access protected property Player::$health'],
    ],
    [
        '5 Een override met parent::',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$enemy = new Enemy("Goblin", 30, 2, "een dolk");

$enemy->takeDamage(10);
echo $enemy->getHealth() . "\n";

$enemy->takeDamage(50);
echo $enemy->getHealth() . "\n";
CODE,
        ['22', 'Goblin is verslagen en laat een dolk vallen', '-26'],
    ],
    [
        '6 Een constructor die de parent vervangt',
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
}

class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100;
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

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage);

        if ($this->health <= 0) {
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen
";
        }
    }
}

$npc = new Npc("Bob", 40, 0);
CODE,
        ['Fatal error: Uncaught ArgumentCountError: Too few arguments to function Npc::__construct(), 3 passed'],
    ],
];

foreach ($scenarios as [$name, $code, $expected]) {
    check($name, runScript($code), $expected);
}
