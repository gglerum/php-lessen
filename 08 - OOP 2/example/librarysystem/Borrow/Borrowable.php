<?php

/**
 * Is used in conjunction with the Borrow trait to provide the functionality of borrowing and returning items
 */
interface Borrowable
{
    public function copies(): int;
    public function isAvailable(): bool;
    public function borrowItem(): void;
    public function returnitem(): void;
    public function isBorrowedByCustomer(): bool;
}
