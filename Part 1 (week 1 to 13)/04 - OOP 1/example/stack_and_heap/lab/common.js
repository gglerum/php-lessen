// Gedeelde onderdelen voor de labs: de code van BankAccount met uitleg per onderdeel.
// De code hier is dezelfde als in de voorbeeldbestanden, zodat de student altijd kan zien wat een method doet.

const BANK_ACCOUNT_MEMBERS = {
    property: {
        title: 'Property: $balance',
        code: 'private int $balance;',
        text: 'Het saldo zit in het object zelf. Elk BankAccount object heeft zijn eigen $balance. Door private kan alleen code binnen de class erbij.',
    },
    constructor: {
        title: 'Constructor: __construct()',
        code: 'public function __construct(int $balance = 0)\n{\n    $this->balance = $balance;\n}',
        text: 'Wordt automatisch aangeroepen bij new. new BankAccount(100) maakt een object en zet het beginsaldo op 100. Geef je niets mee, dan is het saldo 0.',
    },
    deposit: {
        title: 'Method: deposit()',
        code: 'public function deposit(int $amount): void\n{\n    $this->balance = $this->balance + $amount;\n}',
        text: 'Telt $amount op bij het saldo van dit object. $this is het object waarop je de method aanroept: bij $account->deposit(50) is $this dus $account.',
    },
    getBalance: {
        title: 'Method: getBalance()',
        code: 'public function getBalance(): int\n{\n    return $this->balance;\n}',
        text: 'Geeft het saldo van dit object terug. Omdat $balance private is, is dit de manier om het saldo van buiten de class te lezen.',
    },
};


// de BankAccount uit het levensduur-lab: met een owner en een destructor
const LIFETIME_ACCOUNT_MEMBERS = {
    property: {
        title: 'Property: $owner',
        code: 'private string $owner;',
        text: 'De naam van de eigenaar zit in het object zelf. Elk BankAccount object heeft zijn eigen $owner.',
    },
    constructor: {
        title: 'Constructor: __construct()',
        code: 'public function __construct(string $owner)\n{\n    $this->owner = $owner;\n    echo "rekening van $owner aangemaakt";\n}',
        text: 'Wordt automatisch aangeroepen bij new. Bewaart de eigenaar in het object en toont een regel, zodat je ziet wanneer het object ontstaat.',
    },
    destructor: {
        title: 'Destructor: __destruct()',
        code: 'public function __destruct()\n{\n    echo "rekening van {$this->owner} opgeruimd";\n}',
        text: 'De tegenhanger van de constructor. PHP roept hem automatisch aan vlak voordat het object uit het geheugen verdwijnt: als er niets meer naar het object wijst, of aan het einde van het script.',
    },
};

// de teller uit het request-lab
const VISIT_COUNTER_MEMBERS = {
    property: {
        title: 'Property: $count',
        code: 'private int $count = 0;',
        text: 'De teller zit in het object en begint bij 0. Elk nieuw VisitCounter object begint opnieuw bij 0.',
    },
    increment: {
        title: 'Method: increment()',
        code: 'public function increment(): void\n{\n    $this->count = $this->count + 1;\n}',
        text: 'Verhoogt de teller van dit object met 1.',
    },
    getCount: {
        title: 'Method: getCount()',
        code: 'public function getCount(): int\n{\n    return $this->count;\n}',
        text: 'Geeft de huidige waarde van de teller terug.',
    },
};

// bouwt het paneel op in het element, en markeert de onderdelen die dit scenario gebruikt
function renderClassPanel(container, usedKeys, members = BANK_ACCOUNT_MEMBERS, className = 'BankAccount') {
    container.replaceChildren();

    const summary = document.createElement('summary');
    summary.textContent = `De class ${className}: wat doen de onderdelen?`;
    container.append(summary);

    for (const [key, member] of Object.entries(members)) {
        const used = usedKeys.includes(key);

        const section = document.createElement('section');
        section.className = 'member' + (used ? ' used' : '');

        const heading = document.createElement('h3');
        heading.textContent = member.title;
        if (used) {
            const badge = document.createElement('span');
            badge.className = 'badge';
            badge.textContent = 'Gebruikt in dit scenario';
            heading.append(badge);
        }

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
