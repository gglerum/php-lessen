<?php

declare(strict_types=1);

// onze eigen afspraak, zoals in les 03
interface WordSource
{
    public function get(): array;
}

// de externe API blijft zoals hij is
class ThirdPartyWordApi
{
    public function fetchWordsAsJson(): string
    {
        return '{"words": ["kiwi", "watermelon", "apple"]}';
    }
}

// de adapter past de API aan op onze interface, alle kennis over de API zit in deze class
class ApiWords implements WordSource
{
    private ThirdPartyWordApi $api;

    public function __construct(ThirdPartyWordApi $api)
    {
        $this->api = $api;
    }

    public function get(): array
    {
        return json_decode($this->api->fetchWordsAsJson(), true)['words'];
    }
}

class LongestWordPicker
{
    private WordSource $source;

    public function __construct(WordSource $source)
    {
        $this->source = $source;
    }

    public function pick(): string
    {
        $longest = '';

        foreach ($this->source->get() as $word) {
            if (strlen($word) > strlen($longest)) {
                $longest = $word;
            }
        }

        return $longest;
    }
}

// de picker weet niets van de API of van JSON
echo (new LongestWordPicker(new ApiWords(new ThirdPartyWordApi())))->pick() . "\n";
