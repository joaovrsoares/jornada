<?php

declare(strict_types=1);

namespace App\Core;

class Env {
    public static function load(string $file): void {
        if (!file_exists($file)) {
            throw new RuntimeException("Arquivo .env não encontrado.");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            $_ENV[$key] = $value;
        }
    }
}
