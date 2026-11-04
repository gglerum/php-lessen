// Gedeelde stappen voor de labs bij de les Basics: de for loop uit Galgje (hangman_v1.php) die de letters van het woord toont.
// Het scenario regelt de regels erboven en de echo eronder; deze functie levert de stappen van de loop zelf.
//   word      het woord waar Galgje op loopt
//   guessed   de letters die al geraden zijn
//   lessEqual true: de fout <= in plaats van < (dan loopt de loop een keer te veel)
function loopSteps(word, guessed, lessEqual = false) {
    const steps = [];
    const limit = lessEqual ? word.length + 1 : word.length;
    const sign = lessEqual ? '<=' : '<';
    let display = '';

    for (let i = 0; i < limit; i++) {
        const outOfRange = i >= word.length;
        const letter = outOfRange ? null : word[i];

        steps.push({
            at: 'for',
            say: i === 0
                ? `De for loop begint: $i wordt 0. Is 0 ${sign} strlen($word)? strlen("${word}") is ${word.length}. Ja, dus de loop draait.`
                : `$i++ maakt $i ${i}. Is ${i} ${sign} ${word.length}? Ja, dus de loop draait nog een keer.`,
            ops: [['set', 'i', i]],
        });

        if (outOfRange) {
            steps.push({
                at: 'if',
                say: `$word[${i}] bestaat niet: het woord heeft maar ${word.length} letters (de posities lopen van 0 tot ${word.length - 1}). PHP geeft een waarschuwing en gebruikt een lege waarde. Die zit niet in $guessedLetters.`,
                ops: [['warn', `Uninitialized string offset ${i}`]],
            });
            display += '_';
            steps.push({ at: 'hide', say: 'De letter is niet geraden, dus er komt een _ bij. De uitvoer is nu een teken te lang.', ops: [['set', 'display', display]] });
            continue;
        }

        const isGuessed = guessed.includes(letter);
        steps.push({
            at: 'if',
            say: `$word[${i}] is "${letter}". Zit "${letter}" in $guessedLetters? ${isGuessed ? 'Ja' : 'Nee'}.`,
            ops: [],
        });
        display += isGuessed ? letter : '_';
        steps.push({
            at: isGuessed ? 'show' : 'hide',
            say: isGuessed ? `De letter is geraden, dus "${letter}" wordt achter $display geplakt.` : 'De letter is niet geraden, dus er komt een _ achter $display.',
            ops: [['set', 'display', display]],
        });
    }

    steps.push({
        at: 'for',
        say: `$i++ maakt $i ${limit}. Is ${limit} ${sign} ${word.length}? Nee, dus de loop stopt. De code gaat verder na de loop.`,
        ops: [['set', 'i', limit]],
    });
    return { steps, display };
}
