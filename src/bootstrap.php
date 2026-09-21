<?php

// Bootstrap e Autoloader PSR-4 para App, Importer e Tests

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/',
        'Importer\\' => __DIR__ . '/../importer/',
        'Tests\\' => __DIR__ . '/../tests/'
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

\App\Config\Env::load();

