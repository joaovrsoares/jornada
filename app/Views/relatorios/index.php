<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$nomeMesRef = RegistrarController::formatarMesAno($mes);
$mesAnterior = RegistrarController::deslocarMes($mes, -1);
$proximoMes = RegistrarController::deslocarMes($mes, 1);
$jsonMapaCalendario = htmlspecialchars(
    json_encode($mapaCalendario ?? [], JSON_UNESCAPED_UNICODE) ?: '{}',
    ENT_QUOTES,
    'UTF-8'
);
?>
<section class="space-y-6">
    <header
        class="print:hidden"
        id="widget-calendario"
        data-escopo="mes"
        data-rota-base="/relatorios"
        data-mes-selecionado="<?= htmlspecialchars($mes) ?>"
        data-hoje="<?= date('Y-m-d') ?>"
        data-registros="<?= $jsonMapaCalendario ?>"
    >
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold tracking-tight text-zinc-900">Folha de conferência mensal</h2>
                <small class="mt-0.5 block text-xs text-zinc-500">Compare seus lançamentos com o espelho de ponto fornecido pelo empregador.</small>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <a
                        href="/relatorios?mes=<?= urlencode($mesAnterior) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-stone-300 bg-white font-mono text-base font-bold text-zinc-700 shadow-2xs transition-colors hover:bg-stone-50 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                        title="Mês anterior (<?= htmlspecialchars(RegistrarController::formatarMesAno($mesAnterior)) ?>)"
                        aria-label="Mês anterior"
                    >
                        ‹
                    </a>
                    <button
                        type="button"
                        id="btn-calendario"
                        aria-expanded="false"
                        aria-controls="painel-calendario"
                        class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-xl border border-stone-300 bg-white px-3.5 text-sm font-bold text-zinc-900 shadow-2xs transition-colors hover:border-teal-600/50 hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                        title="Abrir calendário do sistema"
                    >
                        <svg class="size-4 text-teal-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                        </svg>
                        <span><?= htmlspecialchars($nomeMesRef) ?></span>
                        <svg id="icone-calendario-seta" class="size-4 text-zinc-500 transition-transform duration-150" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <a
                        href="/relatorios?mes=<?= urlencode($proximoMes) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-stone-300 bg-white font-mono text-base font-bold text-zinc-700 shadow-2xs transition-colors hover:bg-stone-50 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                        title="Próximo mês (<?= htmlspecialchars(RegistrarController::formatarMesAno($proximoMes)) ?>)"
                        aria-label="Próximo mês"
                    >
                        ›
                    </a>
                </div>
                <a
                    href="/relatorios/exportar?mes=<?= urlencode($mes) ?>"
                    class="inline-flex h-9 items-center justify-center rounded-xl border border-stone-300 bg-white px-3.5 text-xs font-semibold text-zinc-800 shadow-2xs transition-colors hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                >
                    Exportar CSV
                </a>
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex h-9 cursor-pointer items-center justify-center rounded-xl bg-zinc-900 px-4 text-xs font-bold text-white shadow-2xs transition-colors hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                >
                    Imprimir / PDF
                </button>
            </div>
        </div>

        <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>
    </header>

    <div class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs print:break-inside-avoid print:border-zinc-300 print:shadow-none">
        <article class="grid grid-cols-2 gap-4 border-b border-stone-200 bg-stone-50/70 px-5 py-4 lg:grid-cols-4 sm:px-6">
            <div>
                <small class="block text-[11px] font-medium text-zinc-500">Identificação</small>
                <strong class="mt-0.5 block text-sm font-bold text-zinc-900"><?= htmlspecialchars($nome) ?></strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500">Período de referência</small>
                <strong class="mt-0.5 block text-sm font-bold text-zinc-900"><?= htmlspecialchars($nomeMesRef) ?></strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500">Jornada diária contratada</small>
                <strong class="mt-0.5 block font-mono text-sm font-bold text-zinc-900 tabular-nums">
                    <?= RegistrarController::formatarMinutos($cargaDiaria) ?>
                    <?= !empty($tolerancia) ? '(±' . (int) $tolerancia . 'm)' : '' ?>
                </strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500">Dias registrados</small>
                <strong class="mt-0.5 block font-mono text-sm font-bold text-zinc-900 tabular-nums">
                    <?= count($registros) ?>
                    <?php if ($ocorrencias['atestado'] || $ocorrencias['folga'] || $ocorrencias['falta']): ?>
                        <span class="font-sans text-xs font-normal text-zinc-500">
                            (<?= $ocorrencias['atestado'] ?> atest., <?= $ocorrencias['folga'] ?> folga, <?= $ocorrencias['falta'] ?> falta)
                        </span>
                    <?php endif; ?>
                </strong>
            </div>
        </article>

        <div class="grid grid-cols-2 divide-y divide-stone-200 lg:grid-cols-4 lg:divide-x lg:divide-y-0">
            <article class="p-5 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Total trabalhado</span>
                <p class="mt-1.5 font-mono text-2xl font-bold tracking-tight text-zinc-900 tabular-nums">
                    <?= RegistrarController::formatarMinutos($totalTrabalhado) ?>
                </p>
                <small class="mt-1 block text-xs text-zinc-500">Soma líquida do mês</small>
            </article>

            <article class="p-5 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Horas extras (+)</span>
                <p class="mt-1.5 font-mono text-2xl font-bold tracking-tight text-emerald-700 tabular-nums">
                    +<?= RegistrarController::formatarMinutos($horasExtras) ?>
                </p>
                <small class="mt-1 block text-xs text-zinc-500">Créditos acumulados</small>
            </article>

            <article class="p-5 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Atrasos e faltas (-)</span>
                <p class="mt-1.5 font-mono text-2xl font-bold tracking-tight tabular-nums <?= $horasDebito < 0 ? 'text-red-700' : 'text-zinc-900' ?>">
                    <?= RegistrarController::formatarMinutos($horasDebito) ?>
                </p>
                <small class="mt-1 block text-xs text-zinc-500">Débitos acumulados</small>
            </article>

            <article class="p-5 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Saldo líquido</span>
                <p class="mt-1.5 font-mono text-2xl font-bold tracking-tight tabular-nums <?= $saldoLiquido > 0 ? 'text-emerald-700' : ($saldoLiquido < 0 ? 'text-red-700' : 'text-zinc-900') ?>">
                    <?= RegistrarController::formatarMinutos($saldoLiquido, true) ?>
                </p>
                <small class="mt-1 block text-xs text-zinc-500">Resultado para confronto</small>
            </article>
        </div>
    </div>

    <article
        id="painel-confronto"
        data-trabalhado="<?= (int) $totalTrabalhado ?>"
        data-saldo="<?= (int) $saldoLiquido ?>"
        class="rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs sm:p-6 print:hidden space-y-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <b class="text-sm font-bold text-zinc-900">Simulador de confronto com o RH / Empresa</b>
                <small class="mt-0.5 block text-xs text-zinc-500">
                    Digite os totais impressos na folha da empresa (ex.: <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">160:00</code>, <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">+02:15</code> ou <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">-01:30</code>) para apurar diferenças na hora.
                </small>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="block rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                <span class="block text-xs font-semibold text-zinc-700">Total trabalhado na folha da empresa (HH:MM)</span>
                <input
                    type="text"
                    id="empresa-trabalhado"
                    placeholder="Ex.: 162:30"
                    autocomplete="off"
                    class="mt-2 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors placeholder:font-normal placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <span class="mt-2 block min-h-5 font-mono text-xs font-semibold tabular-nums text-zinc-500" id="res-trabalhado"></span>
            </label>

            <label class="block rounded-xl border border-stone-200 bg-stone-50/60 p-4">
                <span class="block text-xs font-semibold text-zinc-700">Saldo do mês na folha da empresa (±HH:MM)</span>
                <input
                    type="text"
                    id="empresa-saldo"
                    placeholder="Ex.: +01:45 ou -00:30"
                    autocomplete="off"
                    class="mt-2 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors placeholder:font-normal placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <span class="mt-2 block min-h-5 font-mono text-xs font-semibold tabular-nums text-zinc-500" id="res-saldo"></span>
            </label>
        </div>
    </article>

    <?php if (empty($registros)): ?>
        <article class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <p class="text-sm text-zinc-600">Nenhum registro encontrado para <?= htmlspecialchars($nomeMesRef) ?>.</p>
        </article>
    <?php else: ?>
        <div class="space-y-3 lg:hidden print:hidden">
            <?php foreach ($registros as $item):
                $ts = strtotime($item['data']);
                $diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
                $saldo = $item['saldo_minutos'];
                $classeSaldo = RegistrarController::classeBadgeSaldo($saldo);
                $comp = RegistrarController::statusComprovacao($item);
                $classeComp = RegistrarController::CLASSES_COMPROVACAO[$comp['status']] ?? RegistrarController::CLASSES_COMPROVACAO['isento'];
                $classeTipo = RegistrarController::CLASSES_TIPO[$item['tipo']] ?? RegistrarController::CLASSES_TIPO['normal'];
            ?>
                <article class="rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                    <div class="px-4 py-3.5">
                        <header class="flex flex-wrap items-start justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <strong class="font-mono text-sm font-bold text-zinc-900 tabular-nums"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                                <span class="text-xs text-zinc-500"><?= $diaSem ?></span>
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[11px] font-medium <?= $classeTipo ?>">
                                    <?= htmlspecialchars(RegistrarController::ROTULOS_TIPO[$item['tipo']] ?? $item['tipo']) ?>
                                </span>
                            </div>
                            <span
                                class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= $classeComp ?>"
                                title="<?= $comp['batidas'] === 0 ? 'Nenhuma batida registrada' : $comp['fotos'] . ' de ' . $comp['batidas'] . ' batidas com foto do ticket' ?>"
                            >
                                <?= htmlspecialchars($comp['rotulo_curto']) ?>
                            </span>
                        </header>

                        <dl class="mt-3 grid grid-cols-4 divide-x divide-stone-200/80 overflow-hidden rounded-lg border border-stone-200/80 bg-stone-50/70">
                            <?php foreach (RegistrarController::CAMPOS_BATIDA_CURTOS as $campo => $rotuloCurto): ?>
                                <div class="min-w-0 px-2.5 py-2">
                                    <dt class="truncate text-[10px] font-medium text-zinc-500"><?= $rotuloCurto ?></dt>
                                    <dd class="mt-0.5 font-mono text-sm font-semibold text-zinc-900 tabular-nums">
                                        <?= RegistrarController::renderizarCelulaBatida($item, $campo) ?>
                                    </dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>

                        <?php if (!empty($item['observacao'])): ?>
                            <p class="mt-2.5 text-xs leading-relaxed text-zinc-500">
                                <span class="font-medium text-zinc-600">Obs.:</span> <?= htmlspecialchars($item['observacao']) ?>
                            </p>
                        <?php endif; ?>

                        <footer class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-zinc-500">
                                Trabalhado
                                <strong class="ml-1 font-mono text-xs font-semibold text-zinc-900 tabular-nums"><?= RegistrarController::formatarMinutos($item['minutos_trabalhados']) ?></strong>
                            </span>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 font-mono text-[11px] font-semibold tabular-nums <?= $classeSaldo ?>">
                                <?= $saldo === null ? 'Aberto' : RegistrarController::formatarMinutos($saldo, true) ?>
                            </span>
                        </footer>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <article class="hidden overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs lg:block print:block print:border-zinc-300 print:shadow-none">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50/90 text-xs font-semibold text-zinc-600">
                            <th class="px-4 py-3 whitespace-nowrap">Data</th>
                            <th class="px-3 py-3 whitespace-nowrap">Ocorrência</th>
                            <th class="px-3 py-3 whitespace-nowrap">Entrada</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saída int.</th>
                            <th class="px-3 py-3 whitespace-nowrap">Retorno</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saída</th>
                            <th class="px-3 py-3 whitespace-nowrap">Trabalhado</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saldo</th>
                            <th class="px-3 py-3 whitespace-nowrap">Tickets</th>
                            <th class="px-4 py-3 whitespace-nowrap">Observação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200/80">
                        <?php foreach ($registros as $item):
                            $ts = strtotime($item['data']);
                            $diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
                            $saldo = $item['saldo_minutos'];
                            $classeSaldo = RegistrarController::classeBadgeSaldo($saldo);
                            $comp = RegistrarController::statusComprovacao($item);
                            $classeComp = RegistrarController::CLASSES_COMPROVACAO[$comp['status']] ?? RegistrarController::CLASSES_COMPROVACAO['isento'];
                            $classeTipo = RegistrarController::CLASSES_TIPO[$item['tipo']] ?? RegistrarController::CLASSES_TIPO['normal'];
                        ?>
                            <tr class="transition-colors hover:bg-stone-50/80">
                                <td class="px-4 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <strong class="font-semibold text-zinc-900"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                                    <span class="ml-1 font-sans text-xs text-zinc-500"><?= $diaSem ?></span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium <?= $classeTipo ?>">
                                        <?= htmlspecialchars(RegistrarController::ROTULOS_TIPO[$item['tipo']] ?? $item['tipo']) ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'entrada') ?></td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'saida_intervalo') ?></td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'retorno_intervalo') ?></td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'saida') ?></td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <strong class="font-semibold text-zinc-900"><?= RegistrarController::formatarMinutos($item['minutos_trabalhados']) ?></strong>
                                </td>
                                <td class="px-3 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $classeSaldo ?>">
                                        <?= $saldo === null ? 'Aberto' : RegistrarController::formatarMinutos($saldo, true) ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $classeComp ?>">
                                        <?= htmlspecialchars($comp['rotulo']) ?>
                                    </span>
                                </td>
                                <td class="max-w-52 truncate px-4 py-2.5 text-xs text-zinc-500" title="<?= htmlspecialchars($item['observacao'] ?? '') ?>">
                                    <?= htmlspecialchars($item['observacao'] ?? '—') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-stone-200 bg-stone-50 font-semibold text-zinc-900">
                            <td colspan="6" class="px-4 py-3 text-xs">
                                <strong>Fechamento do período (<?= count($registros) ?> <?= count($registros) === 1 ? 'dia' : 'dias' ?>)</strong>
                            </td>
                            <td class="px-3 py-3 font-mono text-xs whitespace-nowrap tabular-nums">
                                <strong><?= RegistrarController::formatarMinutos($totalTrabalhado) ?></strong>
                            </td>
                            <td class="px-3 py-3 font-mono text-xs whitespace-nowrap tabular-nums">
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= RegistrarController::classeBadgeSaldo($saldoLiquido) ?>">
                                    <?= RegistrarController::formatarMinutos($saldoLiquido, true) ?>
                                </span>
                            </td>
                            <td colspan="2" class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </article>
    <?php endif; ?>
</section>
