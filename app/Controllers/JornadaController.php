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
            $r = RegistrarController::enriquecerRegistro($r, $cargaDiaria, $tolerancia);

            $totalTrabalhadoMes += $r['minutos_trabalhados'];
            if ($r['saldo_minutos'] !== null) {
                $saldoMes += $r['saldo_minutos'];
            }
        }
        unset($r);

        $hoje = date('Y-m-d');
        $porData = [];
        foreach ($registros as $r) {
            $porData[(string) $r['data']] = $r;
        }

        // O espelho mostra o mês inteiro: dias lançados, dias úteis em aberto,
        // fins de semana e dias que ainda vão chegar.
        $dias = [];
        $primeiroTs = strtotime($mes . '-01');
        $ultimoDia = (int) date('t', $primeiroTs);

        for ($d = 1; $d <= $ultimoDia; $d++) {
            $data = sprintf('%s-%02d', $mes, $d);
            $ts = strtotime($data);
            $diaSemana = (int) date('w', $ts);
            $registro = $porData[$data] ?? null;

            if ($registro !== null) {
                $estado = 'lancado';
            } elseif ($diaSemana === 0 || $diaSemana === 6) {
                $estado = 'fim_semana';
            } elseif ($data > $hoje) {
                $estado = 'futuro';
            } else {
                $estado = 'sem_lancamento';
            }

            $dias[] = [
                'data' => $data,
                'dia' => date('d/m', $ts),
                'dia_semana' => RegistrarController::DIAS_SEMANA[$diaSemana],
                'estado' => $estado,
                'rotulo_estado' => match ($estado) {
                    'futuro' => 'Ainda por vir',
                    'fim_semana' => 'Fim de semana',
                    default => 'Sem lançamento',
                },
                'registro' => $registro,
                'eh_hoje' => $data === $hoje,
            ];
        }

        $this->view('jornada/index', [
            'titulo' => 'Espelho do mês',
            'mes' => $mes,
            'cargaDiaria' => $cargaDiaria,
            'tolerancia' => $tolerancia,
            'totalTrabalhadoMes' => $totalTrabalhadoMes,
            'saldoMes' => $saldoMes,
            'registros' => $registros,
            'dias' => $dias,
            'mapaCalendario' => RegistrarController::obterMapaCalendario(),
        ]);
    }
}
