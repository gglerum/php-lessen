<?php

/**
 * QueryBuilder class
 * This class is responsible for building and executing SQL queries.
 * It provides methods to construct and run various types of database queries.
 */
class QueryBuilder
{

    private string $table;

    private PdoService $pdoService;

    private array $select;

    private array $where;

    private string $className;

    public function __construct(string $className, ?string $table = null)
    {
        $this->pdoService = PdoService::getInstance();
        $this->className = $className;
        $this->table = $table ?? strtolower($className) . 's';
    }

    /**
     * Set the table name for the query.
     *
     * @param string $table The name of the table.
     * @return QueryBuilder Returns the current instance of QueryBuilder.
     */
    public function table(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Selects the specified fields for the query.
     *
     * @param string ...$fields The fields to select.
     * @return QueryBuilder Returns the current instance of QueryBuilder.
     */
    public function select(string ...$fields): QueryBuilder
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * Sets the WHERE clause for the query.
     *
     * @param array $keyValuePairs An associative array of key-value pairs.
     * @return QueryBuilder Returns the current instance of QueryBuilder.
     */
    public function where(array $keyValuePairs): QueryBuilder
    {
        $this->where = $keyValuePairs;
        return $this;
    }

    /**
     * Fetches records from the database.
     *
     * @return array The fetched records as an array of associative arrays.
     */
    public function get(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->select) . ' FROM ' . $this->table;
        if ($this->where) {
            $sql .= ' WHERE ' . implode(' AND ', array_map(fn($key) => "$key = :$key", array_keys($this->where)));
        }
        return $this->pdoService->fetch($sql, array_values($this->where), $this->className);
    }

    /**
     * Inserts a new record into the database.
     * @param array $keyValuePairs
     * @return int
     */
    public function insert(array $keyValuePairs): int
    {
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', array_keys($keyValuePairs)) . ') VALUES (' . implode(', ', array_map(fn($key) => ":$key", array_keys($keyValuePairs))) . ')';
        return $this->pdoService->insert($sql, array_values($keyValuePairs));
    }

    /**
     * Updates a record in the database.
     * @param array $keyValuePairs
     * @return bool
     */
    public function update(array $keyValuePairs): bool
    {
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', array_map(fn($key) => "$key = :$key", array_keys($keyValuePairs))) . ' WHERE ' . implode(' AND ', array_map(fn($key) => "$key = :$key", array_keys($this->where)));
        return $this->pdoService->update($sql, array_merge(array_values($keyValuePairs), array_values($this->where)));
    }
}
