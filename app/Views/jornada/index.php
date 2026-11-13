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
    <?php if (!empty($salvo)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Registro atualizado.
        </p>
    <?php endif; ?>

    <div
        class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs"
        id="widget-calendario"
        data-escopo="mes"
        data-rota-base="/jornada"
        data-mes-selecionado="<?= htmlspecialchars($mes) ?>"
        data-hoje="<?= date('Y-m-d') ?>"
        data-registros="<?= $jsonMapaCalendario ?>"
    >
        <div class="border-b border-stone-200/90 px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-zinc-900">Espelho mensal de batidas</h2>
                    <small class="mt-0.5 block text-xs text-zinc-500">
                        Jornada base: <span class="font-mono font-semibold text-zinc-800 tabular-nums"><?= RegistrarController::formatarMinutos($cargaDiaria) ?>/dia</span>
                        <?= !empty($tolerancia) ? ' · Tolerância: <span class="font-mono font-semibold text-zinc-800 tabular-nums">±' . (int) $tolerancia . ' min</span>' : '' ?>
                        · Clique nos horários com <strong class="text-emerald-700">✓</strong> para abrir a foto do ticket
                    </small>
                </div>

                <div class="flex items-center gap-1.5">
                    <a
                        href="/jornada?mes=<?= urlencode($mesAnterior) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-stone-300 bg-stone-50 font-mono text-base font-bold text-zinc-700 transition-colors hover:bg-stone-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
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
                        class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-xl border border-stone-300 bg-stone-50 px-3.5 text-sm font-bold text-zinc-900 transition-colors hover:border-teal-600/50 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
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
                        href="/jornada?mes=<?= urlencode($proximoMes) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-stone-300 bg-stone-50 font-mono text-base font-bold text-zinc-700 transition-colors hover:bg-stone-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                        title="Próximo mês (<?= htmlspecialchars(RegistrarController::formatarMesAno($proximoMes)) ?>)"
                        aria-label="Próximo mês"
                    >
                        ›
                    </a>
                </div>
            </div>

            <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>
        </div>

        <div class="grid grid-cols-1 divide-y divide-stone-200/90 bg-stone-50/50 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
            <div class="px-5 py-4 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Horas registradas no período</span>
                <div class="mt-1 flex items-baseline gap-2.5">
                    <p class="font-mono text-2xl font-bold tracking-tight text-zinc-900 tabular-nums sm:text-3xl">
                        <?= RegistrarController::formatarMinutos($totalTrabalhadoMes) ?>
                    </p>
                    <small class="text-xs text-zinc-500">
                        (<?= count($registros) ?> <?= count($registros) === 1 ? 'dia lançado' : 'dias lançados' ?>)
                    </small>
                </div>
            </div>

            <div class="px-5 py-4 sm:px-6">
                <span class="text-xs font-medium text-zinc-500">Saldo líquido do mês</span>
                <div class="mt-1 flex items-baseline gap-2.5">
                    <p class="font-mono text-2xl font-bold tracking-tight tabular-nums sm:text-3xl <?= $saldoMes > 0 ? 'text-emerald-700' : ($saldoMes < 0 ? 'text-red-700' : 'text-zinc-900') ?>">
                        <?= RegistrarController::formatarMinutos($saldoMes, true) ?>
                    </p>
                    <small class="text-xs text-zinc-500">Confronto com a jornada diária</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($registros)): ?>
        <article class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center">
            <p class="text-sm text-zinc-600">Nenhum ponto registrado em <?= htmlspecialchars($mes) ?>.</p>
            <a
                href="/registrar"
                class="mt-4 inline-flex items-center justify-center rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-2xs transition-colors hover:bg-teal-800"
            >
                Registrar primeira batida
            </a>
        </article>
    <?php else: ?>
        <div class="space-y-3 lg:hidden">
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

                        <footer class="mt-3 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs text-zinc-500">
                                    Trabalhado
                                    <strong class="ml-1 font-mono text-xs font-semibold text-zinc-900 tabular-nums"><?= RegistrarController::formatarMinutos($item['minutos_trabalhados']) ?></strong>
                                </span>
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 font-mono text-[11px] font-semibold tabular-nums <?= $classeSaldo ?>">
                                    <?= $saldo === null ? 'Aberto' : RegistrarController::formatarMinutos($saldo, true) ?>
                                </span>
                            </div>

                            <div class="ml-auto inline-flex items-center gap-1">
                                <a
                                    href="/registrar?modo=completo&data=<?= urlencode($item['data']) ?>"
                                    class="inline-flex size-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-zinc-600 transition-colors hover:border-teal-600/40 hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                                    title="Editar"
                                    aria-label="Editar registro de <?= htmlspecialchars(date('d/m/Y', $ts)) ?>"
                                >
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M21 20.9998H13M2.5 21.4998L8.04927 19.3655C8.40421 19.229 8.58168 19.1607 8.74772 19.0716C8.8952 18.9924 9.0358 18.901 9.16804 18.7984C9.31692 18.6829 9.45137 18.5484 9.72028 18.2795L21 6.99982C22.1046 5.89525 22.1046 4.10438 21 2.99981C19.8955 1.89525 18.1046 1.89524 17 2.99981L5.72028 14.2795C5.45138 14.5484 5.31692 14.6829 5.20139 14.8318C5.09877 14.964 5.0074 15.1046 4.92823 15.2521C4.83911 15.4181 4.77085 15.5956 4.63433 15.9506L2.5 21.4998ZM2.5 21.4998L4.55812 16.1488C4.7054 15.7659 4.77903 15.5744 4.90534 15.4867C5.01572 15.4101 5.1523 15.3811 5.2843 15.4063C5.43533 15.4351 5.58038 15.5802 5.87048 15.8703L8.12957 18.1294C8.41967 18.4195 8.56472 18.5645 8.59356 18.7155C8.61877 18.8475 8.58979 18.9841 8.51314 19.0945C8.42545 19.2208 8.23399 19.2944 7.85107 19.4417L2.5 21.4998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <form method="post" action="/registrar/excluir" class="inline-flex" onsubmit="return confirm('Excluir o registro do dia <?= date('d/m/Y', $ts) ?>?');">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="mes" value="<?= htmlspecialchars($mes) ?>">
                                    <button
                                        type="submit"
                                        class="inline-flex size-9 cursor-pointer items-center justify-center rounded-lg border border-stone-200 bg-white text-zinc-600 transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
                                        title="Excluir"
                                        aria-label="Excluir registro de <?= htmlspecialchars(date('d/m/Y', $ts)) ?>"
                                    >
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M16 6V5.2C16 4.0799 16 3.51984 15.782 3.09202C15.5903 2.71569 15.2843 2.40973 14.908 2.21799C14.4802 2 13.9201 2 12.8 2H11.2C10.0799 2 9.51984 2 9.09202 2.21799C8.71569 2.40973 8.40973 2.71569 8.21799 3.09202C8 3.51984 8 4.0799 8 5.2V6M10 11.5V16.5M14 11.5V16.5M3 6H21M19 6V17.2C19 18.8802 19 19.7202 18.673 20.362C18.3854 20.9265 17.9265 21.3854 17.362 21.673C16.7202 22 15.8802 22 14.2 22H9.8C8.11984 22 7.27976 22 6.63803 21.673C6.07354 21.3854 5.6146 20.9265 5.32698 20.362C5 19.7202 5 18.8802 5 17.2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
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
                            <th class="px-3 py-3 whitespace-nowrap">Tipo</th>
                            <th class="px-3 py-3 whitespace-nowrap">Entrada</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saída int.</th>
                            <th class="px-3 py-3 whitespace-nowrap">Retorno</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saída</th>
                            <th class="px-3 py-3 whitespace-nowrap">Total</th>
                            <th class="px-3 py-3 whitespace-nowrap">Saldo</th>
                            <th class="px-3 py-3 whitespace-nowrap">Tickets</th>
                            <th class="px-3 py-3 whitespace-nowrap">Obs.</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Ações</th>
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
                                    <strong class="font-semibold text-zinc-900"><?= htmlspecialchars(date('d/m', $ts)) ?></strong>
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
                                <td class="max-w-48 truncate px-3 py-2.5 text-xs text-zinc-500" title="<?= htmlspecialchars($item['observacao'] ?? '') ?>">
                                    <?= htmlspecialchars($item['observacao'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <a
                                            href="/registrar?modo=completo&data=<?= urlencode($item['data']) ?>"
                                            class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                                            title="Editar"
                                            aria-label="Editar registro de <?= htmlspecialchars(date('d/m/Y', $ts)) ?>"
                                        >
                                            <svg class="size-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21 20.9998H13M2.5 21.4998L8.04927 19.3655C8.40421 19.229 8.58168 19.1607 8.74772 19.0716C8.8952 18.9924 9.0358 18.901 9.16804 18.7984C9.31692 18.6829 9.45137 18.5484 9.72028 18.2795L21 6.99982C22.1046 5.89525 22.1046 4.10438 21 2.99981C19.8955 1.89525 18.1046 1.89524 17 2.99981L5.72028 14.2795C5.45138 14.5484 5.31692 14.6829 5.20139 14.8318C5.09877 14.964 5.0074 15.1046 4.92823 15.2521C4.83911 15.4181 4.77085 15.5956 4.63433 15.9506L2.5 21.4998ZM2.5 21.4998L4.55812 16.1488C4.7054 15.7659 4.77903 15.5744 4.90534 15.4867C5.01572 15.4101 5.1523 15.3811 5.2843 15.4063C5.43533 15.4351 5.58038 15.5802 5.87048 15.8703L8.12957 18.1294C8.41967 18.4195 8.56472 18.5645 8.59356 18.7155C8.61877 18.8475 8.58979 18.9841 8.51314 19.0945C8.42545 19.2208 8.23399 19.2944 7.85107 19.4417L2.5 21.4998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <form method="post" action="/registrar/excluir" class="inline-flex" onsubmit="return confirm('Excluir o registro do dia <?= date('d/m/Y', $ts) ?>?');">
                                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                            <input type="hidden" name="mes" value="<?= htmlspecialchars($mes) ?>">
                                            <button
                                                type="submit"
                                                class="inline-flex size-8 cursor-pointer items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
                                                title="Excluir"
                                                aria-label="Excluir registro de <?= htmlspecialchars(date('d/m/Y', $ts)) ?>"
                                            >
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16 6V5.2C16 4.0799 16 3.51984 15.782 3.09202C15.5903 2.71569 15.2843 2.40973 14.908 2.21799C14.4802 2 13.9201 2 12.8 2H11.2C10.0799 2 9.51984 2 9.09202 2.21799C8.71569 2.40973 8.40973 2.71569 8.21799 3.09202C8 3.51984 8 4.0799 8 5.2V6M10 11.5V16.5M14 11.5V16.5M3 6H21M19 6V17.2C19 18.8802 19 19.7202 18.673 20.362C18.3854 20.9265 17.9265 21.3854 17.362 21.673C16.7202 22 15.8802 22 14.2 22H9.8C8.11984 22 7.27976 22 6.63803 21.673C6.07354 21.3854 5.6146 20.9265 5.32698 20.362C5 19.7202 5 18.8802 5 17.2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>
    <?php endif; ?>
</section>
