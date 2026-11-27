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
        $linha = static fn(array $row) => fputcsv($out, $row, ';', '"', '');

        $linha([
            'Data',
            'Ocorrencia',
            'Entrada',
            'Saida Intervalo',
            'Retorno Intervalo',
            'Saida',
            'Total Trabalhado',
            'Saldo do Dia',
            'Comprovante',
            'Observacao',
        ]);

        foreach ($dados['registros'] as $r) {
            $saldoStr = $r['saldo_minutos'] === null
                ? 'Em andamento'
                : RegistrarController::formatarMinutos($r['saldo_minutos'], true);
            $comp = RegistrarController::statusComprovacao($r);

            $linha([
                date('d/m/Y', strtotime((string) $r['data'])),
                $r['tipo'],
                RegistrarController::horaCurta($r['entrada'] ?? null),
                RegistrarController::horaCurta($r['saida_intervalo'] ?? null),
                RegistrarController::horaCurta($r['retorno_intervalo'] ?? null),
                RegistrarController::horaCurta($r['saida'] ?? null),
                RegistrarController::formatarMinutos($r['minutos_trabalhados']),
                $saldoStr,
                $comp['rotulo'],
                $r['observacao'] ?? '',
            ]);
        }

        $linha([]);
        $linha([
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
        ]);

        fclose($out);
        exit;
    }

    private function carregarDadosMes(string $mesInput): array
    {
        $pdo = Database::connection();

        $mes = RegistrarController::mesValido($mesInput) ? $mesInput : date('Y-m');

        $usuario = ConfiguracoesController::obterUsuario();
        $nome = $usuario['nome_completo'];
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $stmt = $pdo->prepare(
            'SELECT * FROM registros_ponto
             WHERE usuario_id = :usuario_id AND data LIKE :mes
             ORDER BY data ASC'
        );
        $stmt->execute([
            'usuario_id' => $usuario['id'],
            'mes' => $mes . '-%',
        ]);
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
            $r = RegistrarController::enriquecerRegistro($r, $cargaDiaria, $tolerancia);

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
        ];
    }
}
