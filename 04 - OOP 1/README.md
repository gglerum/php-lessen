## OOP 1
Lees op https://www.w3schools.com/php/default.asp de volgende onderwerpen goed door:

- Classes & Objects
- Constructor
- Destructor
- Access Modifiers
- Encapsulation (https://www.geeksforgeeks.org/php-encapsulation/)
- Constants

## Voorbeeld
### Hangman v3
Met Object Georienteerd programmeren nemen we een volgende stap in het organiseren van onze code, door variabelen en functies die bij elkaar horen samen te plaatsen in een class.

Zo zie je dat [console.php](./example/hangman_v3/console.php) alleen nog maar de invoer van de gebruiker aanneemt en de rest uitbesteed aan de [GameService](./example/hangman_v3/game/GameService.php).

GameService op zijn beurt besteed de "woord" gerelateerde functies uit aan de [Word](./example/hangman_v3/game/Word.php) class. En het tekenen van hangman uit aan [DrawnHangman](./example/hangman_v3/game/DrawnHangman.php).

Daarnaast is er nog een class [Game](./example/hangman_v3/game/Game.php) die gebruikt wordt om variabelen met betrekking tot de status van het spel bij te houden.

#### Dependency injection
De code maakt ook gebruik van dependency injection. Dat wil zeggen dat we bepaalde classes niet binnen een andere class aanmaken. Neem als voorbeeld [RandomWordGenerator](./example/hangman_v3/game/generator/RandomWordGenerator.php). Hier maken we in `console.php` een object van aan en geven daar een object van [Words](./example/hangman_v3/game/generator/words.php) aan mee.

Waarom hebben we de `Words` class nodig en voegen we de woordenlijst niet direct toe aan `RandomWordGenerator`? en waarom maken we een object van `RandomWordGenerator` niet direct aan in de `GameService` class?

Het simpele maar vage antwoord hierop is `Polymorpisme` wele in OOP3 zal worden behandelt. Een meer uitgebreid antwoord is dat dependency injection (zoals het heet wanneer je een object van een class meegeeft aan een constructor van een andere class) stelt ons instaat om te kunnen wisselen met een andere class.

Stel we willen woorden uit de database gebruiken en niet uit een array? Dependency injection en polymoprphisme stelt ons instaat dit te doen, zonder al te veel code aan te hoeven passen. Hoe dat precies werkt zie je straks in OOP3.

## Testen
Tijdens het ontwikkelen van je applicatie test je vaak met de hand je code. Met `echo` controleer je of if statements op de juiste manier worden uitgevoerd en of een variabele wel de juiste waarde heeft. Dit werkt tot een zekere hoogte, tot je een aantal maanden verder bent, iets aan de code verandert en het niet meer werkt zoals verwacht. Dan moet je weer met de hand je code door lopen en `echo` of `var_dump` hier en daar toevoegen om te zien waar iets misgaat. Zeker voor een grote code base is dit ondoenlijk.

Gelukkig bestaat er iets als "automatic testing" waarbij automatisch wordt gecontroleerd of je code nog steeds werkt zoals bedoelt, ook nadat je wijzigingen hebt doorgevoerd. Je moet uiteraard wel eerst de testen schrijven.

In de map tests van librarysystem in de example map zie je hoe zo'n test er uit kan zien.

Om testen te kunnen schrijven heb je de PHPUnit library nodig: https://phpunit.de/index.html

Meer over testen kun je in de volgende artikelen lezen:
- https://www.freecodecamp.org/news/test-php-code-with-phpunit/
- https://pguso.medium.com/a-beginners-guide-to-phpunit-writing-and-running-unit-tests-in-php-d0b23b96749f

## Huiswerk
Kijk in het #backend-projecten kanaal of je daar een project tussen ziet staan die je interessant vindt. Dit wordt het project waar je de komende tijd aan wilt werken en dingen aan wilt toevoegen.

Bouw deze door gebruikt te maken van verschillende classes die ieder hun eigen functies hebben.