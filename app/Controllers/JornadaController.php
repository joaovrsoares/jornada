<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class JornadaController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();

        $mesInput = (string) ($_GET['mes'] ?? date('Y-m'));
        $mes = RegistrarController::mesValido($mesInput) ? $mesInput : date('Y-m');

        $usuario = ConfiguracoesController::obterUsuario();
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $stmtMes = $pdo->prepare(
            'SELECT * FROM registros_ponto
             WHERE usuario_id = 1 AND data LIKE :mes
             ORDER BY data DESC'
        );
        $stmtMes->execute(['mes' => $mes . '-%']);
        $registros = $stmtMes->fetchAll();

        $totalTrabalhadoMes = 0;
        $saldoMes = 0;

        foreach ($registros as &$r) {
            $r['minutos_trabalhados'] = RegistrarController::calcularMinutos($r);
            $r['saldo_minutos'] = RegistrarController::calcularSaldoDia($r, $cargaDiaria, $tolerancia);

            $totalTrabalhadoMes += $r['minutos_trabalhados'];
            if ($r['saldo_minutos'] !== null) {
                $saldoMes += $r['saldo_minutos'];
            }
        }
        unset($r);

        $this->view('jornada/index', [
            'titulo' => 'Minha jornada',
            'mes' => $mes,
            'cargaDiaria' => $cargaDiaria,
            'tolerancia' => $tolerancia,
            'totalTrabalhadoMes' => $totalTrabalhadoMes,
            'saldoMes' => $saldoMes,
            'registros' => $registros,
            'mapaCalendario' => RegistrarController::obterMapaCalendario(),
        ]);
    }
}
