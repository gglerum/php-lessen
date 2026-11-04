<?php

declare(strict_types=1);

// een externe woorden API die we niet zelf hebben geschreven en niet kunnen aanpassen
class ThirdPartyWordApi
{
    public function fetchWordsAsJson(): string
    {
        return '{"words": ["kiwi", "watermelon", "apple"]}';
    }
}

class LongestWordPicker
{
    private ThirdPartyWordApi $api;

    public function __construct(ThirdPartyWordApi $api)
    {
        $this->api = $api;
    }

    public function pick(): string
    {
        // de picker moet weten hoe de API werkt en welk formaat hij teruggeeft
        $words = json_decode($this->api->fetchWordsAsJson(), true)['words'];

        $longest = '';

        foreach ($words as $word) {
            if (strlen($word) > strlen($longest)) {
                $longest = $word;
            }
        }

        return $longest;
    }
}

echo (new LongestWordPicker(new ThirdPartyWordApi()))->pick() . "\n";

// de picker is nu vast aan deze API: een andere bron betekent de picker aanpassen
