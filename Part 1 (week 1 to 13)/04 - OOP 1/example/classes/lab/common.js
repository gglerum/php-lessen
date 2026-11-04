// Gedeelde onderdelen voor de labs over classes: de code van de classes, uitleg per onderdeel en herbruikbare stappen.
// De classes hier zijn dezelfde als in de voorbeeldbestanden, zodat je altijd kunt zien wat een onderdeel doet.

// ---------- de classes als code in het programma (labels beginnen met c- om niet te botsen met die van een scenario) ----------

// class_basics.php: een class met een public property en een method
const LISTING_BASIC = `class BankAccount
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount; // @c-dep
    }
}
`;

// constructor.php: met een constructor
const LISTING_CTOR = `class BankAccount
{
    public string $owner;
    public int $balance;

    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner; // @c-owner
        $this->balance = $balance; // @c-balance
    }

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount; // @c-dep
    }
}
`;

// de rekening met een public saldo en een controle in withdraw()
const LISTING_PUBLIC = `class BankAccount
{
    public int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance; // @c-ctor
    }

    public function withdraw(int $amount): void
    {
        if ($amount > $this->balance) { // @c-if
            echo "Onvoldoende saldo\\n"; // @c-echo
            return; // @c-return
        }

        $this->balance = $this->balance - $amount; // @c-sub
    }
}
`;

// access_modifiers.php: het saldo is private en er is een getter
const LISTING_PRIVATE = `class BankAccount
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance; // @c-ctor
    }

    public function withdraw(int $amount): void
    {
        if ($amount > $this->balance) { // @c-if
            echo "Onvoldoende saldo\\n"; // @c-echo
            return; // @c-return
        }

        $this->balance = $this->balance - $amount; // @c-sub
    }

    public function getBalance(): int
    {
        return $this->balance; // @c-get
    }
}
`;

// de versies voor het lab "Zoek de fout"
const LISTING_BASIC_BUG = `class BankAccount
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $balance = $balance + $amount; // @c-dep
    }
}
`;

const LISTING_CTOR_BUG = `class BankAccount
{
    public int $balance;

    public function __construct(int $balance)
    {
        $balance = $balance; // @c-ctor
    }
}
`;

const LISTING_CTOR_FIXED = `class BankAccount
{
    public int $balance;

    public function __construct(int $balance)
    {
        $this->balance = $balance; // @c-ctor
    }
}
`;

// ---------- uitleg per onderdeel ----------

const MEMBERS_CTOR_BUG = {
    property: {
        title: 'Property: $balance',
        code: 'public int $balance;',
        text: 'Het saldo zit in het object zelf. Er is geen standaardwaarde: de property krijgt pas een waarde als de constructor hem zet.',
    },
    constructor: {
        title: 'Constructor: __construct()',
        code: 'public function __construct(int $balance)\n{\n    // hier moet de property worden gezet\n}',
        text: 'Wordt automatisch aangeroepen bij new. De parameter $balance is een gewone variabele binnen de constructor. Om de property van het object te vullen heb je $this-> nodig.',
    },
};

const MEMBERS_BASIC = {
    property: {
        title: 'Property: $balance',
        code: 'public int $balance = 0;',
        text: 'Het saldo zit in het object zelf. Elk BankAccount object heeft zijn eigen $balance. Een nieuw object begint met de standaardwaarde 0. Door public kan iedereen er bij.',
    },
    deposit: {
        title: 'Method: deposit()',
        code: 'public function deposit(int $amount): void\n{\n    $this->balance = $this->balance + $amount;\n}',
        text: 'Telt $amount op bij het saldo van dit object. $this is het object waarop je de method aanroept: bij $account->deposit(50) is $this dus $account.',
    },
};

const MEMBERS_CTOR = {
    properties: {
        title: 'Properties: $owner en $balance',
        code: 'public string $owner;\npublic int $balance;',
        text: 'De naam van de eigenaar en het saldo zitten in het object zelf. Elk object heeft zijn eigen waarden. Ze hebben geen standaardwaarde: ze krijgen hun waarde pas in de constructor.',
    },
    constructor: {
        title: 'Constructor: __construct()',
        code: 'public function __construct(string $owner, int $balance = 0)\n{\n    $this->owner = $owner;\n    $this->balance = $balance;\n}',
        text: 'Wordt automatisch aangeroepen bij new. De argumenten bij new BankAccount("John", 100) komen binnen als parameters. De constructor zet ze in de properties van het nieuwe object. Geef je geen saldo mee, dan is het 0.',
    },
    deposit: MEMBERS_BASIC.deposit,
};

