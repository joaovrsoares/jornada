<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/../app/Core/Autoloader.php';

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Env;
use App\Core\Router;

Env::load(__DIR__ . '/../.env');

Auth::iniciar();

$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($caminho !== '/') {
    $caminho = rtrim($caminho, '/');
}

# Rotas acessíveis sem sessão. As demais exigem uma conta autenticada.
$rotasPublicas = ['/login', '/cadastro'];

if (!in_array($caminho, $rotasPublicas, true) && !Auth::check()) {
    header('Location: /login');
    exit;
}

# Protege todos os envios de formulário contra CSRF.
if ($metodo === 'POST') {
    Csrf::exigir();
}

$router->dispatch($metodo, $_SERVER['REQUEST_URI'] ?? '/');
