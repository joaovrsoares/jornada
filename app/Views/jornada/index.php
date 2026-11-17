<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$nomeMesRef = RegistrarController::formatarMesAno($mes);
$mesAnterior = RegistrarController::deslocarMes($mes, -1);
$proximoMes = RegistrarController::deslocarMes($mes, 1);
$mesDeHoje = date('Y-m');
$jsonMapaCalendario = RegistrarController::jsonMapaCalendario($mapaCalendario ?? []);
?>
<section class="space-y-5">
    <?php if (!empty($excluido)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            <?= htmlspecialchars((string) $excluido) ?>
        </p>
    <?php elseif (!empty($salvo)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Registro atualizado.
        </p>
    <?php endif; ?>

    <article
        class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 text-white shadow-md"
        id="widget-calendario"
        data-escopo="mes"
        data-rota-base="/jornada"
        data-mes-selecionado="<?= htmlspecialchars($mes) ?>"
        data-hoje="<?= date('Y-m-d') ?>"
        data-registros="<?= $jsonMapaCalendario ?>"
    >
        <div class="px-5 py-4 sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-white">Espelho do mês</h2>
                    <small class="mt-0.5 block text-xs text-zinc-400">
                        Jornada base <span class="font-mono font-semibold text-zinc-200 tabular-nums"><?= RegistrarController::formatarMinutos($cargaDiaria) ?>/dia</span>
                        <?= !empty($tolerancia) ? ' · tolerância <span class="font-mono font-semibold text-zinc-200 tabular-nums">±' . (int) $tolerancia . ' min</span>' : '' ?>
                        · horários com <strong class="text-emerald-400">✓</strong> abrem o comprovante
                    </small>
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    <a
                        href="/jornada?mes=<?= urlencode($mesAnterior) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
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
                        class="inline-flex h-9 cursor-pointer items-center gap-2 rounded-xl border border-zinc-700/90 bg-zinc-800/90 px-3.5 text-sm font-bold text-white transition-colors hover:border-teal-500/60 hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Abrir calendário do sistema"
                    >
                        <?= RegistrarController::icone('calendario', 'size-4 text-teal-400') ?>
                        <span><?= htmlspecialchars($nomeMesRef) ?></span>
                        <?= RegistrarController::icone('chevron', 'size-4 text-zinc-400 transition-transform duration-150', ['id' => 'icone-calendario-seta']) ?>
                    </button>
                    <a
                        href="/jornada?mes=<?= urlencode($proximoMes) ?>"
                        class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Próximo mês (<?= htmlspecialchars(RegistrarController::formatarMesAno($proximoMes)) ?>)"
                        aria-label="Próximo mês"
                    >
                        ›
                    </a>
                    <a
                        href="/jornada?mes=<?= urlencode($mesDeHoje) ?>"
                        class="ml-auto inline-flex h-9 items-center justify-center rounded-xl border border-teal-500/30 bg-teal-950/60 px-3 text-xs font-semibold text-teal-300 transition-colors hover:bg-teal-900/70 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                        title="Ir para o mês atual"
                        aria-label="Ir para o mês atual"
                    >
                        Mês atual
                    </a>
                </div>
            </div>

            <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>
        </div>

        <div class="grid grid-cols-1 divide-y divide-zinc-800 border-t border-zinc-800 bg-zinc-950 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
            <div class="px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Horas registradas</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-2xl font-bold tracking-tight text-white tabular-nums">
                    <?= RegistrarController::formatarMinutos($totalTrabalhadoMes) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">
                    <?= count($registros) ?> <?= count($registros) === 1 ? 'dia lançado' : 'dias lançados' ?> em <?= htmlspecialchars($nomeMesRef) ?>
                </small>
            </div>

            <div class="px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Saldo do mês</span>
                <p class="mt-0.5 whitespace-nowrap font-mono text-2xl font-bold tracking-tight tabular-nums <?= $saldoMes > 0 ? 'text-emerald-400' : ($saldoMes < 0 ? 'text-red-600' : 'text-white') ?>">
                    <?= RegistrarController::formatarMinutos($saldoMes, true) ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">Confronto com a jornada diária</small>
            </div>
        </div>
    </article>

    <div class="space-y-3 lg:hidden">
        <?php foreach ($dias as $dia):
            $ts = strtotime($dia['data']);
            $ehHoje = $dia['eh_hoje'];
            $registro = $dia['registro'];

            if ($registro === null):
                $urlEditar = '/jornada/editar?data=' . urlencode($dia['data']);
        ?>
            <article class="flex items-center justify-between gap-3 rounded-xl border bg-white px-4 py-2.5 shadow-2xs <?= $ehHoje ? 'border-teal-600/30' : 'border-stone-200/80' ?>">
                <div class="flex min-w-0 flex-wrap items-baseline gap-x-2">
                    <strong class="font-mono text-sm font-bold tabular-nums <?= $ehHoje ? 'text-teal-800' : 'text-zinc-900' ?>"><?= $dia['dia'] ?></strong>
                    <span class="text-xs text-zinc-500"><?= $dia['dia_semana'] ?></span>
                    <?php if ($ehHoje): ?>
                        <span class="inline-flex items-center rounded-md bg-teal-50 px-1.5 py-0.5 text-[11px] font-semibold text-teal-800 ring-1 ring-inset ring-teal-600/20">Hoje</span>
                    <?php endif; ?>
                    <span class="text-xs text-zinc-400"><?= $dia['rotulo_estado'] ?></span>
                </div>
                <a
                    href="<?= $urlEditar ?>"
                    class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-zinc-600 transition-colors hover:border-teal-600/40 hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                    title="Lançar ponto"
                    aria-label="Lançar ponto de <?= htmlspecialchars($dia['dia']) ?>"
                >
                    <?= RegistrarController::icone('editar') ?>
                </a>
            </article>
        <?php else:
            $item = $registro;
            $mostrarAcoes = true;
            require __DIR__ . '/../layouts/cartao-dia.php';
        endif; endforeach; ?>
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
                            <th class="px-3 py-3 whitespace-nowrap">Comprovante</th>
                            <th class="px-3 py-3 whitespace-nowrap">Obs.</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200/80">
                        <?php foreach ($dias as $dia):
                            $ts = strtotime($dia['data']);
                            $ehHoje = $dia['eh_hoje'];
                            $registro = $dia['registro'];

                            if ($registro === null):
                        ?>
                            <tr class="text-zinc-500 <?= $ehHoje ? 'bg-teal-50/40' : '' ?>">
                                <td class="px-4 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <strong class="font-semibold text-zinc-700"><?= htmlspecialchars($dia['dia']) ?></strong>
                                    <span class="ml-1 font-sans text-xs text-zinc-500"><?= $dia['dia_semana'] ?></span>
                                    <?php if ($ehHoje): ?>
                                        <span class="ml-1 inline-flex items-center rounded-md bg-teal-50 px-1.5 py-0.5 text-[11px] font-semibold text-teal-800 ring-1 ring-inset ring-teal-600/20">Hoje</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-0.5 text-xs font-medium text-zinc-600 ring-1 ring-inset ring-stone-200"><?= $dia['rotulo_estado'] ?></span>
                                </td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 font-mono text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 text-xs text-zinc-400">—</td>
                                <td class="px-3 py-2.5 text-xs text-zinc-400">—</td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <a
                                        href="/jornada/editar?data=<?= urlencode($dia['data']) ?>"
                                        class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                                        title="Lançar ponto"
                                        aria-label="Lançar ponto de <?= htmlspecialchars($dia['dia']) ?>"
                                    >
                                        <?= RegistrarController::icone('editar') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php else:
                            $item = $registro;
                            $diaSem = $dia['dia_semana'];
                        ?>
                            <tr class="transition-colors <?= $ehHoje ? 'bg-teal-50/60 hover:bg-teal-50/80' : 'hover:bg-stone-50/80' ?>">
                                <td class="px-4 py-2.5 font-mono text-xs whitespace-nowrap tabular-nums">
                                    <strong class="font-semibold text-zinc-900"><?= htmlspecialchars(date('d/m', $ts)) ?></strong>
                                    <span class="ml-1 font-sans text-xs text-zinc-500"><?= $diaSem ?></span>
                                    <?php if ($ehHoje): ?>
                                        <span class="ml-1 inline-flex items-center rounded-md bg-teal-50 px-1.5 py-0.5 text-[11px] font-semibold text-teal-800 ring-1 ring-inset ring-teal-600/20">Hoje</span>
                                    <?php endif; ?>
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
                                <td class="max-w-48 truncate px-3 py-2.5 text-xs text-zinc-500" title="<?= htmlspecialchars($item['observacao'] ?? '') ?>">
                                    <?= htmlspecialchars($item['observacao'] ?? '—') ?>
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <?php $estilo = 'tabela'; require __DIR__ . '/../layouts/acoes-registro.php'; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>
</section>
