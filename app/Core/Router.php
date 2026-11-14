<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];

    # Adiciona uma rota GET
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    # Adiciona uma rota POST
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void {
        # Extrai apenas o caminho da URI, ignorando a query string (ex: /sobre?teste=1 -> /sobre)
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        # Normaliza barras finais (ex: /sobre/ -> /sobre), mantendo apenas a raiz "/"
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        # Verifica se a rota existe
        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);

            echo "Página não encontrada.";

            return;
        }

        # Obtém o controlador e o método da rota
        [$controllerClass, $controllerMethod] =
            $this->routes[$method][$path];
        
        $controller = new $controllerClass();

        # Chama o método do controlador
        $controller->$controllerMethod();
    }
}