## Functions & Arrays
Lees op https://www.w3schools.com/php/default.asp de volgende onderwerpen goed door:

- Functions
- Arrays
- Superglobals
- Casting
- Constants
- Magic Constants
- Callback Functions

## Voorbeeld
In de example map staat de 2e versie van Galgje. In deze versie is het spel opgesplits in functies. Door code te verdelen over functies wordt het overzichtelijker en kunnen we makkelijker code hergebruiken. Zie bijvoorbeeld de functie `handleMistake()` die zowel vanuit `handleLetter()` als `handleWord()` wordt aangeroepen om een foute gok af te handelen. 

In het voorbeeld wordt een Exception gegooit wanneer de speler een ander karakter probeert te raden dan een letter, vervolgens wordt deze afgevangen en wordt een melding aan de speler getoond.

Normaal gesproken worden exceptions diep in de code gebruikt om programmeer fouten af te vangen, niet de fouten van de eind gebruiker. Een eind gebruiker hoort nooit de tekst van een exception te zien te krijgen. Een exception is alleen bedoelt voor de 
programmeur. Een try and catch hoort dus eigenlijk niet gebruikt te worden als een veredelde if statement.

Een voorbeeld van waar een Exception kan voorkomen is bijvoorbeeld wanneer er geen verbindingen kan worden gemaakt met de database. In zo'n situatie kan er een exception worden gegooid en kan er op een andere plaats in de code worden bepaald hoe dit verder moet worden afgehandeld zonder database.

## Huiswerk
![Library methods](./library.png)

We gaan een simpele applicatie maken om boeken te beheren. We maken hier gebruik van een "multi dimensionale array". Een array dat zelf meerdere arrays bevat. 

Dit worden de `$books` arrays die je bovenaan in je php bestand zet. Daarnaast maken we gebruik van een simpele
array voor `$auteurs`. Deze arrays gaan we "global" gebruiken in de functies van onze applicatie.

### Boek toevoegen

Om het eerst "eenvoudiger" te houden, hoef je de eind gebruiker niet te vragen om een auteur toe te voegen.
Je mag dit zelf in de code hard coderen. Vul de `$authors` array met drie namen van auteurs. 

Wanneer de eindgebruiker een boek toevoegt vraag je eerst om een auteur te kiezen. Je laat een lijst
zien van alle auteurs, met voor elke auteur een index:

> 1. J.K. Rowling
> 2. Stephen King
> 3. Dan Brown

De index is de `i` incrementer/teller van je `for-loop`. Wanneer de eindgebruiker een auteur heeft gekozen
kun je de juiste naam uit de `$authors` array halen. Vervolgens vraag je
om de rest van de gegevens van het boek.

Wanneer je alle gegegeves hebt kun je een nieuwe associatieve array maken met daarin de gegevens en deze toevoegen aan een de array `$books`.

-------
### Boek verwijderen

Voor het verwijderen van een boek kun je hetzelfde principe gebruiken. Je laat een lijst zien van alle boeken
zien met een index. Wanneer de eindgebruiker een boek heeft gekozen, kun je deze verwijderen uit de `$books` array
aan de hand van de gegeven index.

-------
### Use Cases

Om even kort samen te vatten, deze opdracht kent drie "Use Cases":

#### Use Case 1: Voeg een boek toe

**Primary Actor:** Eindgebruiker

**Preconditions:**  
- De eindgebruiker heeft toegang tot het systeem.
- Er zijn al auteurs beschikbaar in het systeem.

**Postconditions:**  
- Het nieuwe boek is toegevoegd aan de bibliotheek.

**Main Success Scenario:**  
1. De eindgebruiker kiest de optie om een boek toe te voegen.
2. Het systeem toont een lijst van beschikbare auteurs.
3. De eindgebruiker selecteert een auteur uit de lijst.
4. Het systeem vraagt de eindgebruiker om de titel van het boek in te voeren.
5. De eindgebruiker voert de titel van het boek in.
6. Het systeem vraagt de eindgebruiker om de ISBN van het boek in te voeren.
7. De eindgebruiker voert de ISBN van het boek in.
8. Het systeem vraagt de eindgebruiker om de uitgever van het boek in te voeren.
9. De eindgebruiker voert de uitgever van het boek in.
10. Het systeem vraagt de eindgebruiker om de publicatie datum van het boek in te voeren.
11. De eindgebruiker voert de publicatie datum van het boek in.
12. Het systeem vraagt de eindgebruiker om het aantal paginas van het boek in te voeren.
13. De eindgebruiker voert het aantal paginas van het boek in.
14. Het systeem maakt een nieuw boek aan met de ingevoerde gegevens en de geselecteerde auteur.
15. Het systeem voegt de nieuwe boek toe aan de lijst met boeken.
16. Het systeem gaat terug naar het hoofdmenu en bevestigt dat het boek succesvol is toegevoegd.

**Extensions:**

3a. De eindgebruiker selecteert een niet-bestaande auteur:
- Het systeem toont een foutmelding en vraagt de eindgebruiker om opnieuw een auteur te selecteren.

#### Use Case 2: Toon alle boeken

**Primary Actor:** Eindgebruiker

**Preconditions:**
- De eindgebruiker heeft toegang tot het systeem.
- Er zijn al boeken beschikbaar in het systeem.

**Postconditions:**
- De lijst van alle boeken wordt getoond aan de eindgebruiker.

**Main Success Scenario:**
1. De eindgebruiker kiest de optie om alle boeken te tonen.
2. Het systeem haalt de lijst van alle boeken op.
3. Het systeem toont de lijst van alle boeken met hun details (titel, auteur, ISBN, etc.).

