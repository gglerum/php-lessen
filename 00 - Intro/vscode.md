## VS Code

### Code Style
De PHP extensie van DEVSENSE past de PSR-12 standaard toe voor het formateren van je code. Dit is de code style die wij voor PHP over het algemeen hanteren.

#### Auto format on save


### Code Completion
`ctrl + spatie`

https://code.visualstudio.com/docs/editor/intellisense

### Local History
Het is de bedoeling dat we met Git gaan werken. Git zorgt er voor dat als je code kwijt raakt, dat je deze code
via Git weer terug kunt halen. Helaas werkt dit alleen met code die je gepushed hebt.

Gelukkig is er een oplossing om code terug te halen wanneer `ctrl + z` niet werkt. Als je in de explorer rechts op een bestand klikt, dan kun je uit het keuze menu kiezen voor: "open timeline". Vs code opent dan een weergave waar je de tijdlijn van het bestand terug kan zien. Wanneer je bijvoorbeeld een bestand opslaat dan maakt vscode een snapshot van het bestand. 

Je kunt de versies in de timeline rechts aanklikken en kun je onder andere kiezen om: versies te vergelijken, een versie in te zien of een versie te herstellen.

#### Een verwijdert bestand terug halen
Als je een bestand perongeluk hebt verwijdert dan kun je deze via vscode weer terug halen. Dit kun je doen door de "command palette" te openen met `ctrl + shift + p` en dan te zoeken op "Local History" en kies je voor "Local History: Find Entry to Restore".