# Callback functies
Binnen PHP kunnen functies andere functies als argumenten ontvangen, een voorbeeld hiervan zijn de verschillende array functies, zoals `array_filter`. Om een bestaande functie te gebruiken als callback functie geef je deze als string mee, zoals hieronder:

```PHP
$array = [1, 'twee', 3, 'vier', 5, 'zes', 7, 'acht', 9, 10];
$filtered = array_filter($array, 'is_numeric')
// [1, 3, 5, 7, 9, 10]
```

De callback functie die als argument wordt meegegeven wordt achter de schermen aangeroepen; bij de aanroep krijgt de callback functie een bepaalde waarde mee. In het geval van `array_filter` wordt de callback functie (`is_numeric` in het bovenstaande voorbeeld) aangeroepen voor elke waarde in een array (`$array` in het bovenstaande voorbeeld).

Achter de schermen werkt `array_filter` dus als volgt:

```PHP
$array = [1, 'twee', 3, 'vier', 5, 'zes', 7, 'acht', 9, 10];
$filtered = array_filter($array, 'is_numeric')

function array_filter($array, $callback){
    $newArray = [];
    foreach ($array as $arrayItem) {
        if ($callback($arrayItem)) {
            $newArray[] = $arrayItem;
        }
    }
    return $newArray;
}
```

Het kan wat lastig lezen, dus laten we `$callback` direct vervangen in de loop met `is_numeric`:

```PHP
foreach ($array as $arrayItem) {
    if (is_numeric($arrayItem)) {
        $newArray[] = $arrayItem;
    }
}
```

Je gebruikt `array_filter` om waardes uit een array te halen die voldoen aan een expressie (voorbeeld expressie: $getal >= 5).

```PHP
$namen = [
    'finn',
    'glenn',
    'safira',
    'ischka',
    'ed',
    'jan'
];

$filteredNames  = array_filter($namen, fn($naam) => strlen($naam) <= 3);
//['ed', 'jan']
```

## User defined functie vs  built-in functie als callback functies
Een "User defined" functie is een functie die je zelf hebt geschreven/gedeclareert
```PHP
function is5OrBigger($number)
{
    // `is_numeric` is een php built-in functie
    return is_numeric($number) && $number >= 5;
}
```

Zowel "User defined" functies als php ingebouwde functie kun je gebruiken als callback functies.

```PHP
$numbers = [1, 'twee', 3, 'vier', 5, 'zes', 7, 'acht', 9, 10];

// user-defined functie
$numbersBiggerThan5 = array_filter($numbers, 'is5OrBigger');

// php built-in functie
$realNumbers = array_filter($numbers, 'is_numeric');
```

## Anonieme functies

Een anonieme functie is een functie zonder naam, die je direct kan gebruiken als argument voor een functie die een callback functie verwacht. Je kunt deze overigens ook toewijzen aan een variabel of in een array stoppen, maar laten we dat voor nu even vergeten.

Een anonieme functie ziet er als volgt uit:
```PHP
function ($a, $b){
    return $a * $b;
}
```
Zoals je ziet onbreekt dus alleen de naam. Een anonieme functie kunnen we dus meegeven
aan een functie die een callback functie als argument verwacht. We kunnen dus in het volgende voorbeeld:

```PHP
$realNumbers = array_filter($numbers, 'is_numeric');
```

`is_numeric` vervangen met een anonieme functie, bijvoorbeeld:

```PHP
$oddKeys = array_filter($numbers, function ($number, $key) {
    return is_numeric($number) && $key % 2 == 0;
}, ARRAY_FILTER_USE_BOTH);
```
Het derde argument `ARRAY_FILTER_USE_BOTH` is misschien iets verwarrend. Deze hoort
niet bij de anonieme functie. `ARRAY_FILTER_USE_BOTH` vertelt aan `array_filter` dat
de callback functie niet alleen de `array waarde` gebruikt, maar ook de `array key`.

## Arrow functies
Een arrow functie is een korte manier om een anonieme functie te schrijven die altijd een waarde teruggeeft en ziet er als volgt uit
```PHP
fn($parameter1, $parameter2) => 'return' . 'waarde'
```

voorbeeld:
```PHP
fn($a, $b) => $a + $b;
```

een anonieme functie schrijf je als:
```PHP
function($a, $b) {
    return $a + $b;
}
```
Laten we het stapgewijs vergelijken met elkaar:

```PHP 
fn($a, $b)
```

vs

```PHP
function($a, $b)
```

Het enige verschil hier is dat "function" bij een arrow functie niet helemaal wordt uitgeschreven, maar afgekort als "fn".

```PHP
=> $a + $b;
```

vs

```PHP
{
    return $a + $b;
}
```

Hier wijkt een arrow functie af van een anonieme functie. Een arrow functie heeft geen krulhaken `{ }`
en in plaats van `return` gebruik je `=>`.

LETOP: een arrow functie geeft altijd een return waarde terug; dus de `=>` is verplicht.

Gebruik van een arrow functie als callback argument voor `array_filter`:

```PHP
$stringsLongerThan3Characters = array_filter($numbers, fn($number) => is_string($number) && strlen($number) > 3);
```

## Callback Functie Scope

Om variabelen buiten de scope van een callback functie te gebruiken bij een anonieme functie, is de keyword `use`. Deze komt direct na `function($parameter)` zoals in het voorbeeld hieronder:

```PHP
$max = 5;

$test = array_filter($numbers, function ($number) use ($max) {
    return is_numeric($number) && $number > $max;
});
```

Bij een arrow functie hoeft dit niet, deze heeft altijd toegang tot variabelen buiten de callback scope.

```PHP
$max = 5;
$test = array_filter($numbers, fn($number) => is_numeric($number) && $number > $max);
```
