<?php

declare(strict_types=1);

namespace App;

/**
 * Configuration management class that handles environment variables.
 * 
 * This class provides functionality to load and access environment variables
 * from a .env file. It implements a simple singleton pattern to ensure
 * the .env file is only loaded once during the application lifecycle.
 */
class Config
{
    /** 
     * @var array<string, string> Cached environment variables
     */
    private static array $env = [];

    /**
     * @var string The name of the environment file
     */
    private const ENV_FILE = '.env';

    /**
     * Loads environment variables from the .env file.
     * 
     * This method:
     * 1. Checks if variables are already loaded (singleton pattern)
     * 2. Locates and validates the .env file
     * 3. Parses the file line by line, skipping comments
     * 4. Stores variables in memory for subsequent access
     * 
     * @throws \RuntimeException If the .env file is not found
     */
    private static function load(): void
    {
        // Return early if already loaded
        if (!empty(self::$env)) {
            return;
        }

        // Construct path to .env file
        $envFile = dirname(__DIR__) . '/' . self::ENV_FILE;
        
        // Validate file existence
        if (!file_exists($envFile)) {
            throw new \RuntimeException(
                '.env file not found. Copy .env.example to .env and update the values.'
            );
        }

        // Read and parse the file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments
            if (str_starts_with($line, '#')) {
                continue;
            }

            // Parse and store variables
            list($name, $value) = explode('=', $line, 2);
            self::$env[trim($name)] = trim($value);
        }
    }

    /**
     * Retrieves an environment variable value.
     * 
     * @param string $key The environment variable name to retrieve
     * @param string|null $default Optional default value if the variable is not found
     * @return string|null The value of the environment variable or the default value
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        self::load();
        return self::$env[$key] ?? $default;
    }
}