const MEMBERS_PUBLIC = {
    property: {
        title: 'Property: $balance',
        code: 'public int $balance;',
        text: 'Het saldo zit in het object zelf. Omdat de property public is, kan iedereen het saldo rechtstreeks lezen en aanpassen, ook buiten de class.',
    },
    constructor: {
        title: 'Constructor: __construct()',
        code: 'public function __construct(int $balance = 0)\n{\n    $this->balance = $balance;\n}',
        text: 'Wordt automatisch aangeroepen bij new. Zet het beginsaldo van het nieuwe object. Geef je niets mee, dan is het saldo 0.',
    },
    withdraw: {
        title: 'Method: withdraw()',
        code: 'public function withdraw(int $amount): void\n{\n    if ($amount > $this->balance) {\n        echo "Onvoldoende saldo\\n";\n        return;\n    }\n\n    $this->balance = $this->balance - $amount;\n}',
        text: 'Haalt $amount van het saldo af, maar alleen als er genoeg saldo is. Is dat niet zo, dan toont de method een melding en stopt met return. Dit is de controle die beschermt tegen een negatief saldo.',
    },
};

const MEMBERS_PRIVATE = {
    property: {
        title: 'Property: $balance',
        code: 'private int $balance;',
        text: 'Het saldo zit in het object zelf. Door private kan alleen code binnen de class bij het saldo. Van buiten kun je het niet lezen of aanpassen.',
    },
    constructor: MEMBERS_PUBLIC.constructor,
    withdraw: MEMBERS_PUBLIC.withdraw,
    getBalance: {
        title: 'Method: getBalance()',
        code: 'public function getBalance(): int\n{\n    return $this->balance;\n}',
        text: 'Geeft het saldo van dit object terug. Omdat $balance private is, is dit de manier om het saldo van buiten de class te lezen. Zo\'n method heet een getter.',
    },
};

// bouwt het paneel op in het element
function renderMembers(container, members, className = 'BankAccount') {
    container.replaceChildren();
    if (!members) {
        container.hidden = true;
        return;
    }
    container.hidden = false;

    const summary = document.createElement('summary');
    summary.textContent = `De class ${className}: wat doen de onderdelen?`;
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

// de constructor van de versie met alleen een saldo
function ctorSteps(at, varName, balance, id) {
    return [
        {
            at,
            say: `new BankAccount(${balance}) maakt een nieuw object en start de constructor. $this is het nieuwe object, de parameter $balance is ${balance}.`,
            ops: [['new', id, 'BankAccount', { balance: null }], ['push', '__construct()', { this: ref(id), balance }]],
        },
        { at: 'c-ctor', say: `De constructor zet de property balance van het object op ${balance}.`, ops: [['prop', id, 'balance', balance]] },
        { at, say: `De constructor is klaar en zijn frame verdwijnt. Het object blijft bestaan en $${varName} wijst ernaar.`, ops: [['pop'], ['set', varName, ref(id)]] },
    ];
}

function depositSteps(at, id, amount, newBalance, who) {
    return [
        {
            at,
            say: `deposit(${amount}) wordt aangeroepen op ${who}. $this is dat object, $amount is ${amount}.`,
            ops: [['push', 'deposit()', { this: ref(id), amount }]],
        },
        { at: 'c-dep', say: `balance wordt ${newBalance - amount} + ${amount} = ${newBalance}. Alleen dit object verandert.`, ops: [['prop', id, 'balance', newBalance]] },
        { at, say: 'deposit() is klaar en zijn frame verdwijnt. De wijziging blijft bestaan in het object.', ops: [['pop']] },
    ];
}

// withdraw met te weinig saldo: de controle slaat aan
function withdrawRefusedSteps(at, id, amount, balance) {
    return [
        { at, say: `withdraw(${amount}) wordt aangeroepen. $this is het object, $amount is ${amount}.`, ops: [['push', 'withdraw()', { this: ref(id), amount }]] },
        { at: 'c-if', say: `Is ${amount} > ${balance}? Ja: er is te weinig saldo.`, ops: [] },
        { at: 'c-echo', say: 'De method toont een melding.', ops: [['echo', 'Onvoldoende saldo']] },
        { at: 'c-return', say: 'return stopt de method. Het saldo wordt niet aangepast.', ops: [] },
        { at, say: 'Het frame van withdraw() verdwijnt. Het saldo van het object is ongewijzigd.', ops: [['pop']] },
    ];
}
