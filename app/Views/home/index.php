<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$tsHoje = strtotime($hoje);
$diaSemanaHojeNum = (int) date('w', $tsHoje);
$diaSemanaHoje = RegistrarController::DIAS_SEMANA_COMPLETOS[$diaSemanaHojeNum];
$ehDiaUtil = $diaSemanaHojeNum >= 1 && $diaSemanaHojeNum <= 5;
$nomeMesRef = RegistrarController::formatarMesAno($mesAtual);
$tipoHoje = (string) ($registroHoje['tipo'] ?? 'normal');
$ehDiaNormal = $tipoHoje === 'normal';
$rotuloProxima = $proximaBatida !== null ? RegistrarController::CAMPOS_BATIDA[$proximaBatida] : null;
$saidaAberta = $ehDiaNormal && $proximaBatida !== null;

if (!$ehDiaNormal) {
    $rotuloStatusHoje = RegistrarController::ROTULOS_TIPO[$tipoHoje] ?? $tipoHoje;
    $corStatusHoje = match ($tipoHoje) {
        'atestado' => 'bg-sky-400',
        'folga' => 'bg-teal-400',
        'feriado' => 'bg-emerald-400',
        'falta' => 'bg-red-400',
        default => 'bg-stone-300',
    };
} elseif ($statusHoje['batidas'] === 0) {
    $rotuloStatusHoje = 'Sem batidas';
    $corStatusHoje = 'bg-amber-400';
} else {
    $rotuloStatusHoje = $statusHoje['rotulo'];
    $corStatusHoje = match ($statusHoje['status']) {
        'comprovado' => 'bg-emerald-400',
        'parcial' => 'bg-teal-400',
        'pendente' => 'bg-amber-400',
        default => 'bg-stone-300',
    };
}

// O card do dia fica sobre fundo escuro: usa a paleta do calendário e do modal.
$classeSaldoMes = $saldoMes > 0
    ? 'text-emerald-400'
    : ($saldoMes < 0 ? 'text-red-600' : 'text-white');

// Linha de orientação do dia: compensação de débito, saída prevista ou fechamento.
$compensacao = $assistente['compensacao'] ?? null;
$sugestaoRotulo = null;
$sugestaoTitulo = null;
$sugestaoDetalhe = null;

if ($ehDiaNormal && $ehDiaUtil) {
    if ($compensacao !== null) {
        $sugestaoRotulo = 'Sugestão de compensação';
        $sugestaoTitulo = '+' . $compensacao['minutos_por_dia'] . ' min por dia'
            . ($compensacao['saida_com_compensacao'] !== null
                ? ', saída às ' . $compensacao['saida_com_compensacao']
                : '');
        $diasRestantes = (int) $compensacao['dias_uteis_restantes'];
        $sugestaoDetalhe = 'Débito de ' . $compensacao['debito_total']
            . ($diasRestantes > 0
                ? ' · ' . $diasRestantes
                    . ($diasRestantes === 1 ? ' dia útil restante' : ' dias úteis restantes')
                : ' · último dia útil do mês');
    } elseif ($saidaAberta && !empty($assistente['saida_prevista'])) {
        $sugestaoRotulo = 'Saída prevista';
        $sugestaoTitulo = (string) $assistente['saida_prevista'];
        $sugestaoDetalhe = $assistente['nota_intervalo'] !== null
            ? (string) $assistente['nota_intervalo']
            : null;
    } elseif (!empty($registroHoje['saida'])) {
        $sugestaoTitulo = 'Dia concluído.';

        if (RegistrarController::ehUltimoDiaUtilDaSemana($hoje)) {
            $sugestaoTitulo .= ' Bom final de semana!';
        }

        if (isset($registroHoje['saldo_minutos'])) {
            $sugestaoDetalhe = 'Saldo do dia: '
                . RegistrarController::formatarMinutos((int) $registroHoje['saldo_minutos'], true);
        }
    } else {
        $sugestaoTitulo = 'Ainda sem batidas hoje.';
    }
}
$totalPendencias = count($pendencias['em_aberto']) + count($pendencias['sem_foto']) + count($pendencias['faltas']);

