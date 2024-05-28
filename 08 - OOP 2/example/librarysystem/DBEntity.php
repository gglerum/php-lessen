<?php
require_once 'DbManager.php';
abstract class DBEntity
{
    private DbManager $dbManager;

    protected static string $table = '';

    public static function selectQuery(string $class, string $query, array $params): array
    {
        $dbManager = DbManager::getPdo();
        $results = $dbManager->executeQuery($query, $params);
        if (!$results) {
            return null;
        }
        return array_map(function ($result) use ($class) {
            $builder = call_user_func($class . '::builder');
            foreach ($result as $key => $value) {
                $builder->$key($value);
            }
            return $builder->build();
        }, $results);
    }

    public static function load(int $id): mixed
    {
        $results = DBEntity::selectQuery(static::class, 'SELECT * FROM ' . static::$table . ' where id = ?', [$id]);
        if ($results[0]) {
            return $results[0];
        }
        return null;
    }

    public static function all(): mixed
    {
        return DBEntity::selectQuery(static::class, 'SELECT * FROM ' . static::$table, []);
    }

    public static function insert()
    {
        $dbManager = DbManager::getPdo();
        $keys = array_keys($_POST);
        $keysString = implode(', ', $keys);
        $values = implode(', ', str_split(str_repeat('?', count($keys))));
        $query = 'INSERT INTO ' . static::$table . ' ( ' . $keysString . ' ) VALUES ( ' . $values . ' )';
        $dbManager->executeQuery($query, array_values($_POST));
        return $dbManager->lastInsertId();
    }
}
