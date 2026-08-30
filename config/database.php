<?php

declare(strict_types=1);

return [
    'host' => $_ENV['DB_HOST'] ?? 'db',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? 'jornada',
    'username' => $_ENV['DB_USERNAME'] ?? 'jornada',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
];