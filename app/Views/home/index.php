<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$nomeMesRef = RegistrarController::formatarMesAno($mesAtual);
$mesAnterior = RegistrarController::deslocarMes($mesAtual, -1);
$proximoMes = RegistrarController::deslocarMes($mesAtual, 1);
$jsonMapaCalendario = htmlspecialchars(
    json_encode($mapaCalendario ?? [], JSON_UNESCAPED_UNICODE) ?: '{}',
    ENT_QUOTES,
    'UTF-8'
);
?>
<section class="space-y-7">
    <div
        class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 text-white shadow-sm"
        id="widget-calendario"
        data-escopo="mes"
        data-rota-base="/"
        data-mes-selecionado="<?= htmlspecialchars($mesAtual) ?>"
        data-hoje="<?= date('Y-m-d') ?>"
        data-registros="<?= $jsonMapaCalendario ?>"
    >
        <div class="border-b border-zinc-800 px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-mono text-xs font-medium text-teal-400">RESUMO OPERACIONAL</span>
                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                        <a
                            href="/?mes=<?= urlencode($mesAnterior) ?>"
                            class="inline-flex size-8 items-center justify-center rounded-lg border border-zinc-700/90 bg-zinc-800/90 font-mono text-sm font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white"
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
                            class="inline-flex h-8 cursor-pointer items-center gap-2 rounded-lg border border-zinc-700/90 bg-zinc-800/90 px-3 text-sm font-bold text-white transition-colors hover:border-teal-500/60 hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                            title="Abrir calendário do sistema"
                        >
                            <svg class="size-3.5 text-teal-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                            </svg>
                            <span><?= htmlspecialchars($nomeMesRef) ?></span>
                            <svg id="icone-calendario-seta" class="size-3.5 text-zinc-400 transition-transform duration-150" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <a
                            href="/?mes=<?= urlencode($proximoMes) ?>"
                            class="inline-flex size-8 items-center justify-center rounded-lg border border-zinc-700/90 bg-zinc-800/90 font-mono text-sm font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white"
                            title="Próximo mês (<?= htmlspecialchars(RegistrarController::formatarMesAno($proximoMes)) ?>)"
                            aria-label="Próximo mês"
                        >
                            ›
                        </a>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="/registrar"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white shadow-2xs transition-colors hover:bg-teal-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400"
                    >
                        <span>+ Registrar batida agora</span>
                    </a>
                    <a
                        href="/registrar?modo=completo"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-700 bg-zinc-800 px-3.5 py-2 text-xs font-semibold text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-white"
                    >
                        <span>Lançar dia completo</span>
                    </a>
                </div>
            </div>

            <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>
        </div>

        <div class="grid grid-cols-1 divide-y divide-zinc-800 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <article class="p-5 sm:p-6">
                <span class="text-xs font-medium text-zinc-400">Horas no mês</span>
                <p class="mt-2 font-mono text-3xl font-bold tracking-tight text-white tabular-nums">
                    <?= RegistrarController::formatarMinutos($minutosMes) ?>
                </p>
                <small class="mt-1.5 block text-xs text-zinc-400">
                    <?= $diasMes ?> <?= $diasMes === 1 ? 'dia contabilizado' : 'dias contabilizados' ?> em <?= htmlspecialchars($nomeMesRef) ?>
                </small>
            </article>

            <article class="p-5 sm:p-6">
                <span class="text-xs font-medium text-zinc-400">Saldo acumulado</span>
                <p class="mt-2 font-mono text-3xl font-bold tracking-tight tabular-nums <?= $saldoAcumulado > 0 ? 'text-emerald-400' : ($saldoAcumulado < 0 ? 'text-red-400' : 'text-white') ?>">
                    <?= RegistrarController::formatarMinutos($saldoAcumulado, true) ?>
                </p>
                <small class="mt-1.5 block text-xs text-zinc-400">Banco de horas geral</small>
            </article>

            <article class="p-5 sm:p-6">
                <span class="text-xs font-medium text-zinc-400">Última batida</span>
                <?php if ($ultimaBatida): ?>
                    <p class="mt-2 font-mono text-3xl font-bold tracking-tight text-teal-300 tabular-nums">
                        <?= htmlspecialchars($ultimaBatida['hora']) ?>
                    </p>
                    <small class="mt-1.5 block text-xs text-zinc-400">
                        <?= htmlspecialchars($ultimaBatida['rotulo']) ?> · <?= htmlspecialchars(date('d/m/Y', strtotime($ultimaBatida['data']))) ?>
                    </small>
                <?php else: ?>
                    <p class="mt-2 font-mono text-3xl font-bold tracking-tight text-zinc-600 tabular-nums">—:—</p>
                    <small class="mt-1.5 block text-xs text-zinc-500">Nenhuma batida registrada</small>
                <?php endif; ?>
            </article>
        </div>
    </div>

    <div class="space-y-3.5">
        <header class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold tracking-tight text-zinc-900">Últimos registros</h2>
                <small class="text-xs text-zinc-500">Toque em qualquer horário com <strong class="text-emerald-700">✓</strong> para inspecionar a foto do ticket térmico.</small>
            </div>
            <?php if (!empty($ultimosRegistros)): ?>
                <a
                    href="/jornada"
                    class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 shadow-2xs transition-colors hover:bg-stone-50"
                >
                    Abrir espelho completo
                </a>
            <?php endif; ?>
        </header>

        <?php if (empty($ultimosRegistros)): ?>
            <article class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
                <p class="text-sm text-zinc-600">Você ainda não possui batidas registradas.</p>
                <a
                    href="/registrar"
                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-2xs transition-colors hover:bg-teal-800"
                >
                    Registrar primeira batida
                </a>
            </article>
        <?php else: ?>
            <div class="space-y-3 lg:hidden">
                <?php foreach ($ultimosRegistros as $item):
                    $ts = strtotime($item['data']);
                    $diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
                    $saldo = $item['saldo_minutos'];
                    $classeSaldo = RegistrarController::classeBadgeSaldo($saldo);
                    $comp = RegistrarController::statusComprovacao($item);
                    $classeComp = RegistrarController::CLASSES_COMPROVACAO[$comp['status']] ?? RegistrarController::CLASSES_COMPROVACAO['isento'];
                ?>
                    <article class="rounded-xl border border-stone-200/90 bg-white shadow-2xs">
                        <div class="px-4 py-3.5">
                            <header class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <strong class="font-mono text-sm font-bold text-zinc-900 tabular-nums"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                                    <span class="text-xs text-zinc-500"><?= $diaSem ?></span>
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

            <article class="hidden overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 bg-stone-50/90 text-xs font-semibold text-zinc-600">
                                <th class="px-4 py-3 whitespace-nowrap">Data</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Entrada</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Saída int.</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Retorno</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Saída</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Trabalhado</th>
                                <th class="px-3.5 py-3 whitespace-nowrap">Saldo</th>
                                <th class="px-4 py-3 whitespace-nowrap">Tickets</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200/80">
                            <?php foreach ($ultimosRegistros as $item):
                                $ts = strtotime($item['data']);
                                $diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
                                $saldo = $item['saldo_minutos'];
                                $classeSaldo = RegistrarController::classeBadgeSaldo($saldo);
                                $comp = RegistrarController::statusComprovacao($item);
                                $classeComp = RegistrarController::CLASSES_COMPROVACAO[$comp['status']] ?? RegistrarController::CLASSES_COMPROVACAO['isento'];
                            ?>
                                <tr class="transition-colors hover:bg-stone-50/80">
                                    <td class="px-4 py-3 font-mono text-xs whitespace-nowrap tabular-nums">
                                        <strong class="font-semibold text-zinc-900"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                                        <span class="ml-1 font-sans text-xs text-zinc-500"><?= $diaSem ?></span>
                                    </td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'entrada') ?></td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'saida_intervalo') ?></td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'retorno_intervalo') ?></td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums text-zinc-700"><?= RegistrarController::renderizarCelulaBatida($item, 'saida') ?></td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums">
                                        <strong class="font-semibold text-zinc-900"><?= RegistrarController::formatarMinutos($item['minutos_trabalhados']) ?></strong>
                                    </td>
                                    <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap tabular-nums">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $classeSaldo ?>">
                                            <?= $saldo === null ? 'Aberto' : RegistrarController::formatarMinutos($saldo, true) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold <?= $classeComp ?>">
                                            <?= htmlspecialchars($comp['rotulo']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        <?php endif; ?>
    </div>
</section>
