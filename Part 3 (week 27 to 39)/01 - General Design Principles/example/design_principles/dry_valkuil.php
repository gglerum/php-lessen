<?php

declare(strict_types=1);

// deze twee functies zien er hetzelfde uit, maar ze bestaan om een andere reden
function getStudentDiscount(float $price): float
{
    return $price * 0.10;
}

function getSeniorDiscount(float $price): float
{
    return $price * 0.10;
}

// als we ze samenvoegen tot getDiscount() en studenten later 15% krijgen,
// dan krijgen senioren er onbedoeld ook 15% bij, of moeten we een uitzondering inbouwen
// de twee regels veranderen om een andere reden, dus laten we ze los van elkaar

echo getStudentDiscount(50.0) . "\n";
echo getSeniorDiscount(50.0) . "\n";
