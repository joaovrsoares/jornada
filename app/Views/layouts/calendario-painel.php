<div
    id="painel-calendario"
    class="mt-4 hidden rounded-2xl border border-zinc-700/90 bg-zinc-950 p-4 text-white shadow-xl"
    aria-label="Calendário do sistema"
>
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1.5">
            <button
                type="button"
                id="cal-mes-anterior"
                class="inline-flex size-8 cursor-pointer items-center justify-center rounded-lg border border-zinc-800 bg-zinc-900 font-mono text-sm font-bold text-zinc-300 transition-colors hover:border-zinc-700 hover:bg-zinc-800 hover:text-white"
                aria-label="Mês anterior"
            >
                ‹
            </button>
            <strong id="cal-titulo" class="min-w-40 text-center text-sm font-bold text-white"></strong>
            <button
                type="button"
                id="cal-proximo-mes"
                class="inline-flex size-8 cursor-pointer items-center justify-center rounded-lg border border-zinc-800 bg-zinc-900 font-mono text-sm font-bold text-zinc-300 transition-colors hover:border-zinc-700 hover:bg-zinc-800 hover:text-white"
                aria-label="Próximo mês"
            >
                ›
            </button>
        </div>

        <button
            type="button"
            id="cal-ir-hoje"
            class="cursor-pointer rounded-lg border border-teal-500/30 bg-teal-950/60 px-2.5 py-1 text-xs font-semibold text-teal-300 transition-colors hover:bg-teal-900/70 hover:text-white"
        >
            Hoje / Mês atual
        </button>
    </div>

    <div id="cal-grade-meses" class="mb-3.5 grid grid-cols-6 gap-1 border-b border-zinc-800/90 pb-3 sm:grid-cols-12"></div>

    <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-semibold text-zinc-500">
        <span>Dom</span>
        <span>Seg</span>
        <span>Ter</span>
        <span>Qua</span>
        <span>Qui</span>
        <span>Sex</span>
        <span>Sáb</span>
    </div>

    <div id="cal-grade" class="mt-1.5 grid grid-cols-7 gap-1"></div>

    <div class="mt-3.5 flex flex-wrap items-center justify-between gap-3 border-t border-zinc-800/90 pt-3 text-[11px] text-zinc-400">
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-emerald-400"></span>
                <span>Jornada completa</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-amber-400"></span>
                <span>Em aberto</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-sky-400"></span>
                <span>Folga / Feriado / Atest.</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-red-400"></span>
                <span>Falta</span>
            </span>
        </div>
        <span id="cal-dica" class="text-[11px] text-zinc-500"></span>
    </div>
</div>