$estadosSemana = [
    'concluido' => ['rotulo' => 'Concluído', 'ponto' => 'bg-emerald-500', 'texto' => 'text-zinc-700'],
    'andamento' => ['rotulo' => 'Em aberto', 'ponto' => 'bg-amber-500', 'texto' => 'text-amber-800'],
    'ocorrencia' => ['rotulo' => 'Ocorrência', 'ponto' => 'bg-sky-500', 'texto' => 'text-sky-800'],
    'falta' => ['rotulo' => 'Sem lançamento', 'ponto' => 'bg-red-500', 'texto' => 'text-red-700'],
    'sem_lancamento' => ['rotulo' => 'Sem batidas', 'ponto' => 'bg-amber-500', 'texto' => 'text-amber-800'],
    'futuro' => ['rotulo' => 'Previsto', 'ponto' => 'bg-stone-300', 'texto' => 'text-zinc-400'],
];

$linhaPendencia = static function (string $data, string $detalhe, string $url): string {
    $ts = strtotime($data);
    $diaSemana = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];

    return '<li>'
        . '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="flex items-center justify-between gap-3 px-4 py-3 transition-colors hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-600">'
        . '<span class="flex min-w-0 flex-wrap items-baseline gap-x-2">'
        . '<span class="font-mono text-xs font-semibold text-zinc-900 tabular-nums">' . date('d/m', $ts) . '</span>'
        . '<span class="text-xs text-zinc-500">' . $diaSemana . '</span>'
        . '<span class="text-xs text-zinc-600">' . htmlspecialchars($detalhe, ENT_QUOTES, 'UTF-8') . '</span>'
        . '</span>'
        . '<span class="shrink-0 text-xs font-semibold text-teal-700 underline decoration-teal-700/30 underline-offset-2">Corrigir</span>'
        . '</a>'
        . '</li>';
};
?>
<section class="space-y-5">
    <?php if (!empty($erro)): ?>
        <p role="alert" class="rounded-xl border border-red-200 border-l-4 border-l-red-600 bg-red-50 px-4 py-3.5 text-sm font-medium text-red-900 shadow-2xs">
            <?= htmlspecialchars($erro) ?>
        </p>
    <?php endif; ?>

    <article class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 text-white shadow-md">
        <div class="grid grid-cols-1 divide-y bg-zinc-950 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
            <div class="border-zinc-800 px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Saldo do mês</span>
                <p class="mt-0.5 font-mono text-2xl font-bold tracking-tight tabular-nums <?= $classeSaldoMes ?>">
                    <?= RegistrarController::formatarMinutos($saldoMes, true) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">Soma dos dias de <?= htmlspecialchars($nomeMesRef) ?></small>
            </div>

            <div class="border-zinc-800 px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Horas no mês</span>
                <p class="mt-0.5 font-mono text-2xl font-bold tracking-tight text-white tabular-nums">
                    <?= RegistrarController::formatarMinutos($minutosMes) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">
                    <?= $diasMes ?> <?= $diasMes === 1 ? 'dia contabilizado' : 'dias contabilizados' ?>
                </small>
            </div>
        </div>

        <div class="border-t border-zinc-800 px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-baseline gap-x-2">
                    <span class="font-mono text-xs font-medium tracking-wide text-teal-400">HOJE</span>
                    <strong class="font-mono text-lg font-bold text-white tabular-nums"><?= date('d/m/Y', $tsHoje) ?></strong>
                    <span class="text-sm text-zinc-400"><?= $diaSemanaHoje ?></span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-lg bg-zinc-800 px-2.5 py-1 text-xs font-semibold text-zinc-200 ring-1 ring-inset ring-zinc-700">
                        <span class="size-1.5 rounded-full <?= $corStatusHoje ?>" aria-hidden="true"></span>
                        <?= htmlspecialchars($rotuloStatusHoje) ?>
                    </span>
                    <a
                        href="/jornada/editar?data=<?= urlencode($hoje) ?>"
                        class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-semibold text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    >
                        <?= $ehDiaNormal ? 'Ajustar horários' : 'Editar este dia' ?>
                    </a>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                <?php foreach (RegistrarController::CAMPOS_BATIDA as $campo => $rotulo):
                    $valor = RegistrarController::horaCurta($registroHoje[$campo] ?? null) ?: null;
                    $ehProxima = $ehDiaNormal && $proximaBatida === $campo;
                ?>
                    <div class="rounded-xl border px-3.5 py-3 <?= $ehProxima ? 'border-teal-500/30 bg-teal-950/60' : 'border-zinc-800 bg-zinc-950' ?>">
                        <span class="text-[11px] font-medium <?= $ehProxima ? 'text-teal-300' : 'text-zinc-400' ?>"><?= htmlspecialchars($rotulo) ?></span>
                        <p class="mt-1 font-mono text-lg font-bold tabular-nums <?= $valor !== null ? 'text-white' : 'text-zinc-600' ?>">
                            <?= $valor ?? '—' ?>
                        </p>
                        <?php if ($ehProxima): ?>
                            <span class="text-[10px] font-semibold text-teal-300">próxima</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($sugestaoTitulo !== null || $saidaAberta): ?>
                <div class="mt-3 flex flex-col gap-3 border-t border-zinc-800 pt-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <?php if ($sugestaoTitulo !== null): ?>
                            <?php if ($sugestaoRotulo !== null): ?>
                                <span class="block text-[11px] font-medium text-zinc-400"><?= htmlspecialchars($sugestaoRotulo) ?></span>
                            <?php endif; ?>
                            <p class="mt-0.5 text-sm font-semibold text-white"><?= htmlspecialchars($sugestaoTitulo) ?></p>
                            <?php if ($sugestaoDetalhe !== null): ?>
                                <small class="block text-[11px] text-zinc-500"><?= htmlspecialchars($sugestaoDetalhe) ?></small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($saidaAberta): ?>
                        <a
                            href="/registrar"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-teal-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 sm:w-auto"
                        >
                            Registrar <?= htmlspecialchars($rotuloProxima) ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </article>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-5 lg:items-start">
        <div class="space-y-3 lg:col-span-3">
            <header class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-zinc-900">Dias úteis da semana</h2>
                    <small class="text-xs text-zinc-500"><?= $semana['inicio'] ?> – <?= $semana['fim'] ?></small>
                </div>
            </header>

            <article class="overflow-hidden rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                <ul class="divide-y divide-stone-200/80">
                    <?php foreach ($semana['dias'] as $dia):
                        $ehHojeSemana = $dia['data'] === $hoje;
                        $registro = $dia['registro'];
                        $tipo = (string) ($registro['tipo'] ?? 'normal');
                        $temBatidas = $registro !== null && (
                            !empty($registro['entrada'])
                            || !empty($registro['saida_intervalo'])
                            || !empty($registro['retorno_intervalo'])
                            || !empty($registro['saida'])
                        );
                        $saldo = $registro['saldo_minutos'] ?? null;
                        $estado = $estadosSemana[$dia['estado']] ?? $estadosSemana['futuro'];
                    ?>
                        <li>
                            <a
                                href="<?= htmlspecialchars($dia['url'], ENT_QUOTES, 'UTF-8') ?>"
                                class="block px-4 py-3 transition-colors hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-600 sm:px-5"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <span class="flex min-w-0 flex-wrap items-baseline gap-x-2">
                                        <span class="w-8 font-mono text-[11px] font-semibold tracking-wide text-zinc-500"><?= $dia['dia_semana'] ?></span>
                                        <span class="font-mono text-sm font-semibold tabular-nums <?= $ehHojeSemana ? 'text-teal-700' : 'text-zinc-900' ?>"><?= $dia['dia'] ?></span>
                                        <?php if ($ehHojeSemana): ?>
                                            <span class="text-xs font-semibold text-teal-700">Hoje</span>
                                        <?php endif; ?>
                                    </span>

                                    <?php if ($temBatidas): ?>
                                        <span class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 font-mono text-[11px] font-semibold tabular-nums <?= RegistrarController::classeBadgeSaldo($saldo) ?>">
                                            <?= $saldo === null ? 'Em aberto' : RegistrarController::formatarMinutos($saldo, true) ?>
                                        </span>
                                    <?php elseif ($tipo !== 'normal'): ?>
                                        <span class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= RegistrarController::CLASSES_TIPO[$tipo] ?? RegistrarController::CLASSES_TIPO['normal'] ?>">
                                            <?= htmlspecialchars(RegistrarController::ROTULOS_TIPO[$tipo] ?? $tipo) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="flex shrink-0 items-center gap-1.5 text-xs <?= $estado['texto'] ?>">
                                            <span class="size-1.5 rounded-full <?= $estado['ponto'] ?>" aria-hidden="true"></span>
                                            <?= htmlspecialchars($estado['rotulo']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($temBatidas): ?>
                                    <div class="mt-2.5 grid grid-cols-4 divide-x divide-stone-200/80 overflow-hidden rounded-lg border border-stone-200/80 bg-stone-50/70">
                                        <?php foreach (RegistrarController::CAMPOS_BATIDA_CURTOS as $campo => $rotuloCurto):
                                            $hora = !empty($registro[$campo]) ? substr((string) $registro[$campo], 0, 5) : null;
                                        ?>
                                            <div class="min-w-0 px-2.5 py-1.5">
                                                <span class="block truncate text-[10px] font-medium text-zinc-500"><?= htmlspecialchars($rotuloCurto) ?></span>
                                                <span class="font-mono text-xs font-semibold tabular-nums <?= $hora !== null ? 'text-zinc-900' : 'text-zinc-400' ?>">
                                                    <?= $hora ?? '—:—' ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <a
                    href="/jornada?mes=<?= urlencode($mesAtual) ?>"
                    class="flex items-center justify-between gap-3 border-t border-stone-200/90 px-4 py-3 text-xs font-semibold text-teal-700 transition-colors hover:bg-stone-50 focus:outline-none focus-visible:bg-stone-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-600 sm:px-5"
                >
                    <span>Abrir espelho do mês</span>
                    <span class="font-mono text-[11px] text-zinc-500"><?= htmlspecialchars($nomeMesRef) ?></span>
                </a>
            </article>
        </div>

        <aside class="space-y-3 lg:col-span-2">
            <header class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-zinc-900">Pendências do mês</h2>
                    <small class="text-xs text-zinc-500">
                        <?php if ($totalPendencias > 0): ?>
                            <?= $totalPendencias ?> <?= $totalPendencias === 1 ? 'item para revisar' : 'itens para revisar' ?>
                        <?php else: ?>
                            Nenhum item em <?= htmlspecialchars($nomeMesRef) ?>.
                        <?php endif; ?>
                    </small>
                </div>
            </header>

            <?php if ($totalPendencias === 0): ?>
                <article class="rounded-xl border border-dashed border-emerald-300 bg-emerald-50/60 px-5 py-8 text-center">
                    <p class="text-sm font-medium text-emerald-900">Tudo em dia por aqui.</p>
                </article>
            <?php else: ?>
                <div class="space-y-3">
                    <?php if (!empty($pendencias['em_aberto'])): ?>
                        <div class="overflow-hidden rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                            <div class="flex items-center justify-between gap-3 border-b border-stone-200 bg-stone-50/80 px-4 py-2.5">
                                <span class="text-xs font-semibold text-zinc-700">Dias em aberto</span>
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                    <?= count($pendencias['em_aberto']) ?>
                                </span>
                            </div>
                            <ul class="divide-y divide-stone-200/80">
                                <?php foreach ($pendencias['em_aberto'] as $item):
                                    $detalhe = $item['proxima'] !== null
                                        ? 'Próxima: ' . RegistrarController::CAMPOS_BATIDA[$item['proxima']]
                                        : 'Sem batidas lançadas';
                                    echo $linhaPendencia(
                                        $item['data'],
                                        $detalhe,
                                        '/registrar?data=' . urlencode($item['data'])
                                    );
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pendencias['sem_foto'])): ?>
                        <div class="overflow-hidden rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                            <div class="flex items-center justify-between gap-3 border-b border-stone-200 bg-stone-50/80 px-4 py-2.5">
                                <span class="text-xs font-semibold text-zinc-700">Dias sem foto</span>
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                    <?= count($pendencias['sem_foto']) ?>
                                </span>
                            </div>
                            <ul class="divide-y divide-stone-200/80">
                                <?php foreach ($pendencias['sem_foto'] as $item):
                                    echo $linhaPendencia(
                                        $item['data'],
                                        $item['faltando'] . ($item['faltando'] === 1 ? ' foto faltando' : ' fotos faltando'),
                                        '/jornada/editar?data=' . urlencode($item['data'])
                                    );
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pendencias['faltas'])): ?>
                        <div class="overflow-hidden rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                            <div class="flex items-center justify-between gap-3 border-b border-stone-200 bg-stone-50/80 px-4 py-2.5">
                                <span class="text-xs font-semibold text-zinc-700">Faltas não lançadas</span>
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-800 ring-1 ring-inset ring-red-600/20">
                                    <?= count($pendencias['faltas']) ?>
                                </span>
                            </div>
                            <ul class="divide-y divide-stone-200/80">
                                <?php foreach ($pendencias['faltas'] as $data):
                                    echo $linhaPendencia(
                                        $data,
                                        'Dia útil sem lançamento',
                                        '/jornada/editar?data=' . urlencode($data)
                                    );
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </aside>
    </div>
</section>
