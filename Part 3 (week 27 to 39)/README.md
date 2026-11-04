# Part 3: Design principles

In Part 1 en 2 hebben we geleerd hoe je code schrijft die werkt. In dit deel leren we hoe je code schrijft die ook werkt als de applicatie groeit, er meer mensen aan werken en de eisen veranderen. We leren dit aan de hand van voorbeelden uit Part 1 (Galgje en het bibliotheek systeem) en een Laravel voorbeeld.

Het gaat niet om regels uit je hoofd leren. Het gaat erom dat je kunt zien waarom een stuk code lastig te veranderen is en weet hoe je dit verbetert.

## Onderwerpen

1. [General Design Principles](01%20-%20General%20Design%20Principles/): KISS, DRY, YAGNI en Separation of Concerns.
2. [Clean Code](02%20-%20Clean%20Code%20Principles/): namen, functies en comments die duidelijk maken wat de code doet, plus Least Astonishment en premature optimization.
3. [SOLID](03%20-%20SOLID/): vijf principes voor het opzetten van classes en interfaces, verdeeld over twee lessen.
4. **GRASP**: bepalen in welke class een stuk code thuishoort.
5. **Refactoring**: stap voor stap een rommelige controller omzetten naar schone code.
6. **Design Patterns**: bekende oplossingen voor terugkerende problemen, en wanneer je ze juist niet gebruikt.
7. **Domain Driven Design**: je code opzetten rondom het probleem dat de applicatie oplost.

## Voorbeeld
In de map [example](./example/) staat een Laravel voorbeeld waarin gebruikers ingevulde sjablonen voor een vragenlijst uploaden. De map `bad` bevat een controller die bijna alle principes uit dit deel overtreedt, de map `better` bevat dezelfde functionaliteit na refactoring. We kijken er in meerdere lessen naar terug.

## Status
Dit deel wordt les voor les herschreven. Totdat een onderwerp is vervangen kan de map nog oude tekst bevatten.
