<?php

/**
 * ItemRepository is responsible for handling database operations for the Item model.
 */
class ItemRepository
{
    protected QueryBuilder $queryBuilder;

    public function __construct(string $className)
    {
        $this->queryBuilder = new QueryBuilder($className);
    }

    /**
     * Adds a new item to the database.
     * @param Item $item The item to add.
     * @return int The ID of the newly created item.
     */
    public function add(Item $item): int
    {
        return $this->queryBuilder->insert($item->toArray());
    }

    /**
     * Retrieves a item from the database by ID.
     * @param int $id The ID of the item to retrieve.
     * @return array The item or items with the specified ID(s).
     */
    public function get(int ...$ids): array|Item
    {
        $items = $this->queryBuilder->select('*')->whereIn(['id' => $ids])
            ->get();

        if (count($ids) === 1) {
            return $items[0];
        }
        return $items;
    }

    /**
     * Retrieves all items from the database.
     * @return array An array of all items in the database.
     */
    public function getAll(): array
    {
        return $this->queryBuilder->where([])->select('*')->get();
    }
}
