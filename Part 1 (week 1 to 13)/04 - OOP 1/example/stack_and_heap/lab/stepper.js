// Engine voor de "step debugger" labs.
// Een scenario bestaat uit een programma (met labels achter de regels, zoals "// @call") en een lijst stappen.
// Elke stap noemt de regel die wordt uitgevoerd en wat die regel doet met de stack, de heap, de uitvoer en de opslag.
// De engine rekent uit hoe alles er na elke stap uitziet, zodat de student ook terug kan stappen.
//
// Operaties in een stap (ops):
//   ['push', 'naam()', {param: waarde}]   nieuw frame bovenop de stack
//   ['pop']                               bovenste frame weg
//   ['set', 'variabele', waarde]          variabele in het bovenste frame
//   ['new', 'o1', 'Klasse', {prop: waarde}]   object op de heap
//   ['prop', 'o1', 'prop', waarde]        property van een object aanpassen
//   ['clone', 'o2', 'o1']                 kopie van een object
//   ['ret', waarde]                       return waarde van het bovenste frame
//   ['echo', 'tekst']                     regel in de uitvoer
//   ['store', 'sleutel', waarde]          opslag buiten het script (zoals $_SESSION)
//   ['gc']                                ruim objecten op waar niets meer naar wijst
// Na elke stap ruimt de engine automatisch onbereikbare objecten op, tenzij de stap `keepGarbage: true` heeft.
// Dat is handig om te laten zien dat een object nog bestaat terwijl de destructor draait.

const SVG_NS = 'http://www.w3.org/2000/svg';

// verwijzing naar een object op de heap en de waarde null
const ref = (id) => ({ ref: id });
const NULL = { isNull: true };

// de class BankAccount zoals die in het programma staat. Labels beginnen met c- om niet te botsen met de labels van een scenario.
const BANK_ACCOUNT_LISTING = `class BankAccount
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance; // @c-ctor-body
    }

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount; // @c-dep-body
    }

    public function getBalance(): int
    {
        return $this->balance; // @c-get-body
    }
}
`;

// herbruikbare stappen: dezelfde handeling komt in meerdere scenario's voor
function ctorSteps(at, varName, balance, id, where, extra = '') {
    return [
        {
            at,
            say: `new BankAccount(${balance}) maakt een object op de heap en start de constructor. $this wijst naar het nieuwe object, de parameter $balance is ${balance}.`,
            ops: [['new', id, 'BankAccount', { balance: null }], ['push', '__construct()', { this: ref(id), balance }]],
        },
        {
            at: 'c-ctor-body',
            say: `De constructor zet de property balance van het object op ${balance}.`,
            ops: [['prop', id, 'balance', balance]],
        },
        {
            at,
            say: `De constructor is klaar en zijn frame verdwijnt. Het object blijft op de heap en $${varName} in ${where} wijst ernaar. ${extra}`.trim(),
            ops: [['pop'], ['set', varName, ref(id)]],
        },
    ];
}

function depositSteps(at, id, amount, newBalance) {
    return [
        {
            at,
            say: `deposit(${amount}) wordt aangeroepen op het object. $this is dat object, $amount is ${amount}.`,
            ops: [['push', 'deposit()', { this: ref(id), amount }]],
        },
        {
            at: 'c-dep-body',
            say: `balance wordt ${newBalance - amount} + ${amount} = ${newBalance}. Dit verandert het object zelf, op de heap.`,
            ops: [['prop', id, 'balance', newBalance]],
        },
        {
            at,
            say: 'deposit() is klaar en zijn frame is weg. De wijziging in het object blijft bestaan.',
            ops: [['pop']],
        },
    ];
}

// prefix is de tekst voor de waarde in de uitvoer, bijvoorbeeld "Saldo: "
function getBalanceSteps(at, id, value, prefix = '') {
    return [
        {
            at: 'c-get-body',
            say: `getBalance() leest de property van het object en geeft ${value} terug.`,
            ops: [['push', 'getBalance()', { this: ref(id) }], ['ret', value]],
        },
        {
            at,
            say: `Het frame van getBalance() is weg en het script toont ${prefix}${value}.`,
            ops: [['pop'], ['echo', `${prefix}${value}`]],
        },
    ];
}

