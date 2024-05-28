<?php
namespace Hacklabfrl\Hangman\Generator;

/**
 * Interface used to be able to switch which word source to use
 */
interface WordSource
{
    public function get(): array;
}
