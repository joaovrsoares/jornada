<section class="space-y-5">
    <?php if (!empty($salvo)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Alterações salvas.
        </p>
    <?php endif; ?>

    <article class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 text-white shadow-md">
        <div class="px-5 py-4 sm:px-6">
            <h2 class="text-base font-bold tracking-tight text-white">Configurações</h2>
            <small class="mt-0.5 block text-xs text-zinc-400">Defina como suas horas diárias e tolerâncias devem ser calculadas no Painel, no Espelho e nos Relatórios.</small>
        </div>

        <div class="grid grid-cols-1 gap-px border-t border-zinc-800 bg-zinc-800 sm:grid-cols-3">
            <div class="bg-zinc-950 px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Identificação</span>
                <p class="mt-0.5 truncate text-lg font-bold text-white"><?= htmlspecialchars($nome) ?></p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">Cabeçalho do relatório</small>
            </div>

            <div class="bg-zinc-950 px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Jornada diária</span>
                <p class="mt-0.5 font-mono text-lg font-bold text-white tabular-nums"><?= htmlspecialchars($cargaHoraria) ?></p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">Segunda a sexta</small>
            </div>

            <div class="bg-zinc-950 px-5 py-4 sm:px-6">
                <span class="block text-[11px] font-medium text-zinc-400">Tolerância diária</span>
                <p class="mt-0.5 font-mono text-lg font-bold tabular-nums <?= (int) $tolerancia > 0 ? 'text-white' : 'text-zinc-500' ?>">
                    <?= (int) $tolerancia > 0 ? '±' . (int) $tolerancia . ' min' : 'Sem tolerância' ?>
                </p>
                <small class="mt-0.5 block text-[11px] text-zinc-500">Variações sem desconto</small>
            </div>
        </div>
    </article>

    <form method="post" action="/configuracoes" class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs">
        <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-3">
            <div class="lg:col-span-3">
                <label for="nome" class="block text-xs font-semibold text-zinc-800">Identificação na folha</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome) ?>"
                    required
                    maxlength="100"
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <small class="mt-1.5 block text-xs text-zinc-500">Seu nome ou matrícula para o cabeçalho do relatório de conferência.</small>
            </div>

            <div>
                <label for="carga_horaria" class="block text-xs font-semibold text-zinc-800">Jornada diária contratada (Seg a Sex)</label>
                <input
                    type="time"
                    id="carga_horaria"
                    name="carga_horaria"
                    value="<?= htmlspecialchars($cargaHoraria) ?>"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <small class="mt-1.5 block text-xs text-zinc-500">Horas por dia útil (ex.: 08:00 para 40h/sem ou 08:48 para 44h/sem). Sábados e domingos contam 100% como hora extra.</small>
            </div>

            <div>
                <label for="tolerancia_minutos" class="block text-xs font-semibold text-zinc-800">Tolerância diária (em minutos)</label>
                <input
                    type="number"
                    id="tolerancia_minutos"
                    name="tolerancia_minutos"
                    min="0"
                    max="60"
                    value="<?= (int) $tolerancia ?>"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 font-mono text-sm font-semibold text-zinc-900 tabular-nums shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <small class="mt-1.5 block text-xs text-zinc-500">Variações diárias até esse limite não geram hora extra nem desconto (padrão CLT: 10 min; use 0 para cálculo exato).</small>
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-stone-200/90 bg-stone-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <small class="text-xs text-zinc-500">As alterações passam a valer em todas as telas assim que forem salvas.</small>
            <button
                type="submit"
                class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto"
            >
                Salvar alterações
            </button>
        </div>
    </form>

    <article class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs">
        <div class="flex flex-col gap-3 border-b border-stone-200/90 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-sm font-bold tracking-tight text-zinc-900">Jornada — Controle de Ponto Pessoal</h2>
                <p class="mt-0.5 text-xs text-zinc-500">Registro independente de batidas, guarda de comprovantes térmicos e auditoria mensal.</p>
            </div>
            <span class="inline-flex shrink-0 items-center self-start rounded-lg bg-stone-100 px-3 py-1 font-mono text-xs font-semibold whitespace-nowrap text-zinc-700 ring-1 ring-inset ring-stone-200 sm:self-auto">
                Versão: <?= htmlspecialchars($versao) ?>
            </span>
        </div>

        <dl class="grid grid-cols-1 gap-4 p-5 text-sm sm:grid-cols-2 sm:p-6">
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Comprovação fotográfica</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Captura direta da câmera no celular e padronização automática dos comprovantes térmicos em WebP leve.</dd>
            </div>
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Confronto de espelho</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Apuração de horas trabalhadas, tolerância diária (art. 58 §1º CLT) e simulador de divergências com o RH.</dd>
            </div>
        </dl>
    </article>
</section>
