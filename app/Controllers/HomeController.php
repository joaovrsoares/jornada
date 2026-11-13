<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $mesInput = (string) ($_GET['mes'] ?? date('Y-m'));
        $mesAtual = RegistrarController::mesValido($mesInput) ? $mesInput : date('Y-m');

        $usuario = ConfiguracoesController::obterUsuario();
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $todos = $pdo->query(
            'SELECT * FROM registros_ponto WHERE usuario_id = 1 ORDER BY data DESC'
        )->fetchAll();

        $minutosMes = 0;
        $diasMes = 0;
        $saldoAcumulado = 0;
        $ultimaBatida = null;

        foreach ($todos as &$r) {
            $r['minutos_trabalhados'] = RegistrarController::calcularMinutos($r);
            $r['saldo_minutos'] = RegistrarController::calcularSaldoDia($r, $cargaDiaria, $tolerancia);

            if ($r['saldo_minutos'] !== null) {
                $saldoAcumulado += $r['saldo_minutos'];
            }

            if (str_starts_with($r['data'], $mesAtual)) {
                $minutosMes += $r['minutos_trabalhados'];
                $diasMes++;
            }

            if ($ultimaBatida === null) {
                foreach (array_reverse(RegistrarController::CAMPOS_BATIDA, true) as $campo => $rotulo) {
                    if (!empty($r[$campo])) {
                        $ultimaBatida = [
                            'hora' => substr((string) $r[$campo], 0, 5),
                            'rotulo' => $rotulo,
                            'data' => $r['data'],
                        ];
                        break;
                    }
                }
            }
        }
        unset($r);

        $this->view('home/index', [
            'titulo' => 'Dashboard',
            'mesAtual' => $mesAtual,
            'minutosMes' => $minutosMes,
            'diasMes' => $diasMes,
            'saldoAcumulado' => $saldoAcumulado,
            'ultimaBatida' => $ultimaBatida,
            'ultimosRegistros' => array_slice($todos, 0, 5),
            'mapaCalendario' => RegistrarController::obterMapaCalendario($todos),
        ]);
    }

    public function sobre(): void
    {
        $this->view('home/sobre', [
            'titulo' => 'Sobre',
            'versao' => 'dev',
        ]);
    }
}
