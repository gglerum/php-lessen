## Regular Expressions
Met regular expressions kun je controleren of een string overeenkomt met een bepaald patroon. Met deze mogelijkheid kun je een aantal dingen doen.

### Validation
Met [preg_match](https://www.php.net/manual/en/function.preg-match.php) is het mogelijk om te controleren of de invoer van een gebruiker voldoet aan bepaalde regels.

Als de gebruiker vragen om een telefoonnummer in te voeren, dan kunnen we de volgende regular expression gebruiken om te controleren of de invoer wel daadwerkelijk een telefoonnummer is:

```^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$```

Dit ziet er vrij complex uit, ik heb het dan ook niet zelf geschreven. Het internet staat vol met regular expressions voor elke toepassing. Deze is voor Nederlandse telefoon nummers en matcht telefoonnummers die bijvoorbeeld kunnen beginnen met: `+31, +31(0), (+31)(0), 0, 0031` gevolgd door 9 getallen.

We kunnen deze regex als volgt gebruiken:

```PHP
const PHONE_NUMBER_PATTERN = '^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$';

$input = readline('Wat is uw telefoonnummer?');
while(!preg_match(PHONE_NUMBER_PATTERN, $input)){
    $input = readline('Vul alstublieft een geldig telefoonnummer in:');
}
```

Er zijn regular expressions voor postcodes, emailaddressen, isbn nummers, etc. 

Het stelt ons instaat om te voorkomen dat een gebruiker de verkeerde gegevens invult.

Een praktijk voorbeeld kun je hier vinden: [BookManager](./01%20validation/)

### Capturing
Een andere toepassing van `preg_match` is het toepassen van een regex om bepaalde waarden uit een stuk tekst te vissen.

Je kunt dit bijvoorbeeld gebruiken om facturen te verwerken die een klant per e-mail binnen krijgt. Stel dat het totaal bedrag zo in de e-mail staat:

```
TOTAALBEDRAG € 30,12
```

Dan kunnen we het bedrag capturen met de volgende regex:

```
(totaalbedrag|TOTAALBEDRAG)\s*€\s?([\d,\.]+)
```

Met de bovenste regex ga ik er vanuit dat 'totaalbedrag' ook in kleine letters kan staan, en met `([\d,\.]+)` match ik bedragen met zowel de Nederlands als de Engelse notatie voor grote bedragen als kleine bedragen.

Deze gebruiken met met `preg_match` als volgt:
```PHP
const TOTAL_AMOUNT_PATTERN = '(totaalbedrag|TOTAALBEDRAG)\s*€\s?([\d,\.]+)';

$string = 'TOTAALBEDRAG € 30,12';
if(preg_match(TOTAL_AMOUNT_PATTERN, $string, $matches)){
    echo "Het totaal bedrag is $matches[1]";
}
```
```
output:
Het totaal bedrag is 30,12
``` 

We kunnen ook meerdere waardes in één keer capturen. Neem bijvoorbeeld dag, maand en jaar van een datum:

```PHP
const DATE_PATTERN = '(\d{2})-(\d{2})-(\d{4})';

$string = '12-12-2012';

if(preg_match(DATE_PATTERN, $string, $matches)){
    echo "De dag is $matches[1]\n";
    echo "De maand is $matches[2]\n";
    echo "Het jaar is $matches[3]\n";
}
```
```
output:
De dag is 12
De maand is 12
Het jaar is 2012
```

### String Replacement
Je kunt ook regex gebruiken om stukjes tekst te vervangen. Dit doe je met `preg_replace`.
Stel je wilt telefoonnummer censureren, dan kan dat op deze manier.

```PHP
<?php
const PHONE_CENSOR_PATTERN = '/\+[0-9]{2}[0-9]{6}([0-9]{3})/';
$result = preg_replace(PHONE_CENSOR_PATTERN, '*********$1', '+31629460763');
echo $result;
```
```
output:
*********763
```