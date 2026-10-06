## Verschillen tussen PHP & Phyton
Er zijn een aantal verschillen tussen het schrijven van code voor Python en PHP. Hier vindt je een
vergelijking op basis van de lessen van Martin.

**Python**
```Python
print("Hello World")
```
**PHP**
```PHP
<?php
echo "Hello World";
```

Zoals je ziet aan d bovenstaande snippets is PHP niet heel veel moeilijker dan Python. Er zijn echter wel een
aantal verschillen. Zoals dat elke php bestand met een `<?php` tag begint.

### Scope & Indentation
Waar Python gebruik maakt van indentation om aan te geven dat een stuk code bij een bepaalde
scope hoort, maakt PHP gebruik van `{}`. In PHP gebruik je eigenlijk alleen whitespaces voor de
leesbaarheid.

Omdat PHP niet naar whitespaces kijkt moeten we dus elke regel eidigen met `;` en bepalen we de scope met curly braces `{}`.

### Types
**Python**
```Python
x = 5
```
**PHP**
```PHP
<?php
$x = 5;
```

In PHP begint elke variabele (met uitzondering van constanten) met een `$`. Je hoeft in PHP zoals bij Python geen type te declareren. Het wordt echter aangeraden om dit wel te doen. Dit mogelijk voor functie parameters, functie return waarde en class properties.

**PHP**
```PHP
<?php
function showText(string $text): void
{
    echo $text;
}
```
De mogelijke types zijn: 

```
Built-in types
- null
- bool
- int
- float
- string
- array
- object
- resource
- never
- void
Relative class types:
- self
- parent
- static
User-defined types (generally referred to as class-types)
- Interfaces
- Classes
- Enumerations
- callable type
```

Een best lange lijst van types. Geen paniek, voor nu zijn alleen de volgende relevant: `bool`, `int`, `float`, `string`, `array` en `void`.

### If statements
**Python**
```Python
if x > 5:
    print("x is groter dan 5")
elif x == 5:
    print("x is gelijk aan 5")
else:
    print("x is kleiner dan 5")
```
**PHP**
```PHP
<?php
if ($x > 5) {
    echo "x is groter dan 5";
} else if ($x == 5) {
    echo "x is gelijk aan 5";
} else {
    echo "x is kleiner dan 5";
}
```
Zoals je kunt zien is de syntax van de if statements in PHP en Python bijna hetzelfde. Het enige
verschil is dat je in PHP de conditie tussen `()` zet en de code tussen `{}`.


#### Logical operators
Een ander verschil die zowel voor loops als de if statements geldt is dat de logical operators in PHP
anders zijn dan in Python.

OR en AND in Python:
```Python
if x > 5 or x < 10:
    print("x is groter dan 5 of kleiner dan 10")
    
while x > 5 and x < 10:
    print(x)
```
in PHP:
```PHP
<?php
if ($x > 5 || $x < 10){
    echo "x is groter dan 5 of kleiner dan 10";
}

while($x > 5 && $x < 10){
    echo $x;
}
```

### Loops
#### For loop
**Python**
```Python
for i in range(5):
    print(i)
    if i == 2:
        break
```
**PHP**
```PHP
<?php
for($i = 0; $i < 5; $i++){
    echo $i;
    if($i == 2){
      break;  
    }
}
```
In PHP moet je de variabele `i` eerst definieren, daarna geef je de conditie op waaraan voldaan moet
worden om de loop te blijven draaien en daarna geef je aan wat er moet gebeuren na elke iteratie.
Zoals je kunt zien kun je binnen PHP ook `break` en `continue` gebruiken.

Gelukkig is de for loop voor het uitlezen van een array (list in Python) een stuk makkelijker:
**Python**
```Python
arr = [1, 2, 3, 4, 5]
for i in arr:
    print(i)
```
**PHP**
```PHP
<?php
$arr = [1, 2, 3, 4, 5];
foreach($arr as $i){
    echo $i;
}
```
of als je ook de array key (index) wilt gebruiken:
```PHP
<?php
$arr = [1, 2, 3, 4, 5];
foreach($arr as $key => $value){
    echo $key . ": " . $value;
}

output: 0: 1
        1: 2
        2: 3
        3: 4
        4: 5
```

