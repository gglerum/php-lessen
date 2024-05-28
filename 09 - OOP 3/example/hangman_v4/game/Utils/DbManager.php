<?php

namespace Hacklabfrl\Hangman\Utils;

use \PDO;

/**
 * Class DbManager
 * 
 * This class is responsible for managing the database connection and executing queries.
 */
class DbManager
{
    private static $dbm;

    private PDO $pdo;

    public function __construct()
    {
        //we load the environment variables, so we don't hardcode any sensitive information
        $env = Env::load();

        //this is the dsn, the string that is used to tell PDO which driver, host and database to use
        $dataSourceName = "mysql:host={$env->get('db_host')};dbname={$env->get('db_name')}";
        //options for the PDO connection
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        //we create a new pdo object we can use to connect to the database.
        $this->pdo = new PDO($dataSourceName, $env->get('db_user'), $env->get('db_pass'), $options);
    }

    /**
     * Get the PDO object. Using the Singleton pattern we only have one PDO connection object during a request
     *
     * @return DbManager
     */
    public static function getPdo(): DbManager
    {
        if (self::$dbm === null) {
            self::$dbm = new self();
        }
        return self::$dbm;
    }

    /**
     * Executes a database query with the given query and parameters.
     *
     * @param string $query The SQL query to execute.
     * @param array $parameters The parameters to bind to the query.
     * @return array The result of the query as an array.
     */
    public function executeQuery(string $query, array $parameters): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }
}
