// Gedeelde onderdelen voor de labs over inheritance: de code van de classes, uitleg per onderdeel en herbruikbare stappen.
// De classes zijn ingekort ten opzichte van de voorbeeldbestanden in example/inheritance, maar werken hetzelfde.
// Labels beginnen met c- om niet te botsen met die van een scenario.

// ---------- de classes als code in het programma ----------

// extends_basics.php: Combatant met een protected property, een constructor en twee methods
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

const LISTING_PLAYER = `class Player extends Combatant
{
    private int $experiencePoints = 0;

    public function levelUp(): void
    {
        $this->experiencePoints = $this->experiencePoints + 100; // @c-lvl
    }
}
`;

// parent_constructor.php: Combatant met alleen een constructor en een getter, Npc met een eigen constructor
const LISTING_COMBATANT_CTOR = `class Combatant
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

    public function getName(): string
    {
        return $this->name; // @c-get
    }
}
`;

const LISTING_NPC = `class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->dialogue = $dialogue; // @c-extra
    }

    public function talk(): string
    {
        return $this->dialogue; // @c-talk
    }
}
`;

// de versie van Npc zonder parent::__construct() voor het lab "Zoek de fout"
const LISTING_NPC_BUG = `class Npc extends Combatant
{
    private string $dialogue;

    public function __construct(string $name, int $health, int $armorRating, string $dialogue)
    {
        $this->dialogue = $dialogue; // @c-extra
    }
}
`;

// override.php: Enemy breidt takeDamage() uit met parent::
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

// hierarchy.php: vier lagen
const LISTING_TREE = `class Entity
{
    protected string $name;
    protected int $rank;

    public function __construct(string $name, int $rank)
    {
        $this->name = $name; // @c-name
        $this->rank = $rank; // @c-rank
    }

    public function getName(): string
    {
        return $this->name; // @c-get
    }
}

class Participant extends Entity
{
    protected int $dice;

    public function __construct(string $name, int $rank, int $dice)
    {
        parent::__construct($name, $rank); // @c-pparent
        $this->dice = $dice; // @c-dice
    }
}

class Combatant extends Participant
{
    protected int $health;
    protected int $armorRating;

    public function __construct(string $name, int $rank, int $dice, int $health, int $armorRating)
    {
        parent::__construct($name, $rank, $dice); // @c-cparent
        $this->health = $health; // @c-health
        $this->armorRating = $armorRating; // @c-armor
    }
}

class Player extends Combatant
{
    private int $experiencePoints = 0;
}
`;

// de versie van Combatant met private properties voor het lab "Zoek de fout"
const LISTING_PRIVATE_COMBATANT = `class Combatant
{
    private string $name;
    private int $health;
    private int $armorRating;

    public function __construct(string $name, int $health, int $armorRating)
    {
        $this->name = $name; // @c-name
        $this->health = $health; // @c-health
        $this->armorRating = $armorRating; // @c-armor
    }
}

class Player extends Combatant
{
    public function describe(): string
    {
        return "Health: " . $this->health; // @c-describe
    }
}
`;

const LISTING_PROTECTED_COMBATANT = `class Combatant
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
}

class Player extends Combatant
{
    public function describe(): string
    {
        return "Health: " . $this->health; // @c-describe
    }
}
`;

// ---------- uitleg per onderdeel ----------

const MEMBERS_EXTENDS = {
    extends: {
        title: 'extends: Player is een Combatant',
        code: 'class Player extends Combatant',
        text: 'Player krijgt alles wat Combatant heeft: de properties, de constructor en de methods. Je ziet het aan het object: de properties uit Combatant staan erin, met erachter uit welke class ze komen.',
    },
    protected: {
        title: 'protected: de buitenwereld niet, de child wel',
        code: 'protected string $name;\nprotected int $health;\nprotected int $armorRating;',
        text: 'Van buiten de class kun je er niet bij, net als bij private. Maar de classes die van Combatant erven kunnen er wel bij. Bij private zou Player er niet bij kunnen.',
    },
    constructor: {
        title: 'De constructor staat in Combatant',
        code: 'public function __construct(string $name, int $health, int $armorRating)',
        text: 'Player heeft geen eigen constructor, dus PHP gebruikt die van Combatant. Je ziet dat in het frame op de stack: Combatant::__construct().',
    },
    own: {
        title: 'Wat Player zelf heeft',
        code: 'private int $experiencePoints = 0;\n\npublic function levelUp(): void',
        text: 'Alleen wat niet in Combatant staat. Deze property en method bestaan niet voor andere Combatants.',
    },
};