// ---------- engine ----------

const deepCopy = (value) => JSON.parse(JSON.stringify(value));
const isRef = (value) => value !== null && typeof value === 'object' && typeof value.ref === 'string';

// een object zonder verwijzingen is onbereikbaar en wordt door PHP opgeruimd
function collectGarbage(state) {
    const reachable = new Set();
    for (const frame of state.frames) {
        for (const variable of frame.vars) if (isRef(variable.value)) reachable.add(variable.value.ref);
    }
    for (const id of Object.keys(state.heap)) {
        if (!reachable.has(id)) delete state.heap[id];
    }
}

function emptyChanged() {
    return { frames: new Set(), vars: new Set(), objs: new Set(), props: new Set(), output: false, storage: new Set() };
}

function applyOps(state, ops, changed) {
    for (const [op, ...args] of ops) {
        const topIndex = state.frames.length - 1;
        const top = state.frames[topIndex];

        if (op === 'push') {
            const [name, params] = args;
            const vars = Object.entries(params).map(([varName, value]) => ({ name: varName, value }));
            state.frames.push({ name, vars, ret: undefined });
            changed.frames.add(state.frames.length - 1);
            vars.forEach((variable) => changed.vars.add(`${state.frames.length - 1}.${variable.name}`));
        } else if (op === 'pop') {
            state.frames.pop();
        } else if (op === 'set') {
            const [varName, value] = args;
            const existing = top.vars.find((variable) => variable.name === varName);
            if (existing) existing.value = value;
            else top.vars.push({ name: varName, value });
            changed.vars.add(`${topIndex}.${varName}`);
        } else if (op === 'new') {
            const [id, cls, props] = args;
            state.heap[id] = { cls, props: { ...props } };
            changed.objs.add(id);
        } else if (op === 'prop') {
            const [id, name, value] = args;
            state.heap[id].props[name] = value;
            changed.props.add(`${id}.${name}`);
        } else if (op === 'clone') {
            const [newId, sourceId] = args;
            state.heap[newId] = { cls: state.heap[sourceId].cls, props: { ...state.heap[sourceId].props } };
            changed.objs.add(newId);
        } else if (op === 'ret') {
            top.ret = args[0];
            changed.vars.add(`${topIndex}.ret`);
        } else if (op === 'echo') {
            state.output.push(String(args[0]));
            changed.output = true;
        } else if (op === 'store') {
            const [key, value] = args;
            state.storage[key] = value;
            changed.storage.add(key);
        } else if (op === 'gc') {
            collectGarbage(state);
        } else {
            throw new Error('Onbekende operatie: ' + op);
        }
    }
}

const snapshot = (state) => ({
    frames: deepCopy(state.frames),
    heap: deepCopy(state.heap),
    output: [...state.output],
    storage: deepCopy(state.storage),
});

// geeft per stap de toestand, zodat vooruit en achteruit stappen even makkelijk zijn
function buildStates(scenario, labels) {
    const states = [];
    let current = { frames: [{ name: 'main', vars: [], ret: undefined }], heap: {}, output: [], storage: {} };
    states.push({ at: null, say: scenario.intro, ...snapshot(current), changed: emptyChanged() });

    for (const step of scenario.steps) {
        if (!labels.has(step.at)) throw new Error(`Label @${step.at} staat niet in het programma van "${scenario.title}"`);
        const changed = emptyChanged();
        current = snapshot(current);
        applyOps(current, step.ops, changed);
        if (!step.keepGarbage) collectGarbage(current);
        states.push({ at: step.at, say: step.say, ...snapshot(current), changed });
    }
    return states;
}

// zet "regel // @label" om in een regel en een label
function parseProgram(text) {
    const lines = [];
    const labels = new Map();
    text.replace(/\n$/, '').split('\n').forEach((raw, index) => {
        const match = raw.match(/^(.*?)\s*\/\/ @([\w-]+)\s*$/);
        const lineText = match ? match[1] : raw;
        if (match) labels.set(match[2], index);
        lines.push(lineText);
    });
    return { lines, labels };
}

