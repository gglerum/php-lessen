<?php

namespace Hacklabfrl\Hangman\Utils;

/**
 * The Env class is responsible for loading and retrieving environment variables.
 */
class Env
{

    /**
     * @var array|false $env The array containing the environment variables or false if the .env file cannot be parsed.
     */
    private array|false $env;

    /**
     * Private constructor to prevent direct instantiation of the class.
     * The constructor loads the environment variables from the .env file.
     */
    private function __construct()
    {
        $this->env = parse_ini_file('.env');
    }

    /**
     * Loads the environment variables from the .env file and returns an instance of the Env class.
     *
     * @return Env An instance of the Env class.
     */
    public static function load(): Env
    {
        return new self();
    }

    /**
     * Retrieves the value of the specified environment variable.
     *
     * @param string $key The name of the environment variable.
     * @return string|false The value of the environment variable if it exists, false otherwise.
     */
    public function get(string $key): string|false
    {
        $ucKey = strtoupper($key);
        if ($this->env && array_key_exists($ucKey, $this->env)) {
            return $this->env[strtoupper($ucKey)];
        }
        return false;
    }
}
