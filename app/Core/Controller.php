<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller {
    protected function view(string $view, array $data = []): void {
        extract($data);
        ob_start(); # Segura o conteúdo da view para renderizar o layout "principal.php"
        require __DIR__ . '/../Views/' . $view . '.php';
        $conteudo = ob_get_clean(); # Pega o conteúdo da view e guarda na variável $conteudo para ser exibido no layout "principal.php"
        require __DIR__ . '/../Views/layouts/principal.php';
    }
}