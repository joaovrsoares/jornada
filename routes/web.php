<?php

declare(strict_types=1);

use App\Controllers\ConfiguracoesController;
use App\Controllers\HomeController;
use App\Controllers\JornadaController;
use App\Controllers\RegistrarController;
use App\Controllers\RelatoriosController;
use App\Core\Router;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/jornada', [JornadaController::class, 'index']);
$router->get('/jornada/editar', [RegistrarController::class, 'editarDia']);
$router->post('/jornada/editar', [RegistrarController::class, 'salvarDia']);

$router->get('/registrar', [RegistrarController::class, 'index']);
$router->post('/registrar', [RegistrarController::class, 'salvar']);
$router->post('/registrar/ocorrencia', [RegistrarController::class, 'salvarOcorrencia']);
$router->post('/registrar/excluir', [RegistrarController::class, 'excluir']);
$router->post('/registrar/remover-foto', [RegistrarController::class, 'removerFoto']);

$router->get('/comprovante', [RegistrarController::class, 'comprovante']);

$router->get('/relatorios', [RelatoriosController::class, 'index']);
$router->get('/relatorios/exportar', [RelatoriosController::class, 'exportar']);

$router->get('/configuracoes', [ConfiguracoesController::class, 'index']);
$router->post('/configuracoes', [ConfiguracoesController::class, 'salvar']);
