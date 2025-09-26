## Variabele en functie naamgeving 

### Variabelen 

In PHP beginnen allen variabelen met een `$` vervolgd met een letter of een underscore. De overige karakters 

mogen letters, cijfers, of underscores zijn. 

```PHP 

$_a1_; // is een geldige variabel naam 

``` 

Voor het benoemen van variabelen in PHP houden we ons aan camelCase notatie. Dit betekent dat de 

eerste letter met een kleine letter begint en de eerste letter van elk volgend woord met een hoofdletter begint. 

```PHP 

$numberOfStudents; // camelCase notatie 

``` 

Variabelen mogen afgekort worden als er van de initialisatie valt af te leiden wat de variabele betekent. 

```PHP 

$fh = fopen("/tmp/debug", "a"); 

``` 

Anders is het beter om de variabele een duidelijke naam te geven. 

```PHP 

$interestRate = 0.05; // het is van de naam af te leiden waar het voor bedoeld is 

``` 

*Let op: PHP is case-sensitive: `$numberofstudents` en `$numberOfStudents` zijn twee verschillende variabelen.* 

 

### Functies 

De naamgeving voor functies volgt dezelfde regels als voor variabelen, met de uitzondering dat functies niet 

hoeven te starten met een `$`. 

 

Een vuist regel voor functies is dat de naam van de functie een omschrijving op zichzelf is van wat de functie 

precies doet. Als de functie naam te lang is, dan doet de functie waarschijnlijk te veel. Vooral als je jezelf 

er op betrapt `or` of `and` te gebruiken om woorden aan elkaar te plakken. 

 

```PHP 

function getSumOfAllEvenNumbersBetween(int $start, int $end): int 

{ 

    $sum = 0; 

    for ($i = $start; $i <= $end; $i++) { 

        if ($i % 2 == 0) { 

            $sum += $i; 

        } 

    } 

    return $sum; 

} 

``` 

 