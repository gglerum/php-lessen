<?php
class BorrowHelper
{
    /**
     * Shows borrow html for a borrowable item
     *
     * @param Borrowable $item
     * @return void
     */
    //Polymorphism is used here to show the borrow html for any borrowable item
    public static function showHtml(Borrowable $item): void
    {
        // variable $item is used in the templates
        require_once 'Borrow/html/borrow.html';

        if ($item->isBorrowedByCustomer()) {
            require_once 'Borrow/html/return.html';
        }
    }

    public static function canBorrow()
    {
        return isset($_SESSION['customer']);
    }
}
