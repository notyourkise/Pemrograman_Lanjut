<?php
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../';
    $paths = [
        'controllers/' . $class . '.php',
        'repositories/' . $class . '.php',
        'factories/' . $class . '.php',
        'core/' . $class . '.php',
    ];
    foreach ($paths as $rel) {
        $file = $baseDir . $rel;
        if (is_file($file)) { require_once $file; return true; }
    }
    return false;
});
