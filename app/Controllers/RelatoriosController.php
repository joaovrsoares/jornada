<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class RelatoriosController extends Controller
{
    public function index(): void
    {
        $dados = $this->carregarDadosMes($_GET['mes'] ?? date('Y-m'));

        $this->view('relatorios/index', [
            'titulo' => 'Relatórios',
            ...$dados,
        ]);
    }

    public function exportar(): void
    {
        $dados = $this->carregarDadosMes($_GET['mes'] ?? date('Y-m'));
        $mes = $dados['mes'];

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"espelho-ponto-{$mes}.csv\"");

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'Data',
            'Ocorrencia',
            'Entrada',
            'Saida Intervalo',
            'Retorno Intervalo',
            'Saida',
            'Total Trabalhado',
            'Saldo do Dia',
            'Comprovacao',
            'Observacao',
        ], ';', '"', '');

        $cortar = static fn(?string $h): string => $h ? substr($h, 0, 5) : '';

        foreach ($dados['registros'] as $r) {
            $saldoStr = $r['saldo_minutos'] === null
                ? 'Em andamento'
                : RegistrarController::formatarMinutos($r['saldo_minutos'], true);
            $comp = RegistrarController::statusComprovacao($r);

            fputcsv($out, [
                date('d/m/Y', strtotime((string) $r['data'])),
                $r['tipo'],
                $cortar($r['entrada'] ?? null),
                $cortar($r['saida_intervalo'] ?? null),
                $cortar($r['retorno_intervalo'] ?? null),
                $cortar($r['saida'] ?? null),
                RegistrarController::formatarMinutos($r['minutos_trabalhados']),
                $saldoStr,
                $comp['rotulo'],
                $r['observacao'] ?? '',
            ], ';', '"', '');
        }

        fputcsv($out, [], ';', '"', '');
        fputcsv($out, [
            'FECHAMENTO',
            count($dados['registros']) . ' dias',
            '',
            '',
            '',
            '',
            RegistrarController::formatarMinutos($dados['totalTrabalhado']),
            RegistrarController::formatarMinutos($dados['saldoLiquido'], true),
            '',
            '',
        ], ';', '"', '');

        fclose($out);
        exit;
    }

    private function carregarDadosMes(string $mesInput): array
    {
        $pdo = Database::connection();

        $mes = RegistrarController::mesValido($mesInput) ? $mesInput : date('Y-m');

        $usuario = ConfiguracoesController::obterUsuario();
        $nome = $usuario['nome'];
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $stmt = $pdo->prepare(
            'SELECT * FROM registros_ponto
             WHERE usuario_id = 1 AND data LIKE :mes
             ORDER BY data ASC'
        );
        $stmt->execute(['mes' => $mes . '-%']);
        $registros = $stmt->fetchAll();

        $totalTrabalhado = 0;
        $horasExtras = 0;
        $horasDebito = 0;
        $saldoLiquido = 0;
        $ocorrencias = [
            'normal' => 0,
            'atestado' => 0,
            'folga' => 0,
            'feriado' => 0,
            'falta' => 0,
        ];

        foreach ($registros as &$r) {
            $r['minutos_trabalhados'] = RegistrarController::calcularMinutos($r);
            $r['saldo_minutos'] = RegistrarController::calcularSaldoDia($r, $cargaDiaria, $tolerancia);

            $totalTrabalhado += $r['minutos_trabalhados'];

            $tipo = $r['tipo'] ?? 'normal';
            if (isset($ocorrencias[$tipo])) {
                $ocorrencias[$tipo]++;
            }

            if ($r['saldo_minutos'] !== null) {
                $saldoLiquido += $r['saldo_minutos'];
                if ($r['saldo_minutos'] > 0) {
                    $horasExtras += $r['saldo_minutos'];
                } elseif ($r['saldo_minutos'] < 0) {
                    $horasDebito += $r['saldo_minutos'];
                }
            }
        }
        unset($r);

        return [
            'mes' => $mes,
            'nome' => $nome,
            'cargaDiaria' => $cargaDiaria,
            'tolerancia' => $tolerancia,
            'registros' => $registros,
            'totalTrabalhado' => $totalTrabalhado,
            'horasExtras' => $horasExtras,
            'horasDebito' => $horasDebito,
            'saldoLiquido' => $saldoLiquido,
            'ocorrencias' => $ocorrencias,
            'mapaCalendario' => RegistrarController::obterMapaCalendario(),
        ];
    }
}
