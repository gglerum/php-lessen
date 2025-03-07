<?php
/*
* Validation
*/
const PHONE_NUMBER_PATTERN = '/(^\+[0-9]{2}$|^\+[0-9]{2}\(0\)$|^\(\+[0-9]{2}\)\(0\)$|^00[0-9]{2}$|^\(0\)[0-9]{9}$|^[0-9\-\s]{10}$)/';
const EMAIL_PATTERN = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

//ask for email
// $input = readline('Wat is uw telefoonnummer?: ');
// while (!preg_match(PHONE_NUMBER_PATTERN, $input)) {
//     $input = readline('Vul alstublieft een geldig telefoonnummer in: ');
// }
// echo $input . "\n";

//ask for phonenumber
// $input = readline('Wat is uw e-mailadres?: ');
// while (!preg_match(EMAIL_PATTERN, $input)) {
//     $input = readline('Vul alstublieft een geldig e-mailadres in: ');
// }
// echo $input . "\n";

/*
* Capturing
*/
const TOTAL_AMOUNT_PATTERN = '/(totaalbedrag|TOTAALBEDRAG)\s*€\s?([\d,\.]+)/';
const DATE_PATTERN = '/(\d{2})-(\d{2})-(\d{4})/';

//amount
// $string = 'TOTAALBEDRAG € 30,12';
// if (preg_match(TOTAL_AMOUNT_PATTERN, $string, $matches)) {
//     echo "Het totaal bedrag is $matches[2]\n";
// }

//parts of a date
// $string = '15-12-2012';
// if (preg_match(DATE_PATTERN, $string, $matches)) {
//     echo "De dag is $matches[1]\n";
//     echo "De maand is $matches[2]\n";
//     echo "Het jaar is $matches[3]\n";
// }