**Extensions:**
- 2a. Er zijn geen boeken beschikbaar in het systeem:
  - 1. Het systeem gaat terug naar het hoofdmenu en toont een melding dat er geen boeken beschikbaar zijn.

#### Use Case 3: Verwijder een boek

**Primary Actor:** Eindgebruiker

**Preconditions:**
- De eindgebruiker heeft toegang tot het systeem.
- Er zijn al boeken beschikbaar in het systeem.

**Postconditions:**
- Het geselecteerde boek is verwijderd uit de bibliotheek.

**Main Success Scenario:**
1. De eindgebruiker kiest de optie om een boek te verwijderen.
2. Het systeem toont een lijst van alle beschikbare boeken.
3. De eindgebruiker selecteert een boek uit de lijst.
4. Het systeem vraagt de eindgebruiker om de keuze te bevestigen.
5. De eindgebruiker bevestigt de keuze.
6. Het systeem verwijdert het geselecteerde boek uit de lijst van boeken.
7. Het systeem gaat terug naar de lijst van beschikbaare boeken en bevestigt dat het boek succesvol is verwijderd.

**Extensions:**
- 3a. De eindgebruiker selecteert een niet-bestaand boek:
  - 1. Het systeem toont opnieuw de lijst van beschikbare boeken met een foutmelding en vraagt de eindgebruiker om opnieuw een boek te selecteren.
- 5a. De eindgebruiker annuleert de verwijdering:
  - 1. Het systeem annuleert de verwijdering en keert terug naar het hoofdmenu.

#### Use Case 4: Toon boeken voor een specifieke auteur
**Primary Actor:** Eindgebruiker

**Preconditions:**
- De eindgebruiker heeft toegang tot het systeem.
- Er zijn al boeken en auteurs beschikbaar in het systeem.

**Postconditions:**
- De lijst van boeken voor de geselecteerde auteur wordt getoond aan de eindgebruiker.

**Main Success Scenario:**
1. De eindgebruiker kiest de optie om boeken voor een specifieke auteur te tonen.
2. Het systeem toont een lijst van alle beschikbare auteurs.
3. De eindgebruiker selecteert een auteur uit de lijst.
4. Het systeem haalt de lijst van boeken voor de geselecteerde auteur op.
5. Het systeem toont de lijst van boeken met hun details (titel, ISBN, uitgever, publicatiedatum, aantal pagina's).

**Extensions:**
- 3a. De eindgebruiker selecteert een niet-bestaande auteur:
 - Het systeem toont een foutmelding en vraagt de eindgebruiker om opnieuw een auteur te selecteren.
- 4a. Er zijn geen boeken beschikbaar voor de geselecteerde auteur:
 - Het systeem gaat terug naar het hoofdmenu en toont een melding dat er geen boeken beschikbaar zijn voor de geselecteerde auteur.

### Functies
Zoals je in de diagram kunt zien heeft elke "Use Case" een eigen methode waarmee hij start,
vervolgens worden de stappen van de "Use Case" in deze methode uitgevoerd. Sommige handelingen
worden uitgevoerd door een andere methode, zoals het tonen van een lijst van boeken of auteurs.

- `showMainMenu()` - Laat het hoofdmenu zien en roept aan de hand van de keuze één van de volgende
  methodes aan: `handleAddBook()`, `showAllBooks()`, `handleRemoveBook()`
- Use Case 1: Een boek toevoegen
  - `handleAddBook()` - roept de methode `showBookForm()` aan en roept met de return waarde `addBook()` aan.
    - `showBookForm()` - roept de methode `showAuthorsMenu()` aan, krijgt de auteur keuze terug en vraagt vervolgens
          de gebruiker om de rest van de gegevens van een boek in te voeren. Returned een gevulde array.
      - `showAuthorsMenu()` - Laat een lijst van auteurs zien en vraagt de gebruiker om een auteur te kiezen. Returned een string met de auteur naam.
    - `addBook()` - Voegt een boek array toe aan de `books` array.
- Use Case 2: Alle boeken tonen
  - `showAllBooks()` - roept `showBookCatalog()` aan.
    - `showBookCatalog()` - Laat een lijst van boeken zien.
- Use Case 3: Een boek verwijderen
  - `handleRemoveBook()` - roept `showRemoveBookForm()` en gebruikt de return waarde om `removeBook()` aan te roepen.
    - `showRemoveBookForm()` - roept `showBookCatalog()` aan en vraagt vervolgens de gebruiker een boek te kiezen. Returned een index van de `books` array.
    - `removeBook()` - Verwijderd een boek uit de `books` array.
- Use Case 4: Toon boeken voor een specifieke auteur
  - `handleShowAuthorBooks()` - roept de methode `showAuthorsMenu()` aan, krijgt de auteur keuze terug en roept daarmee `getBooksByAuthor()` aan. De boeken die worden teruggegeven worden doorgegeven aan `showBookCatalog()`
    - `getBooksByAuthor()` - filtert de lijst van boeken op basis van de meegegeven auteur.

### Letop:
- Elke "Use Case" krijgt zijn eigen functie.
- Maak daarnaast logisch gebruik van functies op je code in logische stukken op te delen.
- Maak voor "Use Case 4" gebruik van de `array_filter` functie om boeken op "auteur" te filteren.

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
- Methodes doen maar 1 ding. Als je merkt dat je methode meerdere dingen doet, splits deze dan op in meerdere methodes.
- Een methode heeft een zelf documenterende naam. Aan de naam van de methode is het direct duidelijk wat het doet.
- Een methode heeft een phpdoc commentaar boven de methode. Hierin staat wat de methode doet, en wat de parameters zijn.