// Gedeelde onderdelen voor de labs over callback functies.
// myArrayFilter is de versie van array_filter "achter de schermen" uit callback_functions.md.

const LISTING_FILTER = `function myArrayFilter($array, $callback)
{
    $newArray = []; // @init
    foreach ($array as $arrayItem) { // @loop
        if ($callback($arrayItem)) { // @call
            $newArray[] = $arrayItem; // @add
        }
    }

    return $newArray; // @ret
}
`;

// Bouwt de stappen van een aanroep van myArrayFilter(). Het scenario regelt alleen wat er in de callback gebeurt.
//   items         de array die wordt gefilterd
//   callbackValue wat in $callback komt te staan (de naam van een functie, of een tekst voor een anonieme functie)
//   frameName     naam van het frame van de callback, bijvoorbeeld "is_numeric()"
//   test          (item) => true of false: wat de callback teruggeeft
//   calleeVars    (item) => variabelen in het frame van de callback
//   calleeSay     (item, result) => uitleg bij het uitvoeren van de callback
//   bodyLabel     regel in de callback die wordt uitgevoerd (zonder: de regel van de aanroep)
//   calleeOps     (item) => extra operaties in de callback, bijvoorbeeld een waarschuwing
//   mainLabel     de regel in main waar myArrayFilter() wordt aangeroepen
function filterSteps(o) {
    const steps = [];
    const kept = [];
    const shown = (list) => list.slice();
    const text = (v) => (typeof v === 'string' ? `"${v}"` : String(v));

    steps.push({
        at: o.mainLabel,
        say: `myArrayFilter() wordt aangeroepen. De array en de callback komen binnen als parameters $array en $callback in een nieuw frame.`,
        ops: [['push', 'myArrayFilter()', { array: o.items, callback: o.callbackValue }]],
    });
    steps.push({ at: 'init', say: '$newArray begint als een lege array. Hier komen de elementen in die door de callback worden goedgekeurd.', ops: [['set', 'newArray', []]] });

    for (const item of o.items) {
        const result = o.test(item);
        steps.push({
            at: 'loop',
            say: `foreach pakt het volgende element: $arrayItem is ${text(item)}.`,
            ops: [['set', 'arrayItem', item]],
        });
        steps.push({
            at: 'call',
            say: `$callback($arrayItem) roept de callback aan met ${text(item)}. De callback krijgt een eigen frame.`,
            ops: [['push', o.frameName, o.calleeVars(item)]],
        });
        steps.push({
            at: o.bodyLabel || 'call',
            say: o.calleeSay(item, result),
            ops: [...(o.calleeOps ? o.calleeOps(item) : []), ['ret', result]],
        });
        if (result) {
            steps.push({ at: 'call', say: 'De callback gaf true terug. Het frame verdwijnt en de if is waar.', ops: [['pop']] });
            kept.push(item);
            steps.push({ at: 'add', say: `${text(item)} wordt toegevoegd aan $newArray.`, ops: [['set', 'newArray', shown(kept)]] });
        } else {
            steps.push({ at: 'call', say: 'De callback gaf false terug. Het frame verdwijnt en de if is niet waar, dus dit element wordt overgeslagen.', ops: [['pop']] });
        }
    }

    steps.push({ at: 'ret', say: 'Alle elementen zijn langs geweest. De functie geeft $newArray terug.', ops: [['ret', shown(kept)]] });
    steps.push({ at: o.mainLabel, say: 'Het frame van myArrayFilter() verdwijnt. De teruggegeven array komt in $filtered in main.', ops: [['pop'], ['set', 'filtered', shown(kept)]] });
    return { steps, kept };
}
