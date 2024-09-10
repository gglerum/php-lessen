## Functions & Arrays
Lees op https://www.w3schools.com/php/default.asp de volgende onderwerpen goed door:

- Functions
- Arrays
- Superglobals
- Casting
- Constants
- Magic Constants
- Callback Functions
- Exceptions

## Voorbeeld
In de example map staat de 2e versie van Galgje. In deze versie is het spel opgesplits in functies. Door code te verdelen over functies wordt het overzichtelijker en kunnen we makkelijker code hergebruiken. Zie bijvoorbeeld de functie `handleMistake()` die zowel vanuit `handleLetter()` als `handleWord()` wordt aangeroepen om een foute gok af te handelen. 

In het voorbeeld wordt een Exception gegooit wanneer de speler een ander karakter probeert te raden dan een letter, vervolgens wordt deze afgevangen en wordt een melding aan de speler getoond.

Normaal gesproken worden exceptions diep in de code gebruikt om programmeer fouten af te vangen, niet de fouten van de eind gebruiker. Een eind gebruiker hoort nooit de tekst van een exception te zien te krijgen. Een exception is alleen bedoelt voor de 
programmeur. Een try and catch hoort dus eigenlijk niet gebruikt te worden als een veredelde if statement.

Een voorbeeld van waar een Exception kan voorkomen is bijvoorbeeld wanneer er geen verbindingen kan worden gemaakt met de database. In zo'n situatie kan er een exception worden gegooid en kan er op een andere plaats in de code worden bepaald hoe dit verder moet worden afgehandeld zonder database.

## Huiswerk
Maak een simpel spel waar je een array vult met speelkaarten. Het spel kiest een random kaart en de speler moet raden welke kaart het is. Zoals bij het eerdere spel mag de speler maar 10 keer raden. Laat de kaarten zien van de pogingen van de speler.

De invoer van de speler is het symbol + het getal of de letter van de kaart.
♠ 2 t/m 10, A (aas), J (boer), Q (koningin), K (koning)
♥ ...
♦ ...
♣ ...

Dit is ook hoe de pogingen van de speler worden weergegeven.

Letop: maak gebruik van functies met return types en schrijf daar documentatie boven. vergeet niet om grote code blocken binnen je functies ook te documenteren.