<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'titulo' => 'Dashboard',
            'mensagem'=> 'Controle de jornada de trabalho.'
        ]);
    }

    public function sobre(): void
    {
        $this->view('home/sobre', [
            'titulo' => 'Sobre',
            'versao'=> 'dev'
        ]);
    }

    public function banco(): void
    {
        $pdo = Database::connection();

        $resultado = $pdo->query('SELECT 1')->fetchColumn();

        echo 'MariaDB respondeu: ' . $resultado;
    }
}