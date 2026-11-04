## Basics 1
Lees op https://www.w3schools.com/php/default.asp de volgende onderwerpen goed door:
- Syntax
- Comments
- Variables
- Echo / Print
- Data Types
- Strings
- Numbers
- Math
- Operators
- IF... Else... Elseif
- Switch
- Loop

## Voorbeeld
In de example map staat een eerste versie van Galgje in een procedurele stijl. Dat wil zeggen dat het in één bestand staat, onder
elkaar en zonder gebruik te maken van functies. In dit voorbeeld kun je zien hoe de eerder genoemde onderwerpen (syntax, comments, variables, etc) gebruikt worden om een simpel console spel te schrijven.

*LETOP: in het voorbeeld wordt gebruik gemaakt van de PHP functie `readline()`. Deze is niet in elke build van PHP besschikbaar. Zie de snippet hieronder by **huiswerk** die gebruikt kan worden om console input uit te lezen.*

## Huiswerk
Maak een simpel console raadspel waarbij de speler een getal moet raden tussen 0 en N. N is het getal dat de speler zelf in mag voeren. De speler mag 10 keer raden.

**Terminal input uitlezen**

Je kunt de volgende snippet gebruiken om gebruikers input uit te lezen.
```PHP
// open de terminal input buffer als stream
$stdin = fopen('php://stdin', 'r');

// vraag om input
echo "Jouw input:";
// lees de gebruiker input van de terminal en haal wit ruimte voor en achter de input weg
$input = trim(fgets($stdin));

//sluit de stream weer
fclose($stdin);
```

### Checklist
- Variabelen zijn in het engels geschreven.
- Variabelen zijn in camelCase.
- Naamgeving van de variabelen zijn duidelijk en beschrijvend.
- Elk code block (begint met `{` en eindigt met `}`) wordt voorgegaan door een regel commentaar.
- Comments zijn in het engels geschreven.
- De code is geformateerd aan de hand van PSR-12.
- Een loop bevat alleen code dat ook echt herhaalt hoort te worden. Berekeningen of andere zware
  operaties die voor elke iteratie hetzelfde blijven, horen niet in een loop te staan.
- Declareer variabelen zo dicht mogelijk waar het gebruikt word.
- De code bevat geen/tot zeer weinig code duplicatie. (DRY: Don't Repeat Yourself)

## Labs
Wil je zien wat PHP regel voor regel doet met variabelen, loops en voorwaarden? In de interactieve labs volg je stukjes uit Galgje stap voor stap. Open [index.html](./example/basics/lab/index.html) in je browser, je hoeft niets te installeren. Werkt de opmaak niet, start dan `php -S localhost:8090` in de map `example/basics/lab`.

1. [loops_and_conditions.html](./example/basics/lab/loops_and_conditions.html) is Lab 1. Een step debugger voor variabelen en operators, de `for` loop, de `while` loop, `if` en `else` en de `switch`.
2. [predict_output.html](./example/basics/lab/predict_output.html) is Lab 2. Zes scenario's waarbij je eerst voorspelt wat er wordt getoond.
3. [bughunt.html](./example/basics/lab/bughunt.html) is Lab 3. Drie scripts die iets anders doen dan bedoeld: een loop die een keer te veel draait, `=` in plaats van `==` en een vergeten `break`.

Met `php verify_lab1.php` tot en met `php verify_lab3.php` (in de map `example/basics`) controleer je dat de antwoorden in de labs kloppen met wat PHP echt doet.
