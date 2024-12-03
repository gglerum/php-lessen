## Tools
### Visual Studio Code
Tijdens deze lessen maken we gebruik van de Visual Studio Code (VS Code). VS Code kun je hier
downloaden: https://code.visualstudio.com/Download

Essentiele know hows van VS Code vind je hier: [VS Code - Deel 1](vscode.md)

#### Extensies
VS Code op zich zelf bied weinig ondersteuning voor PHP. Om het meeste uit VS Code te halen heb je een aantal extensies nodig. De lijst vind je hier: [VS Code - Extensies](vscode_extensions.md)

### Git
De huiswerkopdrachten of eigen projecten die je in PHP schrijft bewaar je ook via Git. Git is een versiebeheersysteem waar je je code kunt bergen en delen met anderen. Één van de belangrijskte features van Git, is dat je altijd terug kan naar een vorig versie. Met de integratie van Git in Vs Code kun je dus ook vrij
makkelijk een bestand terug halen die perongeluk is verwijderd, of een stuk code die je perongeluk hebt
veranderd.

Mocht je nog geen Github account hebben en Git nog niet geinstalleerd, dan kun je dat nu doen.
- https://github.com/
- https://git-scm.com/download/win

Het volgende artikel legt uit hoe je Git kunt gebruiken in Vs Code:
https://code.visualstudio.com/docs/sourcecontrol/intro-to-git

### XAMPP
Om in PHP te programmeren heb je eigenlijk een webserver nodig. Nu kun je in theorie je laptop inrichten als server,
maar het is makkelijker om daar een ontwikkel omgeving voor te installeren zoals XAMPP.

XAMPP kun je hier vinden: https://www.apachefriends.org/

*op een later moment gaan we kijken naar docker, mits je laptop dit ondersteund.*

### XDEBUG
In vanilla PHP kun je niet debuggen. Om dit mogelijk te maken moet je XDebug installeren. Op de volgende pagina kun je aan de hand van een wizard het juiste bestand downloaden, installatie instructies staan daar ook bij.

https://xdebug.org/wizard

## PHP Basics
- [Verschillen Phyton & PHP](differences.md)
- [Naamgeving](naming.md)

## Console applicaties
Hoewel PHP eigenlijk geen taal is om console applicaties in te schrijven, zal dit wel onze focus zijn voor de komende vier weken. Zo kunnen we eerst bekend raken met PHP voor we in http requests en andere web specifieke dingen duiken.

Het is trouwens niet dat er nooit iets in PHP voor de console wordt gemaakt. Veel frameworks zoals laravel hebben hulpprogrammas
die in PHP zijn geschreven.

## Opdracht
Maak een klein programma dat uit een lijst van getallen telt hoeveel er even of oneven zijn.

1. Maak een array met de volgende getallen: 42, 67, 35, 89, 24, 76, 58, 93, 7, 30, 83, 46, 13, 25, 98, 53, 17, 79, 57, 8.
2. Gebruik een for-loop om door de array te itereren.
3. Controleer voor elk getal of het even of oneven is met behulp van een if-else-statement.
4. Tel daarbij zowel de even en oneven getallen en sla deze op in een associative array waarbij de key de string "even" of "oneven" is en de waarde de het toaal of "even" of "oneven" getallen.

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