<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

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
}