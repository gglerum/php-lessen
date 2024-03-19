## VS Code

### Code Style
De PHP extensie van DEVSENSE past de PSR-12 standaard toe voor het formateren van je code. Dit is de code style die wij voor PHP over het algemeen hanteren. Kortom de extensie zorgt er dus voor dat je code op de juiste manier geformateerd is.

#### Auto format
Out of the box moet je met de hand zelf je code formateren door rechts in een PHP bestand te klikken en te kiezen voor "Format Document". We kunnen VS Code dit ook automatisch laten doen.

Ga in het menu naar: `File -> Preferences -> Settings` en daar naar `Text Editor -> Formatting`. Enable hier "Format On Paste" en "Format On Save". Als je het prettig vind kun je ook "Format On Type" aanzetten.

### Code Completion
Tijdens het typen laat PHP je een context menu zien met functies, variabelen of properties. Je kunt deze context menu ook met de hand oproepen met `ctrl + spatie`.

Meer over code completion kun je hier lezen: https://code.visualstudio.com/docs/editor/intellisense

### Local History
Het is de bedoeling dat we met Git gaan werken. Git zorgt er voor dat als je code kwijt raakt, dat je deze code
via Git weer terug kunt halen. Helaas werkt dit alleen met code die je gepushed hebt.

Gelukkig is er een oplossing om code terug te halen wanneer `ctrl + z` niet werkt. Als je in de explorer rechts op een bestand klikt, dan kun je uit het keuze menu kiezen voor: "open timeline". Vs code opent dan een weergave waar je de tijdlijn van het bestand terug kan zien. Wanneer je bijvoorbeeld een bestand opslaat dan maakt vscode een snapshot van het bestand. 

Je kunt de versies in de timeline rechts aanklikken en kun je onder andere kiezen om: versies te vergelijken, een versie in te zien of een versie te herstellen.

#### Een verwijdert bestand terug halen
Als je een bestand perongeluk hebt verwijdert dan kun je deze via vscode weer terug halen. Dit kun je doen door de "command palette" te openen met `ctrl + shift + p` en dan te zoeken op "Local History" en kies je voor "Local History: Find Entry to Restore".