const MEMBERS_PARENT_CTOR = {
    own: {
        title: 'Npc heeft een eigen constructor',
        code: 'public function __construct(string $name, int $health, int $armorRating, string $dialogue)',
        text: 'Npc heeft een extra argument nodig: de dialoog. Een eigen constructor vervangt die van de parent. PHP start dus Npc::__construct() en niet Combatant::__construct().',
    },
    parent: {
        title: 'parent::__construct()',
        code: 'parent::__construct($name, $health, $armorRating);',
        text: 'Roept de constructor van Combatant aan, zodat name, health en armorRating gezet worden. Zonder deze regel blijven ze leeg. In Python is dit super().__init__().',
    },
    extra: {
        title: 'Daarna wat Npc zelf erbij heeft',
        code: '$this->dialogue = $dialogue;',
        text: 'De property dialogue staat alleen in Npc, dus Npc zet hem zelf.',
    },
};

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
        code: 'if ($this->health <= 0) {\n    echo $this->name . " is verslagen en laat " . $this->loot . " vallen";\n}',
        text: 'Alleen een Enemy laat loot vallen. Dit is wat Enemy toevoegt aan het gedrag van Combatant.',
    },
};

const MEMBERS_TREE = {
    chain: {
        title: 'Elke class roept zijn parent aan',
        code: 'parent::__construct($name, $rank, $dice);',
        text: 'Player heeft geen constructor, dus begint het bij Combatant. Combatant roept Participant aan, en Participant roept Entity aan. Op de stack zie je de frames op elkaar komen. De bovenste draait, tot Entity klaar is.',
    },
    owners: {
        title: 'Elke property hoort bij één class',
        code: 'Entity: name, rank\nParticipant: dice\nCombatant: health, armorRating\nPlayer: experiencePoints',
        text: 'In het object staan ze allemaal naast elkaar. Achter elke property zie je in welke class hij is gedeclareerd.',
    },
};

const MEMBERS_NPC_BUG = {
    own: {
        title: 'Npc zet alleen zijn eigen property',
        code: 'public function __construct(string $name, int $health, int $armorRating, string $dialogue)\n{\n    $this->dialogue = $dialogue;\n}',
        text: 'Npc heeft een eigen constructor en die vervangt die van Combatant. Wat Combatant zet (name, health en armorRating) gebeurt hier niet meer vanzelf.',
    },
};

const MEMBERS_PRIVATE_BUG = {
    property: {
        title: 'private in de parent',
        code: 'private int $health;',
        text: 'Door private kan alleen code in Combatant zelf bij health. Player erft de property wel (hij zit in het object), maar kan er niet bij.',
    },
};

const MEMBERS_ENEMY_BUG = {
    override: {
        title: 'Enemy overschrijft takeDamage()',
        code: 'public function takeDamage(int $damage): void\n{\n    if ($this->health <= 0) { ... }\n}',
        text: 'Een override vervangt de method van de parent. Wat Combatant::takeDamage() deed gebeurt hier alleen als je het zelf aanroept.',
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
function childCtorSteps({ at, cls, id, varName, name, health, armorRating, extraProp, extraValue }) {
    const [props, owners] = combatantProps({ [extraProp]: null }, cls);
    return [
        {
            at,
            say: `new ${cls}(...) maakt een object. ${cls} heeft een eigen constructor met een extra argument, dus die start. Alle properties zijn nog leeg.`,
            ops: [['new', id, cls, props, owners], ['push', `${cls}::__construct()`, { this: ref(id), name, health, armorRating, [extraProp]: extraValue }]],
        },
        {
            at: 'c-parent',
            say: 'parent::__construct() start de constructor van Combatant, met de drie argumenten die Combatant nodig heeft.',
            ops: [['push', 'Combatant::__construct()', { this: ref(id), name, health, armorRating }]],
        },
        ...combatantCtorBodySteps(id, name, health, armorRating, 'De constructor van Combatant'),
        { at: 'c-parent', say: `De constructor van Combatant is klaar en zijn frame verdwijnt. We zijn terug in ${cls}::__construct().`, ops: [['pop']] },
        { at: 'c-extra', say: `Nu zet ${cls} zijn eigen property ${extraProp}.`, ops: [['prop', id, extraProp, extraValue]] },
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
