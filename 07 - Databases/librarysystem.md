# Bibliotheek Systeem Deel 4
## 7. **Toevoeging van de Klassen `QueryBuilder` en `PDOSingleton`**

In de nieuwe versie van het systeem zijn twee belangrijke klassen toegevoegd: `QueryBuilder` en `PDOSingleton`. Deze aanpassingen zijn gericht op het verbeteren van de database-interactie door middel van abstractie en het vermijden van directe database-toegang binnen de repositories.

## **Nieuwe Klassen**

**`PDOSingleton`**:
- **Beschrijving**:
  - `PDOSingleton` implementeert het Singleton-patroon om één gedeelde PDO-verbinding te beheren.
  - Door deze klasse te gebruiken, wordt ervoor gezorgd dat er slechts één actieve verbinding met de database bestaat gedurende de levensduur van de applicatie.
- **Voordelen**:
  - Vermindert overhead door het hergebruiken van dezelfde PDO-instantie.
  - Vereenvoudigt het beheer van databaseverbindingen.
- **Relatie**:
  - Wordt gebruikt door de `QueryBuilder` voor database-interacties.

**`QueryBuilder`**:
- **Beschrijving**:
  - `QueryBuilder` biedt een flexibele en gestandaardiseerde manier om database-query's te bouwen en uit te voeren.
  - Het maakt gebruik van de PDO-instantie die wordt geleverd door `PDOSingleton`.
- **Voordelen**:
  - Scheidt de logica van database-interacties van repositories.
  - Maakt de code leesbaarder en onderhoudsvriendelijker.
  - Minimaliseert directe SQL-code binnen de repositories.
- **Relatie**:
  - Wordt gebruikt door de `BookRepository` voor het uitvoeren van CRUD-operaties op boeken.

### **Effect op de Structuur**

**Relatie tussen `BookRepository`, `QueryBuilder` en `PDOSingleton`**:
- De `BookRepository` is nu afhankelijk van de `QueryBuilder` in plaats van rechtstreeks met de database te communiceren.
- `QueryBuilder` vertrouwt op de PDO-instantie van `PDOSingleton` voor het uitvoeren van database-query's.

**Voorbeeld Workflow**:
1. Een gebruiker doet een verzoek via een formulier.
2. De `Router` stuurt het verzoek naar de juiste controller.
3. De controller roept de `BookRepository` aan voor database-interacties.
4. De `BookRepository` gebruikt de `QueryBuilder` om een database-query uit te voeren.
5. De `QueryBuilder` verkrijgt een gedeelde PDO-instantie van `PDOSingleton` en voert de query uit.
6. Resultaten worden verwerkt en doorgegeven aan de HTML-template voor weergave.

### **Samenvatting van de Wijzigingen**:
- **Voordelen**:
  - Verbetert de modulariteit en onderhoudbaarheid van het systeem.
  - Zorgt voor een duidelijke scheiding van verantwoordelijkheden.
  - Minimaliseert herhaling en directe afhankelijkheden binnen de code.
- **Impact**:
  - Database-interacties zijn nu veiliger en eenvoudiger te testen.
  - De repositories bevatten minder complexe logica en SQL-query’s, wat zorgt voor schonere code.

Met deze toevoegingen is het systeem beter voorbereid op schaalbaarheid en complexere database-operaties in de toekomst.

---

## 8. **Uitbreiding van de `Book` Klasse met de Methode `toArray`**

De `Book` klasse heeft een nieuwe methode genaamd `toArray` gekregen. Deze methode maakt het mogelijk om een `Book` object eenvoudig om te zetten naar een associatieve array, wat handig is bij het opslaan in een database.

**Details van de `toArray` Methode**:
- **Beschrijving**:
  - Converteert alle relevante properties van een `Book` object naar een key-value structuur.
- **Gebruik**:
  - Wordt gebruikt door de `BookRepository` om objecten eenvoudig te serialiseren voordat ze worden opgeslagen in de database.

**Effect op de Workflow**:
- Wanneer een boek wordt opgeslagen, kan de `toArray` methode worden aangeroepen om een gestandaardiseerde representatie van het object te verkrijgen.
- De gegenereerde array wordt vervolgens doorgegeven aan de `QueryBuilder` voor verwerking in een SQL-query.

