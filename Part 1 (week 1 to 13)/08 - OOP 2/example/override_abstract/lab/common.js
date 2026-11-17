// Gedeelde onderdelen voor de labs over method overriding en abstracte classes: de code van de classes, uitleg per onderdeel en herbruikbare stappen.
// De classes zijn ingekort ten opzichte van de voorbeeldbestanden in example/override_abstract, maar werken hetzelfde.
// Labels beginnen met c- om niet te botsen met die van een scenario.

// ---------- de classes als code in het programma ----------

// override.php: Combatant met takeDamage(), Enemy breidt takeDamage() uit met parent::
const LISTING_COMBATANT = `class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name; // @c-name
        $this->health = $health; // @c-health
        $this->armorRating = $armorRating; // @c-armor
    }

    public function takeDamage(int $damage): void
    {
        $this->health = $this->health - max(0, $damage - $this->armorRating); // @c-dmg
    }

    public function getHealth(): int
    {
        return $this->health; // @c-get
    }
}
`;

const LISTING_ENEMY = `class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->loot = $loot; // @c-extra
    }

    public function takeDamage(int $damage): void
    {
        parent::takeDamage($damage); // @c-eparent

        if ($this->health <= 0) { // @c-eif
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\\n"; // @c-eecho
        }
    }
}
`;

// de versie van Enemy zonder parent::takeDamage() voor het lab "Zoek de fout"
const LISTING_ENEMY_BUG = `class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->loot = $loot; // @c-extra
    }

    public function takeDamage(int $damage): void
    {
        if ($this->health <= 0) { // @c-eif
            echo $this->name . " is verslagen en laat " . $this->loot . " vallen\\n"; // @c-eecho
        }
    }
}
`;

// override.php: describe() vervangen (Npc) en uitbreiden (Enemy)
const LISTING_DESCRIBE = `class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name; // @c-name
        $this->health = $health; // @c-health
        $this->armorRating = $armorRating; // @c-armor
    }

    public function describe(): string
    {
        return $this->name . " (" . $this->health . " health)"; // @c-describe
    }
}

class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating); // @c-nparent
        $this->dialogue = $dialogue; // @c-nextra
    }

    public function describe(): string
    {
        return $this->name . " zegt: \\"" . $this->dialogue . "\\""; // @c-ndescribe
    }
}

class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->loot = $loot; // @c-extra
    }

    public function describe(): string
    {
        return parent::describe() . " en heeft " . $this->loot . " bij zich"; // @c-edescribe
    }
}
`;

// abstract_classes.php: abstracte Combatant met een abstracte attack()
const LISTING_ABSTRACT_COMBATANT = `abstract class Combatant
{
    protected string $name;
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name; // @c-name
        $this->health = $health; // @c-health
        $this->armorRating = $armorRating; // @c-armor
    }

    abstract public function attack(): int;
}
`;

const LISTING_ABSTRACT_PLAYER = `class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10); // @c-pattack
    }
}
`;

const LISTING_ABSTRACT_ENEMY = `class Enemy extends Combatant
{
    private int $threatLevel;

    public function __construct(string $name, int $health, int $armorRating, int $threatLevel)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->threatLevel = $threatLevel; // @c-extra
    }

    public function attack(): int
    {
        return $this->threatLevel * 4; // @c-eattack
    }
}
`;

// ---------- uitleg per onderdeel ----------

const MEMBERS_OVERRIDE = {
    override: {
        title: 'Enemy overschrijft takeDamage()',
        code: 'public function takeDamage(int $damage): void',
        text: 'Dezelfde naam en dezelfde parameters als in Combatant. Voor een Enemy wordt nu deze versie gebruikt.',
    },
    parent: {
        title: 'parent::takeDamage()',
        code: 'parent::takeDamage($damage);',
        text: 'Voert takeDamage() van Combatant uit, dus het rekenwerk met de armor hoeft niet gekopieerd te worden. Daarna voegt Enemy er zijn eigen controle aan toe.',
    },
    extra: {
        title: 'De uitbreiding',
        code: 'if ($this->health <= 0) {\n    echo $this->name . " is verslagen en laat " . $this->loot . " vallen\\n";\n}',
        text: 'Alleen een Enemy laat loot vallen. Dit is wat Enemy toevoegt aan het gedrag van Combatant.',
    },
};

const MEMBERS_DESCRIBE = {
    replace: {
        title: 'Npc vervangt describe()',
        code: 'public function describe(): string\n{\n    return $this->name . " zegt: ..." ;\n}',
        text: 'Npc gebruikt parent::describe() niet. De versie van Combatant wordt voor een Npc dus nooit uitgevoerd.',
    },
    extend: {
        title: 'Enemy breidt describe() uit',
        code: 'return parent::describe() . " en heeft " . $this->loot . " bij zich";',
        text: 'parent::describe() geeft de tekst van Combatant terug. Enemy plakt er zijn eigen tekst achter.',
    },
};

const MEMBERS_ABSTRACT = {
    abstractClass: {
        title: 'abstract class Combatant',
        code: 'abstract class Combatant',
        text: 'Van een abstracte class kun je geen object maken met new. Je kunt er alleen van erven.',
    },
    abstractMethod: {
        title: 'abstract public function attack(): int;',
        code: 'abstract public function attack(): int;',
        text: 'Een method zonder body. Elke child van Combatant moet attack() zelf schrijven, anders geeft PHP een Fatal error.',
    },
    implementations: {
        title: 'Elke child schrijft zijn eigen attack()',
        code: 'Player: 10 + intdiv($this->experiencePoints, 10)\nEnemy: $this->threatLevel * 4',
        text: 'Dezelfde naam en hetzelfde return type, maar een andere uitkomst. De parent weet dat elke Combatant kan aanvallen, de child bepaalt hoe.',
    },
};

