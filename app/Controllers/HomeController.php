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
        $hoje = date('Y-m-d');
        $mesAtual = date('Y-m');

        $usuario = ConfiguracoesController::obterUsuario();
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $todos = $pdo->query(
            'SELECT * FROM registros_ponto WHERE usuario_id = 1 ORDER BY data DESC'
        )->fetchAll();

        $registrosMes = [];
        $minutosMes = 0;
        $diasMes = 0;
        $saldoMes = 0;
        $saldoAcumulado = 0;

        foreach ($todos as &$r) {
            $r['minutos_trabalhados'] = RegistrarController::calcularMinutos($r);
            $r['saldo_minutos'] = RegistrarController::calcularSaldoDia($r, $cargaDiaria, $tolerancia);

            if ($r['saldo_minutos'] !== null) {
                $saldoAcumulado += $r['saldo_minutos'];
            }

            if (str_starts_with((string) $r['data'], $mesAtual)) {
                $registrosMes[] = $r;
                $minutosMes += $r['minutos_trabalhados'];
                $diasMes++;
                if ($r['saldo_minutos'] !== null) {
                    $saldoMes += $r['saldo_minutos'];
                }
            }
        }
        unset($r);

        $registroHoje = RegistrarController::REGISTRO_VAZIO + [
            'data' => $hoje,
            'minutos_trabalhados' => 0,
            'saldo_minutos' => null,
        ];

        $saldoAnteriorMes = 0;
        foreach ($registrosMes as $r) {
            if ($r['data'] === $hoje) {
                $registroHoje = $r;
            } elseif ($r['saldo_minutos'] !== null) {
                $saldoAnteriorMes += $r['saldo_minutos'];
            }
        }

        $proximaBatida = RegistrarController::proximaBatida($registroHoje);

        $this->view('home/index', [
            'titulo' => 'Painel',
            'hoje' => $hoje,
            'mesAtual' => $mesAtual,
            'registroHoje' => $registroHoje,
            'proximaBatida' => $proximaBatida,
            'statusHoje' => RegistrarController::statusComprovacao($registroHoje),
            'assistente' => RegistrarController::calcularAssistenteDia($registroHoje, $cargaDiaria, $saldoAnteriorMes),
            'minutosMes' => $minutosMes,
            'diasMes' => $diasMes,
            'saldoMes' => $saldoMes,
            'saldoAcumulado' => $saldoAcumulado,
            'semana' => self::montarSemana($todos, $hoje),
            'pendencias' => self::montarPendencias(self::indexarPorData($registrosMes), $mesAtual, $hoje),
        ]);
    }

    private static function indexarPorData(array $registros): array
    {
        $porData = [];
        foreach ($registros as $r) {
            $porData[(string) $r['data']] = $r;
        }

        return $porData;
    }

    /**
     * Dias úteis da semana corrente (segunda a sexta) com o estado de cada um:
     * concluído, em aberto, ocorrência, falta ou ainda por vir.
     *
     * @param array<int, array<string, mixed>> $registros
     * @return array{inicio: string, fim: string, dias: array<int, array{data: string, dia_semana: string, dia: string, estado: string, registro: ?array<string, mixed>, url: string}>}
     */
    private static function montarSemana(array $registros, string $hoje): array
    {
        $porData = self::indexarPorData($registros);
        $inicioTs = strtotime('monday this week', strtotime($hoje));
        $dias = [];

        for ($i = 0; $i < 5; $i++) {
            $ts = strtotime("+{$i} day", $inicioTs);
            $data = date('Y-m-d', $ts);
            $registro = $porData[$data] ?? null;
            $estado = 'sem_lancamento';

            if ($registro !== null) {
                $tipo = (string) ($registro['tipo'] ?? 'normal');

                if ($tipo !== 'normal') {
                    $estado = 'ocorrencia';
                } elseif (!empty($registro['saida'])) {
                    $estado = 'concluido';
                } elseif (!empty($registro['entrada'])) {
                    $estado = 'andamento';
                }
            } elseif ($data > $hoje) {
                $estado = 'futuro';
            } elseif ($data < $hoje) {
                $estado = 'falta';
            }

            $dias[] = [
                'data' => $data,
                'dia_semana' => RegistrarController::DIAS_SEMANA[(int) date('w', $ts)],
                'dia' => date('d/m', $ts),
                'estado' => $estado,
                'registro' => $registro,
                'url' => $registro !== null
                    ? '/jornada/editar?data=' . urlencode($data)
                    : '/registrar?data=' . urlencode($data),
            ];
        }

        return [
            'inicio' => date('d/m', $inicioTs),
            'fim' => date('d/m', strtotime('+4 day', $inicioTs)),
            'dias' => $dias,
        ];
    }

    /**
     * Pendências do mês corrente: dias em aberto, comprovantes incompletos e
     * dias úteis anteriores a hoje que nunca foram lançados.
     *
     * @param array<string, array<string, mixed>> $porData
     * @return array{em_aberto: array<int, array{data: string, proxima: ?string}>, sem_foto: array<int, array{data: string, faltando: int}>, faltas: array<int, string>}
     */
    private static function montarPendencias(array $porData, string $mes, string $hoje): array
    {
        $emAberto = [];
        $semFoto = [];

        foreach ($porData as $data => $r) {
            if ($data >= $hoje || ($r['tipo'] ?? 'normal') !== 'normal') {
                continue;
            }

            if (empty($r['saida'])) {
                $emAberto[] = ['data' => $data, 'proxima' => RegistrarController::proximaBatida($r)];
                continue;
            }

            $comp = RegistrarController::statusComprovacao($r);
            if ($comp['batidas'] > 0 && $comp['fotos'] < $comp['batidas']) {
                $semFoto[] = [
                    'data' => $data,
                    'faltando' => $comp['batidas'] - $comp['fotos'],
                ];
            }
        }

        usort($emAberto, static fn(array $a, array $b): int => $a['data'] <=> $b['data']);
        usort($semFoto, static fn(array $a, array $b): int => $a['data'] <=> $b['data']);

        $primeiraData = $porData === [] ? null : min(array_keys($porData));

        $faltas = [];
        if ($primeiraData !== null) {
            $cursor = strtotime($mes . '-01');
            $limite = strtotime($hoje . ' -1 day');

            while ($cursor !== false && $limite !== false && $cursor <= $limite) {
                $data = date('Y-m-d', $cursor);
                $diaSemana = (int) date('w', $cursor);

                // Só dias úteis, a partir do primeiro lançamento do mês (evita
                // apontar como falta o período anterior ao início do uso).
                if ($diaSemana >= 1 && $diaSemana <= 5 && $data >= $primeiraData && !isset($porData[$data])) {
                    $faltas[] = $data;
                }

                $cursor = strtotime('+1 day', $cursor);
            }
        }

        return [
            'em_aberto' => $emAberto,
            'sem_foto' => $semFoto,
            'faltas' => $faltas,
        ];
    }
}
