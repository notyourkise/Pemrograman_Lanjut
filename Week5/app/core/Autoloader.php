<?php
spl_autoload_register(function ($class) {
    // Simple autoloader for app/* classes without namespaces
    $baseDir = __DIR__ . '/../';

    $paths = [
        'controllers/' . $class . '.php',
        'models/' . $class . '.php',
        'core/' . $class . '.php',
    ];

    foreach ($paths as $rel) {
        $file = $baseDir . $rel;
        if (is_file($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});
