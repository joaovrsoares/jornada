<?php

declare(strict_types=1);

return [
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'public_url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
];