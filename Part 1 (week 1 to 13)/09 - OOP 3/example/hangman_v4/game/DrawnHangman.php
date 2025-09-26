<?php

namespace Hacklabfrl\Hangman;

/**
 * Class DrawnHangman represents the drawn hangman based on the number of attempts.
 */
class DrawnHangman
{
    /**
     * Returns the drawn hangman based on the number of attempts.
     *
     * @param int $attempts The number of attempts remaining.
     * @return string The drawn hangman.
     */
    public function get(int $attempts): string
    {
        $display = '';
        switch ($attempts) {
            case 6:
                $display = "  +---+\n  |   |\n      |\n      |\n      |\n      |\n=========\n";
                break;
            case 5:
                $display = "  +---+\n  |   |\n  O   |\n      |\n      |\n      |\n=========\n";
                break;
            case 4:
                $display = "  +---+\n  |   |\n  O   |\n  |   |\n      |\n      |\n=========\n";
                break;
            case 3:
                $display = "  +---+\n  |   |\n  O   |\n /|   |\n      |\n      |\n=========\n";
                break;
            case 2:
                $display = "  +---+\n  |   |\n  O   |\n /|\  |\n      |\n      |\n=========\n";
                break;
            case 1:
                $display = "  +---+\n  |   |\n  O   |\n /|\  |\n /    |\n      |\n=========\n";
                break;
            case 0:
                $display = "  +---+\n  |   |\n  O   |\n /|\  |\n / \  |\n      |\n=========\n";
                break;
            default:
                $display = "  +---+\n      |\n      |\n      |\n      |\n      |\n=========\n";
        }
        return $display;
    }
}
