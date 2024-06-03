<?php
require_once 'Data/DbManager.php';
/**
 * Is used in conjunction with the Borrowable interface to provide the functionality of borrowing and returning items
 
 */
trait Borrow
{
    private int|null $copies = null;

    /**
     * Get the number of copies available of the item. Cache the copies for the current request
     *
     * @return integer
     */
    public function copies(): int
    {
        if (!$this->copies) {
            $result = DbManager::getPdo()->executeQuery('select count(id) from available where type = ? and item_id = ? and status = 1', [strtolower(static::class), $this->id]);
            $this->copies = $result[0]['count(id)'];
        }
        return $this->copies;
    }

    /**
     * Check if the item is available
     *
     * @return boolean
     */
    public function isAvailable(): bool
    {
        return $this->copies() > 0;
    }

    /**
     * Updates the database to show that the item has been borrowed
     *
     * @return void
     */
    public function borrowItem(): void
    {
        if ($this->isAvailable()) {
            //insert the borrowed item into the customer_borrowed table
            DbManager::getPdo()->executeQuery('insert into customer_borrowed (type, item_id, customer_id) values (?, ?, ?)', [strtolower(static::class), $this->id, $_SESSION['customer']->id]);
            //update the status of the first available copy to 0 (not available)
            DbManager::getPdo()->executeQuery('update available set status = 0 where type = ? and item_id = ? limit 1', [strtolower(static::class), $this->id]);
        }
    }

    /**
     * Updates the database to show that the item has been returned
     *
     * @return void
     */
    public function returnItem(): void
    {
        if (!$this->isAvailable()) {
            //update the return date of the first borrowed copy
            DbManager::getPdo()->executeQuery('UPDATE customer_borrowed SET returned_at = NOW() WHERE type = ? AND item_id = ? AND customer_id = ? AND returned_at IS NULL LIMIT 1', [strtolower(static::class), $this->id, $_SESSION['customer']->id]);
            //update the status of the first available copy to 1 (available)
            DbManager::getPdo()->executeQuery('UPDATE available SET status = 1 WHERE type = ? AND item_id = ? limit 1', [strtolower(static::class), $this->id]);
        }
    }

    public function isBorrowedByCustomer(): bool
    {
        if (!isset($_SESSION['customer'])) {
            return false;
        }
        $result = DbManager::getPdo()->executeQuery('SELECT count(id) FROM customer_borrowed WHERE type = ? AND item_id = ? AND customer_id = ? AND returned_at is null', [strtolower(static::class), $this->id, $_SESSION['customer']->id]);
        return $result[0]['count(id)'] > 0;
    }
}
