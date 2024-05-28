<?php
require_once 'DbManager.php';
/**
 * This class is responsible for getting entities from the database
 **/
abstract class DBEntity
{
    private DbManager $dbManager;

    /**
     * The static string can be set in the child class to define the table name
     *
     * @var string
     */
    protected static string $table = '';

    /**
     * Exeutes a query that expects a resultset
     *
     * @param string $class the class used to build the result
     * @param string $query the query to execute
     * @param array $params the parameters to bind to the query
     * @return array the resulting items
     */
    public static function selectQuery(string $class, string $query, array $params): array|null
    {
        $dbManager = DbManager::getPdo();
        $results = $dbManager->executeQuery($query, $params);
        if (!$results) {
            return null;
        }
        // map the results to objects of $class, by invoking the builder and its methods
        return array_map(function ($result) use ($class) {
            $builder = call_user_func($class . '::builder');
            foreach ($result as $key => $value) {
                //set the property through the builder
                $builder->$key($value);
            }
            return $builder->build();
        }, $results);
    }

    /**
     * Loads an entity from the database
     *
     * @param integer $id of the entity to load
     * @return mixed the entity or null if not found
     */
    public static function load(int $id): mixed
    {
        $results = DBEntity::selectQuery(static::class, 'SELECT * FROM ' . static::$table . ' where id = ?', [$id]);
        if ($results[0]) {
            return $results[0];
        }
        return null;
    }

    /**
     * Fetches all entities from the database
     *
     * @return array the entities
     */
    public static function all(): array
    {
        $result = DBEntity::selectQuery(static::class, 'SELECT * FROM ' . static::$table, []);
        return $result ?: [];
    }

    /**
     * Inserts a single entity into the database
     *
     * @return void
     */
    public static function insert()
    {
        $dbManager = DbManager::getPdo();
        $keys = array_keys($_POST);
        //for the prepared statement we need use the keys for the column names
        $keysString = implode(', ', $keys);
        //we need to create a string for values with the same amount of ? as we have keys
        $values = implode(', ', str_split(str_repeat('?', count($keys))));
        $dbManager->executeQuery('INSERT INTO ' . static::$table . ' ( ' . $keysString . ' ) VALUES ( ' . $values . ' )', array_values($_POST));
        return $dbManager->lastInsertId();
    }
}
