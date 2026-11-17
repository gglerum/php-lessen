// Gedeelde onderdelen voor de labs over polymorphism: de code van de classes, uitleg per onderdeel en herbruikbare stappen.
// De classes zijn ingekort ten opzichte van de voorbeeldbestanden in example/polymorphism, maar werken hetzelfde.
// Labels beginnen met c-, a-, f- of s- om niet te botsen met die van een scenario.
// De voorbeelden en de listings komen uit een generator (scratch) en worden gecontroleerd met verify_lab1.php tot en met verify_lab3.php.

// ---------- de classes en functies als code in het programma ----------

// abstracte Combatant met Player en Enemy
const LISTING_ABSTRACT = `abstract class Combatant
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health; // @c-get
    }

    abstract public function attack(): int;
}

class Player extends Combatant
{
    private int $experiencePoints = 100;

    public function attack(): int
    {
        return 10 + intdiv($this->experiencePoints, 10); // @c-pattack
    }
}

class Enemy extends Combatant
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

// een functie met een Combatant als parameter
const LISTING_ANNOUNCE = `function announce(Combatant $combatant): void
{
    echo $combatant->getName() . " valt aan voor " . $combatant->attack() . " schade\\n"; // @a-echo
}
`;

// twee Combatants als parameter
const LISTING_FIGHT = `function fight(Combatant $attacker, Combatant $target): void
{
    $target->takeDamage($attacker->attack()); // @f-hit
}
`;

// Combatant (niet abstract), Player en Enemy met loot
const LISTING_SHOW_CLASSES = `class Combatant
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
        return $this->name;
    }
}

class Player extends Combatant
{
}

class Enemy extends Combatant
{
    private string $loot;

    public function __construct(string $name, int $health, int $armorRating, string $loot)
    {
        parent::__construct($name, $health, $armorRating); // @c-parent
        $this->loot = $loot; // @c-extra
    }

