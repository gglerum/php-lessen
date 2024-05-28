<?php

namespace Hacklabfrl\Hangman;

/**
 * A enum that represents the different game statuss
 */

//An enum consists of constants without a value, the constants itself is the value
enum GameStatus
{
    case IN_PROGRESS;
    case WON;
    case LOST;
}
