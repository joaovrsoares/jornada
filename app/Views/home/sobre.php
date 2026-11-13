<section class="mx-auto max-w-2xl">
    <article class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs">
        <div class="flex items-center justify-between gap-4 bg-zinc-900 px-6 py-5 text-white">
            <div>
                <h2 class="text-base font-bold text-white">Jornada — Controle de Ponto Pessoal</h2>
                <p class="mt-0.5 text-xs text-zinc-400">Registro independente de batidas, guarda de comprovantes térmicos e auditoria mensal.</p>
            </div>
            <span class="inline-flex shrink-0 items-center rounded-lg bg-zinc-800 px-3 py-1 font-mono text-xs font-semibold whitespace-nowrap text-teal-300 ring-1 ring-inset ring-zinc-700">
                Versão: <?= htmlspecialchars($versao) ?>
            </span>
        </div>

        <dl class="grid grid-cols-1 gap-4 p-6 text-sm sm:grid-cols-2">
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Comprovação fotográfica</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Captura direta da câmera no celular e padronização automática dos tickets térmicos em WebP leve.</dd>
            </div>
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Confronto de espelho</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Apuração de horas trabalhadas, tolerância diária (art. 58 §1º CLT) e simulador de divergências com o RH.</dd>
            </div>
        </dl>
    </article>
</section>
