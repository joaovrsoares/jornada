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
        # Verifica se a rota existe
        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);

            echo "Página não encontrada.";
            
            return;
        }

        # Obtém o controlador e o método da rota
        [$controllerClass, $controllerMethod] =
            $this->routes[$method][$uri];
        
        $controller = new $controllerClass();

        # Verifica se o método existe no controlador
        if (!method_exists($controller, $controllerMethod)) {
            throw new \RuntimeException(
                "Método {$controllerMethod} não encontrado no controlador {$controllerClass}."
            );
        }

        # Chama o método do controlador
        $controller->$controllerMethod();
    }
}