const MEMBERS_ENEMY_BUG = {
    override: {
        title: 'Enemy overschrijft takeDamage()',
        code: 'public function takeDamage(int $damage): void\n{\n    if ($this->health <= 0) { ... }\n}',
        text: 'Een override vervangt de method van de parent. Wat Combatant::takeDamage() deed gebeurt hier alleen als je het zelf aanroept.',
    },
};

const MEMBERS_ABSTRACT_BUG = {
    abstractClass: {
        title: 'abstract class Combatant',
        code: 'abstract class Combatant',
        text: 'Combatant is abstract: een blauwdruk voor classes als Enemy, geen class om zelf een object van te maken.',
    },
    child: {
        title: 'Enemy is een echte class',
        code: 'class Enemy extends Combatant',
        text: 'Enemy schrijft de abstracte attack() zelf. Daarom kun je van Enemy wel een object maken.',
    },
};

// bouwt het paneel op in het element
function renderMembers(container, members, className) {
    container.replaceChildren();
    if (!members) {
        container.hidden = true;
        return;
    }
    container.hidden = false;

    const summary = document.createElement('summary');
    summary.textContent = `${className}: wat doen de onderdelen?`;
    container.append(summary);

    for (const member of Object.values(members)) {
        const section = document.createElement('section');
        section.className = 'member';

        const heading = document.createElement('h3');
        heading.textContent = member.title;

        const pre = document.createElement('pre');
        const code = document.createElement('code');
        code.textContent = member.code;
        pre.append(code);

        const text = document.createElement('p');
        text.textContent = member.text;

        section.append(heading, pre, text);
        container.append(section);
    }
}

// ---------- herbruikbare stappen ----------

// de properties van Combatant, nog zonder waarde, met erbij in welke class ze staan
function combatantProps(extra = {}, extraOwner = null) {
    const props = { name: null, health: null, armorRating: null, ...extra };
    const owners = { name: 'Combatant', health: 'Combatant', armorRating: 'Combatant' };
    for (const key of Object.keys(extra)) owners[key] = extraOwner;
    return [props, owners];
}

// de constructor van Combatant zet de drie gedeelde properties (de regels c-name, c-health en c-armor)
function combatantCtorBodySteps(id, name, health, armorRating, who) {
    return [
        { at: 'c-name', say: `${who} zet de property name van het object.`, ops: [['prop', id, 'name', name]] },
        { at: 'c-health', say: `${who} zet de property health.`, ops: [['prop', id, 'health', health]] },
        { at: 'c-armor', say: `${who} zet de property armorRating.`, ops: [['prop', id, 'armorRating', armorRating]] },
    ];
}

// new van een class zonder eigen constructor: PHP gebruikt die van Combatant
function inheritedCtorSteps(at, cls, id, varName, name, health, armorRating, extra = {}, extraOwner = null) {
    const [props, owners] = combatantProps(extra, extraOwner);
    return [
        {
            at,
            say: `new ${cls}(...) maakt een object met de properties van Combatant en van ${cls}. ${cls} heeft geen eigen constructor, dus PHP start die van Combatant.`,
            ops: [['new', id, cls, props, owners], ['push', 'Combatant::__construct()', { this: ref(id), name, health, armorRating }]],
        },
        ...combatantCtorBodySteps(id, name, health, armorRating, 'De constructor'),
        { at, say: `De constructor is klaar. $${varName} wijst naar het object. Alle properties hebben een waarde.`, ops: [['pop'], ['set', varName, ref(id)]] },
    ];
}

// new van een class met een eigen constructor die parent::__construct() aanroept en daarna zijn eigen property zet
// (parentLabel en extraLabel zijn de labels van de regels in de constructor van de child)
function childCtorSteps({ at, cls, id, varName, name, health, armorRating, extraProp, extraValue, parentLabel = 'c-parent', extraLabel = 'c-extra' }) {
    const [props, owners] = combatantProps({ [extraProp]: null }, cls);
    return [
        {
            at,
            say: `new ${cls}(...) maakt een object. ${cls} heeft een eigen constructor met een extra argument, dus die start. Alle properties zijn nog leeg.`,
            ops: [['new', id, cls, props, owners], ['push', `${cls}::__construct()`, { this: ref(id), name, health, armorRating, [extraProp]: extraValue }]],
        },
        {
            at: parentLabel,
            say: 'parent::__construct() start de constructor van Combatant, met de drie argumenten die Combatant nodig heeft.',
            ops: [['push', 'Combatant::__construct()', { this: ref(id), name, health, armorRating }]],
        },
        ...combatantCtorBodySteps(id, name, health, armorRating, 'De constructor van Combatant'),
        { at: parentLabel, say: `De constructor van Combatant is klaar en zijn frame verdwijnt. We zijn terug in ${cls}::__construct().`, ops: [['pop']] },
        { at: extraLabel, say: `Nu zet ${cls} zijn eigen property ${extraProp}.`, ops: [['prop', id, extraProp, extraValue]] },
        { at, say: `${cls}::__construct() is klaar. $${varName} wijst naar het object.`, ops: [['pop'], ['set', varName, ref(id)]] },
    ];
}

// een method aanroepen die een waarde teruggeeft, voor het tonen
function getterSteps(at, label, frame, id, value, echoText) {
    return [
        { at, say: `${frame} wordt aangeroepen. $this is het object.`, ops: [['push', frame, { this: ref(id) }]] },
        { at: label, say: `De method geeft ${JSON.stringify(value)} terug.`, ops: [['ret', value]] },
        { at, say: 'Het frame verdwijnt en het script toont de waarde.', ops: [['pop'], ['echo', echoText]] },
    ];
}
