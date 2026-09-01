<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Core\Router;

$router = new Router();

$router->get(
    '/',
    [HomeController::class, 'index']
);

$router->get(
    '/sobre',
    [HomeController::class, 'sobre']
);