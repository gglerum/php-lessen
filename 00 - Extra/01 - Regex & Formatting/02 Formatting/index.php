<?php

//double quotes
$getal = 1;
$tekst = "Variabelen in string $getal
test2
test4
test3";
$tekst2 = "expressie werken niet {($getal + $getal)}";

//echo $tekst . "\n";
//echo $tekst2;

//sprintf
//echo sprintf("String met placeholders %s en %s\n", "test", "test2");
//echo sprintf('String met placeholders %1$s en %1$s', "test2");
//echo sprintf('String met placeholders %2$s en %1$s', "test2", "test3");
//echo sprintf("Werken met decimalen %2.2f\n", 1.23456789);
//echo sprintf("Werken met padding %20d test\n", 123456789);
//echo sprintf("Werken met padding %-20d test\n", 123456789);
//echo sprintf("Werken met padding %'.20d test\n", 123456789);

//heredoc
$tekst = <<<STR
test
$getal
"kan ook met quotes"
'geen escape characters nodig'
test
STR;

echo $tekst;
