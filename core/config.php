<?php

class Config
{
    public static function loadConfig()
    {
        $path = BASE_PATH . '.env';
        if (!file_exists($path))
        {
            Logger::warning("Configuration file .env not found!");
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line)
        {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Process key-value pairs
            if (!strpos($line, '=')) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Set environment variable
            putenv("$key=$value");
        }
    }
}

Config::loadConfig();