#### While loop
**Python**
```Python
i = 0
while i < 5:
    print(i)
    i += 1
```
**PHP**
```PHP
<?php
$i = 0;
while($i < 5){
    echo $i;
    $i++;
}
```
Ook hier is de syntax vrijwel hetzelfde.

### Arrays (Lists in Python)
Zoals we bij de for loop al zagen is de syntax voor het aanmaken van een array in PHP en een list in
Python vrijwel hetzelfde.

**Python**
```Python
arr = [1, 2, 3, 4, 5]
print(arr[0]) # 1
```
**PHP**
```PHP
<?php
$arr = [1, 2, 3, 4, 5];
echo $arr[0]; // 1
```

Het is een goed idee om te kijken welke functies PHP standaard beschikbaar heeft om een array te werken:
https://www.php.net/manual/en/ref.array.php Je hoeft ze niet uit de hoofd te weten. Maar het is wel handig
om te weten wat er ongeveer beschikbaar is.

### Dictionary
**Python**
```Python
dictionary = {
    "Dier": "Hond",
    "Voertuig": "Auto"
}
print(dictionary["Dier"]) # Hond
```

**PHP**
```PHP
<?php
$dictionary = [
    "Dier" => "Hond",
    "Voertuig" => "Auto"
];
echo $dictionary["Dier"]; //Hond
```
Een dictionary in PHP wordt ook wel een associative array genoemd. Je kunt voor een associative array dezelfde
functies gebruiken als een normale array.

### Functions
**Python**
```Python
def add(a, b):
    return a + b
```
**PHP**
```PHP
<?php
function add(int $a, int $b): int
{
    return $a + $b;
}
```
In de basis lijk de syntax van een method in PHP en Python op elkaar. Het voornaamste verschil is dat
je in PHP naast de types van de parameters ook het type van de return value kan aangeven, door het
type met een `:` achter de method naam te zetten.

Letop: dit is de meest simpele vorm van een method, in de praktijk kunnen de method definities er een
stuk complexer uitzien. Bijvoorbeeld:

```PHP
<?php
static public function add(?int $a, int $b = 5, int &$sum, int ...$numbers): void
{
    foreach($numbers as $number){
        $sum += $number;
    }
    $sum = $sum * $a + $b;
}
```

### Namespaces & Imports (Modules in Python)
Waar je in Python je code opdeelt in modules, gebruik je in PHP namespaces. Een namespace is een map waarin
meerdere classes kunnen staan. Een class is een bestand waarin je code staat.

**Python**
```Python
import random
def random_number():
    return random.randint(0, 10)
```
**PHP**
```PHP
\Person.php
<?php
namespace Glenn\Test;

public class Person {
    public function __construct(
        public string ...$properties
    ){}
}

\factory\MakePerson.php
<?php
namespace Glenn\Test\Factory;

use Glenn\Test\Person;

public class MakePerson {
    public static makePerson(string $name, string $address): Person
    {
        return new Person($name, $address);
    }
}
```
In de bovenstaande PHP code zijn `Glenn\Test` en `Glenn\Test\Factory` de namespaces (mappen) waar de class `Person` en `MakePerson` in staan. Hoewel je in PHP niet alles in classes en namespaces hoeft onder te brengen, is dit met
Object Georienteerd programmeren wel de bedoeling.

### Comments
**Python**
```Python
#Single line comment

"""
Multi line comment
"""
```
**PHP**
```PHP
// Single line comment

/*
Multi line comment
*/

#  This is a one-line shell-style comment
```
Er is nog een soort comment in PHP, namelijk de `phpdoc` comment. Dit is een comment die je boven een
class of method zet en die je kunt gebruiken om een website met documentatie te genereren. Ook maakt de IDE
gebruik van deze informatie om te laten zien wanneer je met je muis over een class of functie hovert.

```PHP
/**
 * An example class
 * @author Glenn Glerum <email@email.com>
 */
class Example
{
    /**
     * This method does something.
     *
     * @param string $param1 A string parameter.
     * @param int $param2 An integer parameter.
     * @return void
     */
    public function doSomething($param1, $param2)
    {
        // ...
    }
}
```

### String Concatenation
**Python**
```Python
string = "Hello " + "World"
```
**PHP**
```PHP
$string = "Hello " . "World"; 
```