    public function getLoot(): string
    {
        return $this->loot; // @c-getloot
    }
}
`;

// showLoot() met instanceof
const LISTING_SHOWLOOT = `function showLoot(Combatant $combatant): void
{
    if ($combatant instanceof Enemy) { // @s-if
        echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\\n"; // @s-yes
    } else {
        echo $combatant->getName() . " heeft niets bij zich\\n"; // @s-no
    }
}
`;

// showLoot() zonder controle
const LISTING_SHOWLOOT_BUG = `function showLoot(Combatant $combatant): void
{
    echo $combatant->getName() . " heeft " . $combatant->getLoot() . "\\n"; // @s-echo
}
`;

// een class die geen Combatant is
const LISTING_POTION = `class Potion
{
}
`;

// ---------- uitleg per onderdeel ----------

const MEMBERS_ANNOUNCE = {
    paramType: {
        title: 'Combatant $combatant',
        code: 'function announce(Combatant $combatant): void',
        text: 'Het type van de parameter is de parent. Elk object van een class die van Combatant erft past erin: een Player, een Enemy en later ook een class die nog niet bestaat.',
    },
    attack: {
        title: '$combatant->attack()',
        code: '$combatant->attack()',
        text: 'PHP kijkt bij het uitvoeren naar het object zelf, niet naar het type dat bij de parameter staat. Bij een Player draait Player::attack() en bij een Enemy Enemy::attack().',
    },
    abstractMethod: {
        title: 'abstract public function attack(): int;',
        code: 'abstract public function attack(): int;',
        text: 'Omdat attack() abstract is, weet PHP zeker dat elke Combatant hem heeft. Daarom mag announce() hem altijd aanroepen.',
    },
};

const MEMBERS_FIGHT = {
    params: {
        title: 'fight(Combatant $attacker, Combatant $target)',
        code: 'function fight(Combatant $attacker, Combatant $target): void',
        text: 'Beide parameters hebben het type van de parent. Daardoor werkt dezelfde functie voor elke combinatie: een Enemy tegen een Player, een Player tegen een Enemy of twee Players.',
    },
    hit: {
        title: '$target->takeDamage($attacker->attack())',
        code: '$target->takeDamage($attacker->attack());',
        text: 'De aanvaller bepaalt met zijn eigen attack() hoeveel schade er is. takeDamage() staat in Combatant en rekent voor iedereen hetzelfde.',
    },
};

const MEMBERS_INSTANCEOF = {
    instanceofCheck: {
        title: '$combatant instanceof Enemy',
        code: 'if ($combatant instanceof Enemy) { ... }',
        text: 'Geeft true als het object een Enemy is (of van een class die van Enemy erft) en anders false. Het geeft nooit een fout.',
    },
    getLoot: {
        title: 'getLoot() staat alleen in Enemy',
        code: 'public function getLoot(): string',
        text: 'Een Player heeft deze method niet. Door eerst met instanceof te controleren voorkom je dat je hem op een Player aanroept.',
    },
};

const MEMBERS_TYPEERROR = {
    potion: {
        title: 'class Potion',
        code: 'class Potion\n{\n}',
        text: 'Een Potion erft niet van Combatant, dus het is geen Combatant. Het type bij de parameter laat hem niet toe.',
    },
    paramType: {
        title: 'Combatant $combatant',
        code: 'function announce(Combatant $combatant): void',
        text: 'PHP controleert dit type zodra je de functie aanroept. Past het object niet, dan geeft PHP een TypeError en wordt de body van de functie niet uitgevoerd.',
    },
};

const MEMBERS_NOLOOT = {
    getLoot: {
        title: 'getLoot() in showLoot()',
        code: 'echo $combatant->getLoot();',
        text: 'showLoot() weet alleen dat $combatant een Combatant is. getLoot() staat alleen in Enemy: bij een Player geeft PHP een Error.',
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
            say: `new ${cls}(...) maakt een object met de properties van Combatant${Object.keys(extra).length ? ' en van ' + cls : ''}. ${cls} heeft geen eigen constructor, dus PHP start die van Combatant.`,
            ops: [['new', id, cls, props, owners], ['push', 'Combatant::__construct()', { this: ref(id), name, health, armorRating }]],
        },
        ...combatantCtorBodySteps(id, name, health, armorRating, 'De constructor'),
        { at, say: `De constructor is klaar. $${varName} wijst naar het object. Alle properties hebben een waarde.`, ops: [['pop'], ['set', varName, ref(id)]] },
    ];
}

// new van een class met een eigen constructor die parent::__construct() aanroept en daarna zijn eigen property zet
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

// announce($x): de parameter wijst naar het object en attack() van dat object draait
// (at = regel van de aanroep in het script, cls = echte class van het object, attackLabel = regel van attack() in die class)
function announceSteps({ at, id, cls, name, damage, attackLabel }) {
    return [
        { at, say: `announce() krijgt het object mee. De parameter $combatant heeft het type Combatant, maar het object is een ${cls}: een ${cls} is ook een Combatant, dus dit mag.`, ops: [['push', 'announce()', { combatant: ref(id) }]] },
        { at: 'a-echo', say: `De functie roept $combatant->attack() aan. Hij weet niet dat het een ${cls} is, maar PHP kijkt naar het object zelf en gebruikt ${cls}::attack().`, ops: [['push', `${cls}::attack()`, { this: ref(id) }]] },
        { at: attackLabel, say: `${cls}::attack() geeft ${damage} terug.`, ops: [['ret', damage]] },
        { at: 'a-echo', say: `Het frame verdwijnt en announce() toont de tekst met de naam en ${damage}.`, ops: [['pop'], ['echo', `${name} valt aan voor ${damage} schade`]] },
        { at, say: 'announce() is klaar en zijn frame verdwijnt.', ops: [['pop']] },
    ];
}

// showLoot($x) met instanceof
function showLootSteps({ at, id, cls, name, loot }) {
    if (cls === 'Enemy') {
        return [
            { at, say: `showLoot() krijgt het object mee. De parameter heeft het type Combatant, het object is een ${cls}.`, ops: [['push', 'showLoot()', { combatant: ref(id) }]] },
            { at: 's-if', say: `$combatant instanceof Enemy: het object is een Enemy, dus true.`, ops: [] },
            { at: 's-yes', say: `Omdat het een Enemy is, mag getLoot() worden aangeroepen.`, ops: [['push', 'Enemy::getLoot()', { this: ref(id) }]] },
            { at: 'c-getloot', say: `getLoot() geeft ${JSON.stringify(loot)} terug.`, ops: [['ret', loot]] },
            { at: 's-yes', say: 'Het frame verdwijnt en showLoot() toont de tekst.', ops: [['pop'], ['echo', `${name} heeft ${loot}`]] },
            { at, say: 'showLoot() is klaar en zijn frame verdwijnt.', ops: [['pop']] },
        ];
    }
    return [
        { at, say: `showLoot() krijgt het object mee. De parameter heeft het type Combatant, het object is een ${cls}.`, ops: [['push', 'showLoot()', { combatant: ref(id) }]] },
        { at: 's-if', say: `$combatant instanceof Enemy: een Player is geen Enemy (het zijn broers), dus false. De regel met getLoot() wordt overgeslagen.`, ops: [] },
        { at: 's-no', say: 'De else neemt het over en toont een andere tekst.', ops: [['echo', `${name} heeft niets bij zich`]] },
        { at, say: 'showLoot() is klaar en zijn frame verdwijnt.', ops: [['pop']] },
    ];
}
