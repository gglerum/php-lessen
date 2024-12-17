<?php

/**
 * Class PdoService
 * This class provides a PDO service for interacting with a MySQL database.
 */
class PdoService
{
    private $host = '127.0.0.1:3306';
    private $db   = 'mariadb';
    private $charset = 'utf8mb4';

    private $pdo;

    private static $pdoService;

    /**
     * PdoService constructor.
     * Initializes a new instance of the PdoService class.
     */
    public function __construct()
    {
        $env = parse_ini_file('.env');

        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $env['dbuser'], $env['dbpass'], $options);
    }

    /**
     * Get the singleton instance of the PdoService class.
     * @return PdoService The singleton instance of the PdoService class.
     */
    public static function getInstance()
    {
        if (!isset(self::$pdoService)) {
            self::$pdoService = new PdoService();
        }
        return self::$pdoService;
    }

    /**
     * Insert a new record into the database.
     * @param string $sql The SQL query to execute.
     * @param array $values The values to bind to the query.
     * @return int The ID of the last inserted record.
     */
    public function insert(string $sql, array $values): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $this->pdo->lastInsertId();
    }

    /**
     * Fetch a single record from the database.
     * @param int $id The ID of the record to fetch.
     * @param string $table The name of the table to fetch from.
     * @return array The fetched record as an associative array.
     */
    public function fetch(string $sql, array $where, string $className): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($where);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className);
        return $stmt->fetchAll();
    }

    /**
     * Update a record in the database.
     * @param string $sql
     * @param array $values
     * @return bool
     */
    public function update(string $sql, array $values): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
}
