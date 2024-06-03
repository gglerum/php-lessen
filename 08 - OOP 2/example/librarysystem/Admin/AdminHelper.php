<?php

/**
 * The AdminHelper class provides helper methods for the admin functionality.
 */
class AdminHelper
{
    /**
     * Displays the options for a borrowable item.
     *
     * @param Borrowable $item The borrowable item for which to display the options.
     * @return void
     */
    public static function showOptions(Borrowable $item): void
    {
        if (!isset($_SESSION['admin'])) {
            return;
        }
        require_once 'html/options.html';
    }
}
