<section class="space-y-5">
    <?php if (!empty($erro)): ?>
        <p role="alert" class="rounded-xl border border-red-200 border-l-4 border-l-red-600 bg-red-50 px-4 py-3.5 text-sm font-medium text-red-900 shadow-2xs">
            <?= htmlspecialchars((string) $erro) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($senha_alterada)): ?>
        <p role="status" class="rounded-xl border border-emerald-200 border-l-4 border-l-emerald-600 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-2xs">
            Senha alterada com sucesso.
        </p>
    <?php endif; ?>

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
                <p class="mt-0.5 truncate text-lg font-bold text-white"><?= htmlspecialchars($nomeCompleto ?? $nome) ?></p>
                <small class="mt-0.5 block truncate text-[11px] text-zinc-500"><?= htmlspecialchars((string) ($email ?? 'Conta sem e-mail')) ?></small>
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
        <?= \App\Core\Csrf::campo() ?>
        <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-4">
            <div>
                <label for="nome" class="block text-xs font-semibold text-zinc-800">Nome</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome) ?>"
                    required
                    maxlength="100"
                    autocomplete="given-name"
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <small class="mt-1.5 block text-xs text-zinc-500">Aparece no cabeçalho do relatório.</small>
            </div>

            <div>
                <label for="sobrenome" class="block text-xs font-semibold text-zinc-800">Sobrenome</label>
                <input
                    type="text"
                    id="sobrenome"
                    name="sobrenome"
                    value="<?= htmlspecialchars($sobrenome ?? '') ?>"
                    required
                    maxlength="100"
                    autocomplete="family-name"
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm font-medium text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
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
        <div class="border-b border-stone-200/90 px-5 py-4 sm:px-6">
            <h2 class="text-sm font-bold tracking-tight text-zinc-900">Conta e segurança</h2>
            <p class="mt-0.5 text-xs text-zinc-500">
                E-mail de acesso: <strong class="font-semibold text-zinc-700"><?= htmlspecialchars((string) ($email ?? '—')) ?></strong>
            </p>
        </div>

        <form method="post" action="/configuracoes/senha" class="grid grid-cols-1 gap-4 p-5 sm:p-6 lg:grid-cols-3">
            <?= \App\Core\Csrf::campo() ?>

            <div>
                <label for="senha_atual" class="block text-xs font-semibold text-zinc-800">Senha atual</label>
                <input
                    type="password"
                    id="senha_atual"
                    name="senha_atual"
                    autocomplete="current-password"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
            </div>

            <div>
                <label for="nova_senha" class="block text-xs font-semibold text-zinc-800">Nova senha</label>
                <input
                    type="password"
                    id="nova_senha"
                    name="nova_senha"
                    minlength="8"
                    autocomplete="new-password"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
                <small class="mt-1.5 block text-[11px] text-zinc-500">Mínimo de 8 caracteres.</small>
            </div>

            <div>
                <label for="confirmar_nova_senha" class="block text-xs font-semibold text-zinc-800">Confirmar nova senha</label>
                <input
                    type="password"
                    id="confirmar_nova_senha"
                    name="confirmar_nova_senha"
                    minlength="8"
                    autocomplete="new-password"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
            </div>

            <div class="flex items-center justify-end lg:col-span-3">
                <button
                    type="submit"
                    class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl border border-teal-700 bg-white px-5 py-2.5 text-sm font-bold text-teal-800 shadow-2xs transition-colors hover:bg-teal-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 sm:w-auto"
                >
                    Alterar senha
                </button>
            </div>
        </form>
    </article>

    <article id="pwa-instalacao" class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-2xs">
        <div class="flex flex-col gap-3 border-b border-stone-200/90 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="flex items-center gap-2 text-sm font-bold tracking-tight text-zinc-900">
                    Instalar o Jornada
                    <span class="inline-flex items-center rounded-md bg-teal-50 px-1.5 py-0.5 font-mono text-[10px] font-bold text-teal-800 ring-1 ring-inset ring-teal-600/20">PWA</span>
                </h2>
                <p class="mt-0.5 text-xs text-zinc-500">Instale como aplicativo no celular ou no computador, com ícone próprio e abertura em tela cheia.</p>
            </div>

            <button
                type="button"
                id="pwa-instalar"
                style="display: none"
                class="inline-flex shrink-0 cursor-pointer items-center justify-center gap-2 rounded-xl bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Instalar aplicativo
            </button>
        </div>

        <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-3 sm:p-6">
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Android</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Use o botão acima ou o menu do Chrome/Edge → <strong class="font-semibold text-zinc-700">Instalar aplicativo</strong>.</dd>
            </div>
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">iPhone / iPad</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">No Safari, toque em <strong class="font-semibold text-zinc-700">Compartilhar</strong> → <strong class="font-semibold text-zinc-700">Adicionar à Tela de Início</strong>.</dd>
            </div>
            <div class="rounded-xl border border-stone-200/90 bg-stone-50/70 p-4">
                <dt class="text-xs font-semibold text-zinc-800">Desktop</dt>
                <dd class="mt-1 text-xs leading-relaxed text-zinc-600">Chrome/Edge: ícone de instalação na barra de endereço. Safari no macOS: <strong class="font-semibold text-zinc-700">Arquivo → Adicionar ao Dock</strong>.</dd>
            </div>
        </div>

        <p id="pwa-status" role="status" aria-live="polite" class="hidden border-t border-stone-200 bg-stone-50 px-5 py-3 text-xs font-medium text-teal-800 sm:px-6"></p>
    </article>

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
