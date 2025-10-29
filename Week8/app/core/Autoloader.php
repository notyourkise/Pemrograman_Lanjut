<?php

/**
 * Week 8: Autoloader
 * PSR-4 compliant autoloader for the application
 */

class Autoloader
{
    private static $directories = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../repositories/',
        __DIR__ . '/../helpers/',
        __DIR__ . '/../middleware/',
        __DIR__ . '/../core/',
    ];

    /**
     * Register the autoloader
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'load']);
    }

    /**
     * Load a class file
     * 
     * @param string $className
     */
    private static function load($className)
    {
        foreach (self::$directories as $directory) {
            $file = $directory . $className . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
}
