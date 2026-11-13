<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$cortarHora = static fn(?string $h): string => $h ? substr($h, 0, 5) : '';
$classesComprovacaoEscuro = [
    'comprovado' => 'bg-emerald-500/15 text-emerald-300 ring-1 ring-inset ring-emerald-400/30',
    'parcial' => 'bg-teal-500/15 text-teal-300 ring-1 ring-inset ring-teal-400/30',
    'pendente' => 'bg-amber-500/15 text-amber-300 ring-1 ring-inset ring-amber-400/30',
    'isento' => 'bg-zinc-800 text-zinc-400 ring-1 ring-inset ring-zinc-700',
];
$classesPasso = [
    'preenchida' => 'border-zinc-700/90 bg-zinc-800/90 text-white',
    'proxima' => 'border-teal-400 bg-teal-950/60 text-white ring-1 ring-teal-400/40',
    'pendente' => 'border-zinc-800 bg-zinc-900/60 text-zinc-500',
];
$diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$tsAlvo = strtotime($registroAtual['data']);
$diaAnterior = date('Y-m-d', strtotime($registroAtual['data'] . ' -1 day'));
$proximoDia = date('Y-m-d', strtotime($registroAtual['data'] . ' +1 day'));
$statusDia = RegistrarController::statusComprovacao($registroAtual);
$classeBadgeDia = $classesComprovacaoEscuro[$statusDia['status']] ?? $classesComprovacaoEscuro['isento'];
$dataParam = urlencode($registroAtual['data']);
$jsonMapaCalendario = htmlspecialchars(
    json_encode($mapaCalendario ?? [], JSON_UNESCAPED_UNICODE) ?: '{}',
    ENT_QUOTES,
    'UTF-8'
);
?>
<section class="mx-auto max-w-4xl space-y-5">
    <?php if (!empty($erro)): ?>
        <p role="alert" class="rounded-xl border border-red-200 border-l-4 border-l-red-600 bg-red-50 px-4 py-3.5 text-sm font-medium text-red-900 shadow-2xs">
            <?= htmlspecialchars($erro) ?>
        </p>
    <?php elseif (!empty($foto_removida)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Foto de <?= htmlspecialchars($foto_removida) ?> removida. O horário da batida foi mantido.
        </p>
    <?php elseif (!empty($salvo)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Ponto salvo para <?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?>.
        </p>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="inline-grid w-full grid-cols-2 rounded-xl bg-stone-200/80 p-1 sm:w-auto">
            <a
                href="/registrar?modo=individual&data=<?= $dataParam ?>"
                class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-xs font-semibold transition-all <?= $modo === 'individual' ? 'bg-white text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:text-zinc-900' ?>"
            >
                <span>Batida individual</span>
            </a>
            <a
                href="/registrar?modo=completo&data=<?= $dataParam ?>"
                class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-xs font-semibold transition-all <?= $modo === 'completo' ? 'bg-white text-zinc-900 shadow-2xs' : 'text-zinc-600 hover:text-zinc-900' ?>"
            >
                <span>Dia completo / Ocorrência</span>
            </a>
        </div>

        <?php if ($registroAtual['data'] !== $hoje): ?>
            <div class="flex items-center gap-2">
                <a
                    href="/jornada?mes=<?= urlencode(substr($registroAtual['data'], 0, 7)) ?>"
                    class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-2xs transition-colors hover:bg-stone-50 hover:text-zinc-900"
                >
                    Ver no espelho
                </a>
                <a
                    href="/registrar?modo=<?= urlencode($modo) ?>"
                    class="inline-flex items-center justify-center rounded-lg bg-teal-700 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-teal-800"
                >
                    Ir para hoje
                </a>
            </div>
        <?php endif; ?>
    </div>

    <article class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-xs">
        <header
            class="bg-zinc-900 px-5 py-5 text-white sm:px-6"
            id="widget-calendario"
            data-escopo="dia"
            data-rota-base="/registrar"
            data-data-selecionada="<?= htmlspecialchars($registroAtual['data']) ?>"
            data-mes-selecionado="<?= htmlspecialchars(substr($registroAtual['data'], 0, 7)) ?>"
            data-hoje="<?= htmlspecialchars($hoje) ?>"
            data-modo="<?= htmlspecialchars($modo) ?>"
            data-registros="<?= $jsonMapaCalendario ?>"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="font-mono text-xs font-medium text-teal-400">
                            <?= $registroAtual['data'] === $hoje ? 'HOJE' : 'DATA SELECIONADA' ?>
                        </span>
                        <span class="text-zinc-600" aria-hidden="true">·</span>
                        <span class="text-xs text-zinc-300"><?= $diasSemana[(int) date('w', $tsAlvo)] ?></span>
                        <?php if ($statusDia['batidas'] > 0): ?>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= $classeBadgeDia ?>">
                                <?= htmlspecialchars($statusDia['rotulo']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <a
                            href="/registrar?modo=<?= urlencode($modo) ?>&data=<?= urlencode($diaAnterior) ?>"
                            class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                            title="Dia anterior (<?= htmlspecialchars(date('d/m/Y', strtotime($diaAnterior))) ?>)"
                            aria-label="Dia anterior"
                        >
                            ‹
                        </a>

                        <button
                            type="button"
                            id="btn-calendario"
                            aria-expanded="false"
                            aria-controls="painel-calendario"
                            class="inline-flex h-9 cursor-pointer items-center gap-2.5 rounded-xl border border-zinc-700/90 bg-zinc-800/90 px-3.5 font-mono text-base font-bold tracking-tight text-white tabular-nums transition-colors hover:border-teal-500/60 hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 sm:text-lg"
                            title="Abrir calendário para escolher outra data"
                        >
                            <svg class="size-4 text-teal-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                            </svg>
                            <span><?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?></span>
                            <svg id="icone-calendario-seta" class="size-4 text-zinc-400 transition-transform duration-150" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <a
                            href="/registrar?modo=<?= urlencode($modo) ?>&data=<?= urlencode($proximoDia) ?>"
                            class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-700/90 bg-zinc-800/90 font-mono text-base font-bold text-zinc-300 transition-colors hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                            title="Próximo dia (<?= htmlspecialchars(date('d/m/Y', strtotime($proximoDia))) ?>)"
                            aria-label="Próximo dia"
                        >
                            ›
                        </a>
                    </div>
                </div>

                <?php if (!empty($assistente['saida_prevista'])): ?>
                    <div class="rounded-xl border border-zinc-700/80 bg-zinc-800/90 px-3.5 py-2 text-right">
                        <span class="block text-[11px] text-zinc-400">Saída prevista hoje</span>
                        <strong class="font-mono text-lg font-bold text-teal-300 tabular-nums"><?= htmlspecialchars($assistente['saida_prevista']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>

            <?php if ($modo === 'individual'): ?>
                <div class="mt-5 grid grid-cols-2 gap-2.5 sm:grid-cols-4" aria-label="Andamento do dia">
                    <?php
                    $indicePasso = 1;
                    foreach ($camposBatida as $campo => $rotulo):
                        $valor = $cortarHora($registroAtual[$campo] ?? null);
                        $temFoto = !empty($registroAtual['foto_' . $campo]);
                        $estado = $valor !== '' ? 'preenchida' : ($proximaBatida === $campo ? 'proxima' : 'pendente');
                        $classeCardPasso = $classesPasso[$estado];
                        $urlFoto = '/comprovante?data=' . urlencode($registroAtual['data']) . '&campo=' . urlencode($campo);
                    ?>
                        <div class="flex flex-col justify-between gap-1.5 rounded-xl border p-3 transition-colors <?= $classeCardPasso ?>">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[11px] font-medium text-zinc-400"><?= htmlspecialchars($rotulo) ?></span>
                                <span class="font-mono text-[10px] text-zinc-500">0<?= $indicePasso++ ?></span>
                            </div>
                            <strong class="font-mono text-xl font-bold tracking-tight tabular-nums">
                                <?= $valor !== '' ? htmlspecialchars($valor) : '—:—' ?>
                            </strong>
                            <?php if ($valor !== ''): ?>
                                <?php if ($temFoto): ?>
                                    <button
                                        type="button"
                                        class="mt-0.5 inline-flex cursor-pointer items-center gap-1 self-start text-left text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 hover:underline focus:outline-none"
                                        data-comprovante-url="<?= htmlspecialchars($urlFoto) ?>"
                                        data-comprovante-titulo="<?= htmlspecialchars($rotulo . ' — ' . date('d/m/Y', $tsAlvo) . ' às ' . $valor) ?>"
                                    >
                                        ✓ Ticket salvo (ver)
                                    </button>
                                <?php else: ?>
                                    <span class="mt-0.5 text-[11px] font-medium text-amber-400/90">Sem foto</span>
                                <?php endif; ?>
                            <?php elseif ($proximaBatida === $campo): ?>
                                <span class="mt-0.5 text-[11px] font-semibold text-teal-300">● Próxima batida</span>
                            <?php else: ?>
                                <span class="mt-0.5 text-[11px] text-zinc-600">Aguardando</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="p-5 sm:p-6 space-y-6">
            <?php if ($modo === 'individual'): ?>
                <?php if (!empty($assistente['saida_prevista']) || !empty($assistente['compensacao'])): ?>
                    <div class="space-y-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3.5 text-sm text-zinc-800">
                        <?php if (!empty($assistente['saida_prevista'])): ?>
                            <div class="flex flex-wrap items-baseline gap-1.5">
                                <span class="text-xs font-medium text-zinc-600">Previsão de encerramento:</span>
                                <strong class="font-mono text-sm font-bold text-zinc-900 tabular-nums"><?= htmlspecialchars($assistente['saida_prevista']) ?></strong>
                                <small class="text-xs text-zinc-500">(<?= htmlspecialchars($assistente['nota_intervalo']) ?>)</small>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($assistente['compensacao'])):
                            $comp = $assistente['compensacao'];
                            $classeDivisorComp = !empty($assistente['saida_prevista'])
                                ? 'border-t border-stone-200/80 pt-2'
                                : '';
                        ?>
                            <div class="flex flex-col gap-0.5 text-xs sm:text-sm <?= $classeDivisorComp ?>">
                                <span>
                                    Saldo acumulado no mês (<strong class="font-mono font-semibold text-red-700 tabular-nums">-<?= htmlspecialchars($comp['debito_total']) ?></strong>):
                                    <?php if ($comp['dias_necessarios'] === 1): ?>
                                        compensar <strong class="font-semibold text-zinc-900">+<?= (int) $comp['minutos_por_dia'] ?> min hoje</strong> quita o débito.
                                    <?php else: ?>
                                        sugestão de <strong class="font-semibold text-zinc-900">+<?= (int) $comp['minutos_por_dia'] ?> min/dia</strong> pelos próximos <strong class="font-semibold text-zinc-900"><?= (int) $comp['dias_necessarios'] ?> dias úteis</strong>.
                                    <?php endif; ?>
                                </span>
                                <?php if (!empty($comp['saida_com_compensacao'])): ?>
                                    <small class="text-xs text-zinc-500">
                                        Saindo às <strong class="font-mono font-semibold text-zinc-900 tabular-nums"><?= htmlspecialchars($comp['saida_com_compensacao']) ?></strong> hoje você cumpre a compensação sugerida.
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="/registrar" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="modo" value="individual">
                    <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                        <label class="block sm:col-span-5">
                            <span class="flex h-5 items-center justify-between text-xs font-semibold text-zinc-700">
                                <span>Horário da batida</span>
                                <button
                                    type="button"
                                    id="btn-agora"
                                    class="cursor-pointer rounded-md bg-teal-50 px-2 py-0.5 font-mono text-[11px] font-bold text-teal-700 transition-colors hover:bg-teal-100 focus:outline-none"
                                >
                                    Agora
                                </button>
                            </span>
                            <input
                                type="time"
                                name="hora"
                                id="input-hora"
                                value="<?= htmlspecialchars($agora) ?>"
                                <?= $proximaBatida === null ? 'disabled' : '' ?>
                                required
                                class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-zinc-400"
                            >
                        </label>

                        <label class="block sm:col-span-7">
                            <span class="flex h-5 items-center text-xs font-semibold text-zinc-700">Etapa da jornada</span>
                            <select
                                name="campo"
                                <?= $proximaBatida === null ? 'disabled' : '' ?>
                                class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15 disabled:cursor-not-allowed disabled:bg-stone-100 disabled:text-zinc-400"
                            >
                                <?php foreach ($camposBatida as $campo => $rotulo):
                                    $jaLancada = !empty($registroAtual[$campo]);
                                ?>
                                    <option
                                        value="<?= $campo ?>"
                                        <?= $proximaBatida === $campo ? 'selected' : '' ?>
                                        <?= $jaLancada ? 'disabled' : '' ?>
                                    >
                                        <?= htmlspecialchars($rotulo) ?>
                                        <?= $jaLancada ? ' (registrada às ' . $cortarHora($registroAtual[$campo]) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <?php if ($proximaBatida === null): ?>
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-4 text-sm text-zinc-700">
                            Todas as 4 batidas de <strong class="font-mono font-semibold"><?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?></strong> já foram registradas.
                            Para corrigir algum horário ou anexar comprovante, alterne para <a href="/registrar?modo=completo&data=<?= $dataParam ?>" class="font-semibold text-teal-700 hover:underline">Dia completo</a> ou acesse o <a href="/jornada?mes=<?= urlencode(substr($registroAtual['data'], 0, 7)) ?>" class="font-semibold text-teal-700 hover:underline">Espelho do mês</a>.
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50/70 p-4">
                            <label class="block cursor-pointer">
                                <span class="flex items-center gap-2 text-xs font-semibold text-zinc-800">
                                    <svg class="size-4 text-teal-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M1 8a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 8.07 3h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 16.07 6H17a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8Zm13.5 3a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM10 14a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Comprovante térmico da máquina (opcional)</span>
                                </span>
                                <input
                                    type="file"
                                    name="foto"
                                    accept="image/jpeg,image/png,image/webp"
                                    capture="environment"
                                    data-comprimir
                                    class="mt-2 block w-full cursor-pointer rounded-lg border border-stone-300 bg-white px-3 py-2 text-xs text-zinc-700 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-zinc-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-zinc-800 focus:border-teal-600 focus:outline-none"
                                >
                                <small class="mt-2 block text-xs text-zinc-500" data-status-foto>
                                    No celular, abre a câmera diretamente e converte para WebP leve (máx. 1200px) antes de enviar.
                                </small>
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-teal-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span>Registrar batida</span>
                        </button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <form method="post" action="/registrar" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="modo" value="completo">
                    <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">

                    <div class="max-w-sm">
                        <label class="block">
                            <span class="block text-xs font-semibold text-zinc-700">Tipo de ocorrência no dia</span>
                            <select
                                name="tipo"
                                class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                            >
                                <?php foreach (RegistrarController::ROTULOS_TIPO as $valor => $rotulo): ?>
                                    <option value="<?= $valor ?>" <?= ($registroAtual['tipo'] ?? 'normal') === $valor ? 'selected' : '' ?>>
                                        <?= $rotulo ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                        <?php
                        $numCol = 1;
                        foreach ($camposBatida as $campo => $rotulo):
                            $valor = $cortarHora($registroAtual[$campo] ?? null);
                            $temFoto = !empty($registroAtual['foto_' . $campo]);
                            $urlFoto = '/comprovante?data=' . urlencode($registroAtual['data']) . '&campo=' . urlencode($campo);
                        ?>
                            <div class="flex flex-col gap-3 rounded-xl border border-stone-200 bg-stone-50/80 p-3.5">
                                <label class="block">
                                    <span class="flex items-center justify-between text-xs font-semibold text-zinc-800">
                                        <span><?= htmlspecialchars($rotulo) ?></span>
                                        <span class="font-mono text-[10px] text-zinc-400">0<?= $numCol++ ?></span>
                                    </span>
                                    <input
                                        type="time"
                                        name="<?= $campo ?>"
                                        value="<?= htmlspecialchars($valor) ?>"
                                        class="mt-1.5 block w-full rounded-lg border border-stone-300 bg-white px-3 py-2 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                                    >
                                </label>

                                <label class="block">
                                    <span class="block text-[11px] font-medium text-zinc-500">Foto do ticket</span>
                                    <input
                                        type="file"
                                        name="foto_<?= $campo ?>"
                                        accept="image/jpeg,image/png,image/webp"
                                        data-comprimir
                                        class="mt-1 block w-full cursor-pointer rounded-lg border border-stone-300 bg-white px-2.5 py-1.5 text-xs text-zinc-700 file:mr-2 file:cursor-pointer file:rounded-sm file:border-0 file:bg-zinc-900 file:px-2 file:py-1 file:text-[11px] file:font-semibold file:text-white hover:file:bg-zinc-800"
                                    >
                                    <small class="mt-1 block text-[11px] text-zinc-500" data-status-foto></small>
                                </label>

                                <?php if ($temFoto): ?>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <button
                                            type="button"
                                            class="inline-flex cursor-pointer items-center gap-1 self-start rounded-md bg-emerald-50 px-2 py-1 text-left text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-100 focus:outline-none"
                                            data-comprovante-url="<?= htmlspecialchars($urlFoto) ?>"
                                            data-comprovante-titulo="<?= htmlspecialchars($rotulo . ' — ' . date('d/m/Y', $tsAlvo) . ' às ' . $valor) ?>"
                                        >
                                            ✓ Ver ticket salvo
                                        </button>
                                        <button
                                            type="submit"
                                            form="form-remover-<?= $campo ?>"
                                            class="inline-flex cursor-pointer items-center gap-1 self-start rounded-md border border-red-200 bg-white px-2 py-1 text-xs font-semibold text-red-700 transition-colors hover:border-red-300 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
                                        >
                                            Remover foto
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <label class="block">
                            <span class="block text-xs font-semibold text-zinc-700">Observação (opcional)</span>
                            <input
                                type="text"
                                name="observacao"
                                maxlength="255"
                                placeholder="Ex.: Relógio da empresa sem papel, consulta médica, hora extra autorizada"
                                value="<?= htmlspecialchars($registroAtual['observacao'] ?? '') ?>"
                                class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                            >
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Salvar dia completo
                    </button>
                </form>

                <?php foreach (array_keys($camposBatida) as $campoRemover): ?>
                    <form
                        id="form-remover-<?= $campoRemover ?>"
                        method="post"
                        action="/registrar/remover-foto"
                        class="hidden"
                        onsubmit="return confirm('Remover a foto do comprovante de <?= htmlspecialchars($camposBatida[$campoRemover]) ?>? O horário da batida será mantido.');"
                    >
                        <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">
                        <input type="hidden" name="remover_foto" value="<?= $campoRemover ?>">
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </article>
</section>
