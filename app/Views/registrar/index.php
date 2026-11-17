<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

$classesComprovacaoEscuro = RegistrarController::CLASSES_COMPROVACAO_ESCURO;
$classesPasso = [
    'preenchida' => 'border-zinc-700/90 bg-zinc-800/90 text-white',
    'proxima' => 'border-teal-400 bg-teal-950/60 text-white ring-1 ring-teal-400/40',
    'pendente' => 'border-zinc-800 bg-zinc-900/60 text-zinc-500',
];
$tsAlvo = strtotime($registroAtual['data']);
$diaAnterior = date('Y-m-d', strtotime($registroAtual['data'] . ' -1 day'));
$proximoDia = date('Y-m-d', strtotime($registroAtual['data'] . ' +1 day'));
$statusDia = RegistrarController::statusComprovacao($registroAtual);
$editandoDia = $modo === 'completo';
$urlBaseDia = $editandoDia ? '/jornada/editar' : '/registrar';
$classeBadgeDia = $classesComprovacaoEscuro[$statusDia['status']] ?? $classesComprovacaoEscuro['isento'];
$dataParam = urlencode($registroAtual['data']);
$urlEspelho = '/jornada?mes=' . urlencode(substr($registroAtual['data'], 0, 7));
$jsonMapaCalendario = RegistrarController::jsonMapaCalendario($mapaCalendario ?? []);
$tipoDia = (string) ($registroAtual['tipo'] ?? 'normal');
$ehOcorrencia = $tipoDia !== 'normal';
$rotuloTipoDia = RegistrarController::ROTULOS_TIPO[$tipoDia] ?? $tipoDia;
$classeTipoDiaEscuro = RegistrarController::CLASSES_TIPO_ESCURO[$tipoDia] ?? RegistrarController::CLASSES_TIPO_ESCURO['normal'];
$temHorarioDia = false;
foreach (array_keys($camposBatida) as $campoDia) {
    $temHorarioDia = $temHorarioDia || !empty($registroAtual[$campoDia]);
}
?>
<section class="space-y-5">
    <?php if (!empty($erro)): ?>
        <p role="alert" class="rounded-xl border border-red-200 border-l-4 border-l-red-600 bg-red-50 px-4 py-3.5 text-sm font-medium text-red-900 shadow-2xs">
            <?= htmlspecialchars($erro) ?>
        </p>
    <?php elseif (!empty($ocorrencia)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            <?= htmlspecialchars($ocorrencia) ?>
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

    <?php if ($registroAtual['data'] > $hoje): ?>
        <p class="rounded-xl border border-amber-200 border-l-4 border-l-amber-500 bg-amber-50 px-4 py-3.5 text-sm font-medium text-amber-900 shadow-2xs">
            Data no futuro (<?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?>). O lançamento é permitido, mas confira a data antes de salvar.
        </p>
    <?php endif; ?>

    <?php if ($editandoDia): ?>
        <nav aria-label="Trilha de navegação" class="flex flex-wrap items-center gap-1.5 text-xs text-zinc-500">
            <a
                href="<?= $urlEspelho ?>"
                class="rounded-sm font-medium text-zinc-600 transition-colors hover:text-zinc-900 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
            >
                Espelho do mês
            </a>
            <span aria-hidden="true">/</span>
            <span class="font-semibold text-zinc-900">Editando dia <?= date('d/m/Y', $tsAlvo) ?></span>
        </nav>
    <?php endif; ?>

    <?php if ($registroAtual['data'] !== $hoje): ?>
        <div class="flex flex-wrap items-center justify-end gap-2">
            <a
                href="<?= $urlEspelho ?>"
                class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-2xs transition-colors hover:bg-stone-50 hover:text-zinc-900"
            >
                Ver no espelho
            </a>
            <a
                href="<?= $urlBaseDia ?>"
                class="inline-flex items-center justify-center rounded-lg bg-teal-700 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-teal-800"
            >
                Ir para hoje
            </a>
        </div>
    <?php endif; ?>

    <article class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-xs">
        <header
            class="bg-zinc-900 px-5 py-5 text-white sm:px-6"
            id="widget-calendario"
            data-escopo="dia"
            data-rota-base="<?= $urlBaseDia ?>"
            data-data-selecionada="<?= htmlspecialchars($registroAtual['data']) ?>"
            data-mes-selecionado="<?= htmlspecialchars(substr($registroAtual['data'], 0, 7)) ?>"
            data-hoje="<?= htmlspecialchars($hoje) ?>"
            data-registros="<?= $jsonMapaCalendario ?>"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="font-mono text-xs font-medium text-teal-400">
                            <?php if ($editandoDia): ?>
                                <?= !empty($registroAtual['id']) ? 'EDITANDO DIA' : 'LANÇANDO DIA' ?>
                            <?php else: ?>
                                <?= $registroAtual['data'] === $hoje ? 'HOJE' : 'DATA SELECIONADA' ?>
                            <?php endif; ?>
                        </span>
                        <span class="text-zinc-600" aria-hidden="true">·</span>
                        <span class="text-xs text-zinc-300"><?= RegistrarController::DIAS_SEMANA_COMPLETOS[(int) date('w', $tsAlvo)] ?></span>
                        <?php if ($ehOcorrencia): ?>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= $classeTipoDiaEscuro ?>">
                                <?= htmlspecialchars($rotuloTipoDia) ?>
                            </span>
                        <?php elseif ($statusDia['batidas'] > 0): ?>
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= $classeBadgeDia ?>">
                                <?= htmlspecialchars($statusDia['rotulo']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <a
                            href="<?= $urlBaseDia ?>?data=<?= urlencode($diaAnterior) ?>"
                            class="inline-flex size-9 items-center justify-center rounded-xl border font-mono text-base font-bold transition-colors focus:outline-none focus-visible:ring-2 border-zinc-700/90 bg-zinc-800/90 text-zinc-300 hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus-visible:ring-teal-500"
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
                            class="inline-flex h-9 cursor-pointer items-center gap-2.5 rounded-xl border px-3.5 font-mono text-base font-bold tracking-tight tabular-nums transition-colors focus:outline-none focus-visible:ring-2 sm:text-lg border-zinc-700/90 bg-zinc-800/90 text-white hover:border-teal-500/60 hover:bg-zinc-800 focus-visible:ring-teal-500"
                            title="Abrir calendário para escolher outra data"
                        >
                            <?= RegistrarController::icone('calendario', 'size-4 text-teal-400') ?>
                            <span><?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?></span>
                            <?= RegistrarController::icone('chevron', 'size-4 transition-transform duration-150 text-zinc-400', ['id' => 'icone-calendario-seta']) ?>
                        </button>

                        <a
                            href="<?= $urlBaseDia ?>?data=<?= urlencode($proximoDia) ?>"
                            class="inline-flex size-9 items-center justify-center rounded-xl border font-mono text-base font-bold transition-colors focus:outline-none focus-visible:ring-2 border-zinc-700/90 bg-zinc-800/90 text-zinc-300 hover:border-zinc-600 hover:bg-zinc-700 hover:text-white focus-visible:ring-teal-500"
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
                        <strong class="font-mono text-lg font-bold tabular-nums text-teal-300"><?= htmlspecialchars($assistente['saida_prevista']) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <?php require __DIR__ . '/../layouts/calendario-painel.php'; ?>

            <?php if ($modo === 'individual'): ?>
                <?php if ($ehOcorrencia): ?>
                    <div class="mt-5 rounded-xl border border-zinc-700/80 bg-zinc-800/90 px-4 py-3">
                        <strong class="block text-sm font-bold text-white">Dia marcado como <?= htmlspecialchars($rotuloTipoDia) ?></strong>
                        <span class="mt-0.5 block text-xs text-zinc-400">Nenhum horário é esperado neste dia — o lançamento entra no espelho sem batidas.</span>
                    </div>
                <?php else: ?>
                <div class="mt-5 grid grid-cols-2 gap-2.5 sm:grid-cols-4" aria-label="Andamento do dia">
                    <?php
                    $indicePasso = 1;
                    foreach ($camposBatida as $campo => $rotulo):
                        $valor = RegistrarController::horaCurta($registroAtual[$campo] ?? null);
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
                                        ✓ Comprovante salvo (ver)
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
            <?php endif; ?>
        </header>

        <div class="p-5 sm:p-6 space-y-6">
            <?php if ($modo === 'individual'): ?>
                <?php if (!$ehOcorrencia): ?>
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
                            $diasRestantes = (int) $comp['dias_uteis_restantes'];
                            $rotuloRestantes = $diasRestantes === 0
                                ? 'sem dias úteis restantes no mês'
                                : $diasRestantes . ($diasRestantes === 1 ? ' dia útil restante' : ' dias úteis restantes');
                            $classeDivisorComp = !empty($assistente['saida_prevista'])
                                ? 'border-t border-stone-200/80 pt-2'
                                : '';
                        ?>
                            <div class="flex flex-col gap-0.5 text-xs sm:text-sm <?= $classeDivisorComp ?>">
                                <span>
                                    Saldo acumulado no mês (<strong class="font-mono font-semibold text-red-700 tabular-nums">-<?= htmlspecialchars($comp['debito_total']) ?></strong>):
                                    <?php if ($comp['dias_necessarios'] === 1): ?>
                                        compensar <strong class="font-semibold text-zinc-900">+<?= (int) $comp['minutos_por_dia'] ?> min hoje</strong> quita o débito.
                                    <?php elseif ($diasRestantes > 0 && $comp['dias_necessarios'] <= $diasRestantes): ?>
                                        sugestão de <strong class="font-semibold text-zinc-900">+<?= (int) $comp['minutos_por_dia'] ?> min/dia</strong> até o fim do mês (<strong class="font-semibold text-zinc-900"><?= htmlspecialchars($rotuloRestantes) ?></strong>).
                                    <?php else: ?>
                                        sugestão de <strong class="font-semibold text-zinc-900">+<?= (int) $comp['minutos_por_dia'] ?> min/dia</strong> pelos próximos <?= (int) $comp['dias_necessarios'] ?> dias úteis (<strong class="font-semibold text-zinc-900"><?= htmlspecialchars($rotuloRestantes) ?></strong>).
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
                                        <?= $jaLancada ? ' (registrada às ' . RegistrarController::horaCurta($registroAtual[$campo]) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <?php if ($proximaBatida === null): ?>
                        <div class="rounded-xl border border-stone-200 bg-stone-50 p-4 text-sm text-zinc-700">
                            Todas as 4 batidas de <strong class="font-mono font-semibold"><?= htmlspecialchars(date('d/m/Y', $tsAlvo)) ?></strong> já foram registradas.
                            <?php if (RegistrarController::ehUltimoDiaUtilDaSemana($registroAtual['data'])): ?>
                                <strong class="font-semibold text-zinc-900">Bom final de semana!</strong>
                            <?php endif; ?>
                            Para corrigir algum horário ou anexar comprovante, <a href="/jornada/editar?data=<?= $dataParam ?>" class="font-semibold text-teal-700 hover:underline">edite este dia no Espelho do mês</a>.
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-dashed border-stone-300 bg-stone-50/70 p-4">
                            <label class="block cursor-pointer">
                                <span class="flex items-center gap-2 text-xs font-semibold text-zinc-800">
                                    <?= RegistrarController::icone('camera', 'size-4 text-teal-700') ?>
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
                            <span>Bater ponto</span>
                        </button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>

                <div class="rounded-xl border border-stone-200 bg-stone-50/60 p-4 sm:p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <strong class="block text-sm font-bold text-zinc-900">Ocorrência no dia</strong>
                            <small class="mt-0.5 block text-xs text-zinc-500">Falta, folga, feriado ou atestado — sem precisar lançar horários.</small>
                        </div>
                        <?php if ($ehOcorrencia): ?>
                            <form method="post" action="/registrar/ocorrencia">
                                <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">
                                <input type="hidden" name="tipo" value="normal">
                                <button
                                    type="submit"
                                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-2xs transition-colors hover:bg-stone-50 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                                >
                                    Reverter para dia normal
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <form
                        method="post"
                        action="/registrar/ocorrencia"
                        class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end"
                        <?php if ($temHorarioDia): ?>
                            data-confirmar="Lançar esta ocorrência remove os horários e comprovantes já registrados em <?= date('d/m/Y', $tsAlvo) ?>."
                            data-confirmar-titulo="Substituir o dia por uma ocorrência?"
                            data-confirmar-acao="Lançar ocorrência"
                        <?php endif; ?>
                    >
                        <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">
                        <label class="block flex-1">
                            <span class="block text-xs font-semibold text-zinc-700">Tipo de ocorrência</span>
                            <select
                                name="tipo"
                                required
                                class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15 pr-10"
                            >
                                <option value="" disabled <?= $ehOcorrencia ? '' : 'selected' ?>>Selecione o tipo</option>
                                <?php foreach (RegistrarController::TIPOS_LANCAMENTO as $valorTipo): ?>
                                    <option value="<?= $valorTipo ?>" <?= $ehOcorrencia && $tipoDia === $valorTipo ? 'selected' : '' ?>>
                                        <?= RegistrarController::ROTULOS_TIPO[$valorTipo] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button
                            type="submit"
                            class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-bold text-zinc-800 shadow-2xs transition-colors hover:bg-stone-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 sm:w-auto"
                        >
                            <?= $ehOcorrencia ? 'Atualizar ocorrência' : 'Lançar ocorrência' ?>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <form method="post" action="/jornada/editar" enctype="multipart/form-data" class="space-y-5">
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
                            $valor = RegistrarController::horaCurta($registroAtual[$campo] ?? null);
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

                                <div data-foto-campo>
                                    <span class="block text-[11px] font-medium text-zinc-500">Comprovante</span>

                                    <?php if ($temFoto): ?>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                            <button
                                                type="button"
                                                class="inline-flex cursor-pointer items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20 transition-colors hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600"
                                                data-comprovante-url="<?= htmlspecialchars($urlFoto) ?>"
                                                data-comprovante-titulo="<?= htmlspecialchars($rotulo . ' — ' . date('d/m/Y', $tsAlvo) . ' às ' . $valor) ?>"
                                            >
                                                Ver comprovante
                                            </button>
                                            <button
                                                type="submit"
                                                form="form-remover-<?= $campo ?>"
                                                class="inline-flex cursor-pointer items-center gap-1 rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:border-red-300 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
                                            >
                                                Remover foto
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <label class="mt-1.5 inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-stone-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-zinc-800 shadow-2xs transition-colors hover:bg-stone-50 focus-within:ring-2 focus-within:ring-teal-600">
                                            <?= RegistrarController::icone('camera', 'size-3.5 text-zinc-500') ?>
                                            <span>Anexar foto</span>
                                            <input
                                                type="file"
                                                name="foto_<?= $campo ?>"
                                                accept="image/jpeg,image/png,image/webp"
                                                data-comprimir
                                                class="sr-only"
                                            >
                                        </label>
                                        <small class="mt-1 block text-[11px] text-zinc-500" data-status-foto></small>
                                    <?php endif; ?>
                                </div>
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

                    <div class="flex flex-col gap-3 border-t border-stone-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <button
                            type="submit"
                            class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Salvar dia
                        </button>

                        <?php if (!empty($registroAtual['id'])): ?>
                            <button
                                type="submit"
                                form="form-excluir-dia"
                                class="inline-flex w-full cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 shadow-2xs transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 sm:w-auto"
                            >
                                <?= RegistrarController::icone('excluir', 'size-4') ?>
                                Excluir dia
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (!empty($registroAtual['id'])): ?>
                    <form
                        id="form-excluir-dia"
                        method="post"
                        action="/registrar/excluir"
                        class="hidden"
                        data-confirmar="O registro de <?= date('d/m/Y', $tsAlvo) ?> será excluído permanentemente, junto com os comprovantes anexados."
                        data-confirmar-titulo="Excluir registro?"
                        data-confirmar-acao="Excluir"
                    >
                        <input type="hidden" name="id" value="<?= (int) $registroAtual['id'] ?>">
                        <input type="hidden" name="mes" value="<?= htmlspecialchars(substr($registroAtual['data'], 0, 7)) ?>">
                    </form>
                <?php endif; ?>

                <?php foreach (array_keys($camposBatida) as $campoRemover): ?>
                    <form
                        id="form-remover-<?= $campoRemover ?>"
                        method="post"
                        action="/registrar/remover-foto"
                        class="hidden"
                        data-confirmar="A foto do comprovante de <?= htmlspecialchars($camposBatida[$campoRemover]) ?> será removida. O horário da batida é mantido."
                        data-confirmar-titulo="Remover foto?"
                        data-confirmar-acao="Remover foto"
                    >
                        <input type="hidden" name="data" value="<?= htmlspecialchars($registroAtual['data']) ?>">
                        <input type="hidden" name="remover_foto" value="<?= $campoRemover ?>">
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </article>
</section>
