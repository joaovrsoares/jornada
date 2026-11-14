<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$nomeMesRef = RegistrarController::formatarMesAno($mes);
$mesAnterior = RegistrarController::deslocarMes($mes, -1);
$proximoMes = RegistrarController::deslocarMes($mes, 1);
$mesDeHoje = date('Y-m');
?>
<section class="space-y-5">
    <article class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 text-white shadow-md print:border-stone-300 print:bg-white print:text-zinc-900 print:shadow-none">
        <div class="px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-white print:text-zinc-900">Relatórios mensais</h2>
                    <small class="mt-0.5 block text-xs text-zinc-400 print:text-zinc-600">Compare seus lançamentos com o espelho de ponto fornecido pelo empregador.</small>
                </div>

                <div class="flex flex-wrap items-center gap-1.5 print:hidden">
                    <a
                        href="/relatorios?mes=<?= urlencode($mesAnterior) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Mês anterior (<?= htmlspecialchars(RegistrarController::formatarMesAno($mesAnterior)) ?>)"
                        aria-label="Mês anterior"
                    >
                        ‹
                    </a>
                    <span class="inline-flex h-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 px-3.5 text-sm font-bold text-white">
                        <?= htmlspecialchars($nomeMesRef) ?>
                    </span>
                    <a
                        href="/relatorios?mes=<?= urlencode($proximoMes) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Próximo mês (<?= htmlspecialchars(RegistrarController::formatarMesAno($proximoMes)) ?>)"
                        aria-label="Próximo mês"
                    >
                        ›
                    </a>
                    <a
                        href="/relatorios?mes=<?= urlencode($mesDeHoje) ?>"
                        class="inline-flex h-9 items-center justify-center rounded-xl border border-teal-500/30 bg-teal-950/60 px-3 text-xs font-semibold text-teal-300 transition-colors hover:bg-teal-900/70 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Ir para o mês atual"
                    >
                        Mês atual
                    </a>
                    <a
                        href="/relatorios/exportar?mes=<?= urlencode($mes) ?>"
                        class="inline-flex h-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 px-3.5 text-xs font-semibold text-zinc-200 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    >
                        Exportar CSV
                    </a>
                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex h-9 cursor-pointer items-center justify-center rounded-xl bg-teal-600 px-4 text-xs font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                    >
                        Imprimir / PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-px border-t border-zinc-800 bg-zinc-800 lg:grid-cols-4 print:border-stone-300 print:bg-stone-200">
            <article class="bg-zinc-950 px-4 py-3.5 sm:px-6 sm:py-4 print:bg-white">
                <span class="block text-[11px] font-medium text-zinc-400 print:text-zinc-600">Total trabalhado</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-xl font-bold tracking-tight sm:text-2xl text-white tabular-nums print:text-zinc-900">
                    <?= RegistrarController::formatarMinutos($totalTrabalhado) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500 print:text-zinc-600">Soma líquida do mês</small>
            </article>

            <article class="bg-zinc-950 px-4 py-3.5 sm:px-6 sm:py-4 print:bg-white">
                <span class="block text-[11px] font-medium text-zinc-400 print:text-zinc-600">Horas extras (+)</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-xl font-bold tracking-tight sm:text-2xl text-emerald-400 tabular-nums print:text-emerald-700">
                    +<?= RegistrarController::formatarMinutos($horasExtras) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500 print:text-zinc-600">Créditos acumulados</small>
            </article>

            <article class="bg-zinc-950 px-4 py-3.5 sm:px-6 sm:py-4 print:bg-white">
                <span class="block text-[11px] font-medium text-zinc-400 print:text-zinc-600">Atrasos e faltas (-)</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-xl font-bold tracking-tight sm:text-2xl tabular-nums print:text-zinc-900 <?= $horasDebito < 0 ? 'text-red-600' : 'text-white' ?>">
                    <?= RegistrarController::formatarMinutos($horasDebito) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500 print:text-zinc-600">Débitos acumulados</small>
            </article>

            <article class="bg-zinc-950 px-4 py-3.5 sm:px-6 sm:py-4 print:bg-white">
                <span class="block text-[11px] font-medium text-zinc-400 print:text-zinc-600">Saldo líquido</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-xl font-bold tracking-tight sm:text-2xl tabular-nums print:text-zinc-900 <?= $saldoLiquido > 0 ? 'text-emerald-400' : ($saldoLiquido < 0 ? 'text-red-600' : 'text-white') ?>">
                    <?= RegistrarController::formatarMinutos($saldoLiquido, true) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500 print:text-zinc-600">Resultado para confronto</small>
            </article>
        </div>

        <div class="grid grid-cols-2 gap-4 border-t border-zinc-800 px-5 py-3.5 sm:px-6 lg:grid-cols-4 print:border-stone-300">
            <div>
                <small class="block text-[11px] font-medium text-zinc-500 print:text-zinc-600">Identificação</small>
                <strong class="mt-0.5 block text-sm font-bold text-white print:text-zinc-900"><?= htmlspecialchars($nome) ?></strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500 print:text-zinc-600">Período de referência</small>
                <strong class="mt-0.5 block text-sm font-bold text-white print:text-zinc-900"><?= htmlspecialchars($nomeMesRef) ?></strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500 print:text-zinc-600">Jornada diária contratada</small>
                <strong class="mt-0.5 block font-mono text-sm font-bold text-white tabular-nums print:text-zinc-900">
                    <?= RegistrarController::formatarMinutos($cargaDiaria) ?>
                    <?= !empty($tolerancia) ? '(±' . (int) $tolerancia . 'm)' : '' ?>
                </strong>
            </div>
            <div>
                <small class="block text-[11px] font-medium text-zinc-500 print:text-zinc-600">Dias registrados</small>
                <strong class="mt-0.5 block font-mono text-sm font-bold text-white tabular-nums print:text-zinc-900">
                    <?= count($registros) ?>
                    <?php if ($ocorrencias['atestado'] || $ocorrencias['folga'] || $ocorrencias['falta']): ?>
                        <span class="font-sans text-xs font-normal text-zinc-400 print:text-zinc-600">
                            (<?= $ocorrencias['atestado'] ?> atest., <?= $ocorrencias['folga'] ?> folga, <?= $ocorrencias['falta'] ?> falta)
                        </span>
                    <?php endif; ?>
                </strong>
            </div>
        </div>
    </article>

    <details
        id="painel-confronto"
        data-trabalhado="<?= (int) $totalTrabalhado ?>"
        data-saldo="<?= (int) $saldoLiquido ?>"
        class="group rounded-2xl border border-stone-200/90 bg-white shadow-2xs print:hidden"
    >
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 sm:px-6">
            <div>
                <span class="block text-sm font-bold text-zinc-900">Simulador de confronto com o RH / Empresa</span>
                <span class="mt-0.5 block text-xs text-zinc-500">Abra para comparar os totais da folha da empresa com o seu registro.</span>
            </div>
            <?= RegistrarController::icone('chevron', 'size-4 shrink-0 text-zinc-500 transition-transform duration-150 group-open:rotate-180') ?>
        </summary>

        <div class="space-y-4 border-t border-stone-200 px-5 py-5 sm:px-6">
            <small class="block text-xs text-zinc-500">
                Digite os totais impressos na folha da empresa (ex.: <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">160:00</code>, <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">+02:15</code> ou <code class="rounded-sm bg-stone-100 px-1.5 py-0.5 font-mono text-zinc-800">-01:30</code>) para apurar diferenças na hora.
            </small>

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
        </div>
    </details>

    <?php if (empty($registros)): ?>
        <article class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <p class="text-sm text-zinc-600">Nenhum registro encontrado para <?= htmlspecialchars($nomeMesRef) ?>.</p>
        </article>
    <?php else: ?>
        <div class="space-y-3 lg:hidden print:hidden">
            <?php foreach ($registros as $item):
                $ehHoje = false;
                $mostrarAcoes = false;
                require __DIR__ . '/../layouts/cartao-dia.php';
            endforeach; ?>
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
                            <th class="px-3 py-3 whitespace-nowrap">Comprovante</th>
                            <th class="px-4 py-3 whitespace-nowrap">Observação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200/80">
                        <?php foreach ($registros as $item):
                            $ts = strtotime($item['data']);
                            $diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
                        ?>
                            <tr class="transition-colors hover:bg-stone-50/80">
                                <td class="px-4 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <strong class="font-semibold text-zinc-900"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                                    <span class="ml-1 font-sans text-xs text-zinc-500"><?= $diaSem ?></span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium <?= $item['classe_tipo'] ?>">
                                        <?= htmlspecialchars($item['rotulo_tipo']) ?>
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
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $item['classe_saldo'] ?>">
                                        <?= $item['saldo_minutos'] === null ? 'Aberto' : RegistrarController::formatarMinutos($item['saldo_minutos'], true) ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $item['classe_comprovacao'] ?>">
                                        <?= htmlspecialchars($item['rotulo_comprovacao']) ?>
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
