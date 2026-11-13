<section class="mx-auto max-w-xl space-y-5">
    <?php if (!empty($salvo)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Alterações salvas.
        </p>
    <?php endif; ?>

    <form method="post" action="/configuracoes" class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs">
        <div class="border-b border-stone-200 bg-stone-50/70 px-6 py-4">
            <h2 class="text-sm font-bold text-zinc-900">Parâmetros da jornada e folha</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Defina como suas horas diárias e tolerâncias devem ser calculadas nos relatórios.</p>
        </div>

        <div class="space-y-5 p-6">
            <div>
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

            <div class="pt-1">
                <button
                    type="submit"
                    class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-2.5 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto"
                >
                    Salvar alterações
                </button>
            </div>
        </div>
    </form>
</section>