// ---------- weergave ----------

function valueText(value) {
    if (value === null || value === undefined) return '(nog leeg)';
    if (isRef(value)) return '→ #' + value.ref.slice(1);
    if (value.isNull) return 'null';
    if (typeof value === 'string') return `"${value}"`;
    return String(value);
}

function createStepper(config) {
    const { scenarios } = config;
    const el = (id) => document.getElementById(id);
    const listing = el('listing');
    const title = el('title');
    const stepsNav = el('steps');
    const say = el('say');
    const sayWrap = el('say-wrap');
    const stackEl = el('stack-body');
    const heapEl = el('heap-body');
    const memory = el('memory');
    const arrows = el('arrows');
    const scrubber = el('scrubber');
    const counter = el('counter');
    const playButton = el('play');
    const speedSelect = el('speed');
    const classPanel = el('classpanel');
    const outputBox = el('output');
    const outputBody = el('output-body');
    const storageBox = el('storage');
    const storageBody = el('storage-body');
    const variantBar = el('variants');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    let scenario;
    let active;
    let variant = 'bug';
    let states;
    let labels;
    let lineEls;
    let index = 0;
    let timer = null;

    const scenarioIndex = () => {
        const n = parseInt(location.hash.replace('#', ''), 10);
        return n >= 1 && n <= scenarios.length ? n - 1 : 0;
    };

    function loadScenario() {
        variant = 'bug';
        loadVariant();
    }

    // een scenario kan een gecorrigeerde variant hebben (voor de lab "zoek de fout")
    function loadVariant() {
        pause();
        const number = scenarioIndex();
        scenario = scenarios[number];
        active = variant === 'fixed' && scenario.fixed ? { ...scenario, ...scenario.fixed } : scenario;

        const program = (active.withClass ? BANK_ACCOUNT_LISTING + '\n' : '') + active.code;
        const parsed = parseProgram(program);
        labels = parsed.labels;
        states = buildStates(active, labels);

        title.textContent = `Scenario ${number + 1} van ${scenarios.length}: ${scenario.title}`;

        stepsNav.replaceChildren();
        scenarios.forEach((item, i) => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#' + (i + 1);
            a.textContent = String(i + 1);
            a.setAttribute('aria-label', `Scenario ${i + 1}: ${item.title}`);
            if (i === number) a.setAttribute('aria-current', 'step');
            li.append(a);
            stepsNav.append(li);
        });

        if (variantBar) {
            variantBar.hidden = !scenario.fixed;
            variantBar.querySelectorAll('button').forEach((button) => {
                button.setAttribute('aria-pressed', String(button.dataset.variant === variant));
            });
        }

        listing.replaceChildren();
        lineEls = parsed.lines.map((text, i) => {
            const row = document.createElement('div');
            row.className = 'line';
            const gutter = document.createElement('span');
            gutter.className = 'gutter';
            gutter.textContent = String(i + 1);
            const mark = document.createElement('span');
            mark.className = 'mark';
            mark.setAttribute('aria-hidden', 'true');
            const code = document.createElement('code');
            code.textContent = text;
            row.append(gutter, mark, code);
            listing.append(row);
            return row;
        });

        scrubber.max = String(states.length - 1);

        // uitleg bij de class: BankAccount standaard, of de eigen onderdelen van dit scenario
        if (typeof renderClassPanel === 'function' && (active.withClass || active.members)) {
            classPanel.hidden = false;
            renderClassPanel(classPanel, [], active.members, active.className);
        } else {
            classPanel.hidden = true;
        }

        if (storageBox) storageBox.hidden = !active.storage;
        index = 0;
        render();
    }

    function makeValue(value, changedFlag) {
        const span = document.createElement('span');
        span.className = 'value' + (changedFlag ? ' changed' : '');
        span.textContent = valueText(value);
        return span;
    }

    function render() {
        const state = states[index];

        // code: de regel die nu wordt uitgevoerd
        lineEls.forEach((row) => {
            row.classList.remove('current');
            row.removeAttribute('aria-current');
            row.querySelector('.mark').textContent = '';
        });
        if (state.at !== null) {
            const row = lineEls[labels.get(state.at)];
            row.classList.add('current');
            row.setAttribute('aria-current', 'true');
            row.querySelector('.mark').textContent = '▶';
            const target = row.offsetTop - listing.clientHeight / 2 + row.clientHeight / 2;
            listing.scrollTo({ top: Math.max(0, target), behavior: reduceMotion.matches ? 'auto' : 'smooth' });
        }

        // uitleg
        counter.textContent = `Stap ${index} van ${states.length - 1}`;
        say.textContent = state.say;
        scrubber.value = String(index);
        scrubber.setAttribute('aria-valuetext', `Stap ${index} van ${states.length - 1}`);

        // stack: het nieuwste frame bovenaan
        stackEl.replaceChildren();
        if (state.frames.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = '(leeg: er is geen frame meer)';
            stackEl.append(empty);
        }
        for (let i = state.frames.length - 1; i >= 0; i--) {
            const frame = state.frames[i];
            const box = document.createElement('div');
            box.className = 'frame' + (i === state.frames.length - 1 ? ' top' : '') + (state.changed.frames.has(i) ? ' changed' : '');
            const heading = document.createElement('div');
            heading.className = 'frame-name';
            heading.textContent = frame.name + (i === state.frames.length - 1 ? '  (draait nu)' : '');
            box.append(heading);

            const list = document.createElement('ul');
            if (frame.vars.length === 0) {
                const empty = document.createElement('li');
                empty.className = 'empty';
                empty.textContent = '(nog geen variabelen)';
                list.append(empty);
            }
            for (const variable of frame.vars) {
                const li = document.createElement('li');
                if (isRef(variable.value)) li.dataset.ref = variable.value.ref;
                li.append(`$${variable.name} = `, makeValue(variable.value, state.changed.vars.has(`${i}.${variable.name}`)));
                list.append(li);
            }
            if (frame.ret !== undefined) {
                const li = document.createElement('li');
                li.className = 'ret';
                li.append('return ', makeValue(frame.ret, state.changed.vars.has(`${i}.ret`)));
                list.append(li);
            }
            box.append(list);
            stackEl.append(box);
        }

        // heap
        heapEl.replaceChildren();
        const ids = Object.keys(state.heap).sort();
        if (ids.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = '(nog geen objecten)';
            heapEl.append(empty);
        }
        for (const id of ids) {
            const object = state.heap[id];
            const card = document.createElement('div');
            card.className = 'object' + (state.changed.objs.has(id) ? ' changed' : '');
            card.dataset.id = id;
            const heading = document.createElement('div');
            heading.className = 'object-name';
            heading.textContent = `${object.cls} #${id.slice(1)}`;
            card.append(heading);
            const list = document.createElement('ul');
            for (const [name, value] of Object.entries(object.props)) {
                const li = document.createElement('li');
                li.append(`${name} = `, makeValue(value, state.changed.props.has(`${id}.${name}`)));
                list.append(li);
            }
            card.append(list);
            heapEl.append(card);
        }

        // uitvoer van het script
        if (outputBody) {
            outputBody.replaceChildren();
            if (state.output.length === 0) {
                const empty = document.createElement('span');
                empty.className = 'empty';
                empty.textContent = '(nog niets getoond)';
                outputBody.append(empty);
            }
            state.output.forEach((line, i) => {
                const row = document.createElement('div');
                const isNew = state.changed.output && i === state.output.length - 1;
                row.className = 'out-line' + (isNew ? ' changed' : '');
                row.textContent = line;
                outputBody.append(row);
            });
        }

        // opslag buiten het script (bijvoorbeeld de sessie)
        if (storageBody) {
            storageBody.replaceChildren();
            const keys = Object.keys(state.storage);
            if (keys.length === 0) {
                const empty = document.createElement('span');
                empty.className = 'empty';
                empty.textContent = '(nog niets opgeslagen)';
                storageBody.append(empty);
            }
            for (const key of keys) {
                const row = document.createElement('div');
                row.append(`$_SESSION['${key}'] = `, makeValue(state.storage[key], state.changed.storage.has(key)));
                storageBody.append(row);
            }
        }

        drawArrows();
    }

    // pijlen van elke verwijzing op de stack naar het object op de heap
    function drawArrows() {
        arrows.replaceChildren();
        const area = memory.getBoundingClientRect();
        arrows.setAttribute('width', String(area.width));
        arrows.setAttribute('height', String(area.height));

        const defs = document.createElementNS(SVG_NS, 'defs');
        defs.innerHTML = '<marker id="arrowhead" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 z" fill="currentColor"/></marker>';
        arrows.append(defs);

        // staan stack en heap onder elkaar (smal scherm), dan zijn de tekstverwijzingen (-> #1) genoeg
        if (heapEl.getBoundingClientRect().left <= stackEl.getBoundingClientRect().right) return;

        stackEl.querySelectorAll('[data-ref]').forEach((from) => {
            const card = heapEl.querySelector(`[data-id="${from.dataset.ref}"]`);
            if (!card) return;
            const a = from.getBoundingClientRect();
            const b = card.getBoundingClientRect();
            const x1 = a.right - area.left;
            const y1 = a.top + a.height / 2 - area.top;
            const x2 = b.left - area.left;
            const y2 = b.top + b.height / 2 - area.top;
            const path = document.createElementNS(SVG_NS, 'path');
            path.setAttribute('d', `M${x1},${y1} C${x1 + 40},${y1} ${x2 - 40},${y2} ${x2 - 2},${y2}`);
            path.setAttribute('marker-end', 'url(#arrowhead)');
            arrows.append(path);
        });
    }

    function go(newIndex) {
        index = Math.max(0, Math.min(states.length - 1, newIndex));
        render();
    }

    function pause() {
        if (timer !== null) clearInterval(timer);
        timer = null;
        playButton.textContent = '▶ Afspelen';
        playButton.setAttribute('aria-label', 'Afspelen');
        sayWrap.setAttribute('aria-live', 'polite');
    }

    function play() {
        if (index >= states.length - 1) go(0);
        playButton.textContent = '⏸ Pauze';
        playButton.setAttribute('aria-label', 'Pauzeren');
        // tijdens het afspelen geen schermlezer-meldingen bij elke stap
        sayWrap.setAttribute('aria-live', 'off');
        timer = setInterval(() => {
            if (index >= states.length - 1) {
                pause();
                return;
            }
            go(index + 1);
            if (index >= states.length - 1) pause();
        }, parseInt(speedSelect.value, 10));
    }

    el('first').addEventListener('click', () => { pause(); go(0); });
    el('back').addEventListener('click', () => { pause(); go(index - 1); });
    el('forward').addEventListener('click', () => { pause(); go(index + 1); });
    playButton.addEventListener('click', () => (timer === null ? play() : pause()));
    scrubber.addEventListener('input', () => { pause(); go(parseInt(scrubber.value, 10)); });
    speedSelect.addEventListener('change', () => { if (timer !== null) { pause(); play(); } });

    if (variantBar) {
        variantBar.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', () => {
                variant = button.dataset.variant;
                loadVariant();
            });
        });
    }

    // toetsenbord: pijltjes om te stappen, spatie om af te spelen
    document.addEventListener('keydown', (event) => {
        const tag = event.target.tagName;
        if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') return;
        if (event.key === 'ArrowRight') { pause(); go(index + 1); event.preventDefault(); }
        else if (event.key === 'ArrowLeft') { pause(); go(index - 1); event.preventDefault(); }
        else if (event.key === ' ' && tag !== 'BUTTON' && tag !== 'A') { timer === null ? play() : pause(); event.preventDefault(); }
    });

    window.addEventListener('hashchange', loadScenario);
    window.addEventListener('resize', drawArrows);
    loadScenario();
}
