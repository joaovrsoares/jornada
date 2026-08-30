<?php

# Autoloader: carrega automaticamente as classes do namespace App para que não seja necessário usar require_once em cada arquivo

declare(strict_types=1);

spl_autoload_register(function (string $class):void {
    $prefix = 'App\\';
    
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));

    # Converte o namespace em caminho de diretório
    $file = __DIR__ . '/../' . str_replace(
        '\\',
        '/',
        $relativeClass
    ) . '.php';

    # Se o arquivo existir, carrega a classe
    if (file_exists($file)) {
        require_once $file;
    }
});