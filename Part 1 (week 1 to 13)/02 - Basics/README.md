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

## Huiswerk
Maak een simpel console raadspel waarbij de speler een getal moet raden tussen 0 en N. N is het getal dat de speler zelf in mag voeren. De speler mag 10 keer raden.

Je kunt gebruik maken van [readline](https://www.php.net/manual/en/function.readline.php) om de gebruiker om input te vragen.

Letop: vergeet geen commentaar te gebruiken om te omschrijven wat er in code blokken gebeurt. Code blokken starten met een `{` en eindigen met een `}`.

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