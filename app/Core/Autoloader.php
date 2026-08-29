<?php

# Autoloader: carrega automaticamente as classes do namespace App para que não seja necessário usar require_once em cada arquivo

declare(strict_types=1);

spl_autoload_register(function (string $class):void {
    $prefix = 'App\\';
    
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));

    $file = __DIR__ . '/../' . str_replace(
        '\\',
        '/',
        $relativeClass
    ) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});