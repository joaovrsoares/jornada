<?php
    declare (strict_types=1);
    /** @var string $conteudo */
    /** @var string|null $titulo */
    /** @var string|null $rotaAtual */
    $rotaAtual = $rotaAtual ?? '/';
    $modoAtual = ($_GET['modo'] ?? 'individual') === 'completo' ? 'completo' : 'individual';
    $emRegistrar = str_starts_with($rotaAtual, '/registrar');
    $emJornada = str_starts_with($rotaAtual, '/jornada');
    $emRelatorios = str_starts_with($rotaAtual, '/relatorios');
    $emSobre = str_starts_with($rotaAtual, '/sobre');
    $emConfig = str_starts_with($rotaAtual, '/configuracoes');

    $navDesktopBase = 'inline-flex items-center gap-2 rounded-lg px-3.5 py-1.5 text-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600';
    $navDesktopAtivo = 'bg-white font-semibold text-zinc-900 shadow-2xs ring-1 ring-stone-200/90';
    $navDesktopInativo = 'font-medium text-zinc-600 hover:text-zinc-900';

    $navMobileBase = 'flex flex-col items-center justify-center gap-1 py-1.5 text-[11px] transition-colors focus:outline-none';
    $navMobileAtivo = 'font-semibold text-teal-700';
    $navMobileInativo = 'font-medium text-zinc-500 hover:text-zinc-900';
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Jornada') ?> — Jornada</title>
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/app.css') ?>">
</head>

<body class="flex min-h-dvh flex-col bg-stone-100 pb-20 font-sans text-base leading-normal text-zinc-900 antialiased selection:bg-teal-100 selection:text-teal-950 md:pb-0 print:bg-white print:pb-0">
    <header class="sticky top-0 z-30 border-b border-stone-200/90 bg-white/95 backdrop-blur-md print:hidden">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="/" class="group inline-flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-zinc-900 font-mono text-sm font-bold text-white shadow-2xs transition-transform group-hover:scale-[1.02]">
                        <span class="mr-0.5">J</span>
                        <span class="size-1.5 rounded-full bg-teal-400" aria-hidden="true"></span>
                    </span>
                    <div class="flex flex-col">
                        <span class="text-base font-bold tracking-tight text-zinc-900 leading-none">Jornada</span>
                        <span class="mt-0.5 font-mono text-[10px] font-medium text-zinc-500">ponto &amp; auditoria</span>
                    </div>
                </a>
            </div>

            <nav class="hidden md:flex md:items-center md:gap-1 md:rounded-xl md:bg-stone-100 md:p-1 md:ring-1 md:ring-inset md:ring-stone-200/80" aria-label="Navegação principal">
                <a href="/" class="<?= $navDesktopBase ?> <?= $rotaAtual === '/' ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Painel
                </a>
                <a href="/jornada" class="<?= $navDesktopBase ?> <?= $emJornada ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Espelho do mês
                </a>
                <a href="/relatorios" class="<?= $navDesktopBase ?> <?= $emRelatorios ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Relatórios
                </a>
                <a href="/sobre" class="<?= $navDesktopBase ?> <?= $emSobre ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Sobre
                </a>
            </nav>

            <div class="flex items-center gap-2">
                <?php if (!$emRegistrar): ?>
                    <a
                        href="/registrar"
                        class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-teal-700 px-3.5 py-2 text-xs font-semibold text-white shadow-2xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2"
                    >
                        <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                        </svg>
                        <span>Bater ponto</span>
                    </a>
                <?php endif; ?>

                <a
                    href="/sobre"
                    class="inline-flex size-9 items-center justify-center rounded-lg border transition-colors md:hidden <?= $emSobre ? 'border-teal-600/30 bg-teal-50 text-teal-800' : 'border-stone-200 bg-stone-50 text-zinc-600 hover:bg-stone-100 hover:text-zinc-900' ?>"
                    title="Sobre o sistema"
                    aria-label="Sobre o sistema"
                >
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                    </svg>
                </a>

                <a
                    href="/configuracoes"
                    class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-2 text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 <?= $emConfig ? 'border-teal-600/30 bg-teal-50 font-semibold text-teal-800' : 'border-stone-200 bg-stone-50 text-zinc-700 hover:bg-stone-100 hover:text-zinc-900' ?>"
                    title="Configurações de jornada"
                >
                    <svg class="size-4 text-zinc-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                    </svg>
                    <span class="hidden sm:inline">Ajustes</span>
                </a>
            </div>
        </div>
    </header>

    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 pt-6 pb-10 sm:px-6 sm:pt-8 print:max-w-none print:p-0">
        <header class="hidden print:mb-4 print:flex print:items-center print:justify-between print:border-b print:border-zinc-300 print:pb-2">
            <h1 class="text-lg font-bold tracking-tight text-zinc-900"><?= htmlspecialchars($titulo ?? 'Jornada') ?></h1>
            <span class="font-mono text-xs text-zinc-500 tabular-nums"><?= date('d/m/Y') ?></span>
        </header>

        <main class="flex-1">
            <?= $conteudo ?>
        </main>
    </div>

    <nav
        aria-label="Navegação rápida mobile"
        class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur-md md:hidden print:hidden"
    >
        <div class="mx-auto grid h-16 max-w-md grid-cols-5 items-center px-2">
            <a href="/" class="<?= $navMobileBase ?> <?= $rotaAtual === '/' ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                </svg>
                <span>Painel</span>
            </a>

            <a href="/jornada" class="<?= $navMobileBase ?> <?= $emJornada ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                </svg>
                <span>Espelho</span>
            </a>

            <a
                href="/registrar"
                class="flex flex-col items-center justify-center"
                aria-label="Registrar batida"
            >
                <span class="flex items-center justify-center gap-1 rounded-xl px-3.5 py-2 text-xs font-bold shadow-xs transition-colors <?= $emRegistrar ? 'bg-teal-700 text-white ring-2 ring-teal-600/30' : 'bg-zinc-900 text-white hover:bg-zinc-800' ?>">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                    </svg>
                    <span>Ponto</span>
                </span>
            </a>

            <a href="/relatorios" class="<?= $navMobileBase ?> <?= $emRelatorios ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" />
                </svg>
                <span>Confronto</span>
            </a>

            <a href="/configuracoes" class="<?= $navMobileBase ?> <?= $emConfig ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                </svg>
                <span>Ajustes</span>
            </a>
        </div>
    </nav>

    <dialog
        id="modal-comprovante"
        class="m-auto w-full max-w-xl overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 p-0 text-white shadow-2xl backdrop:bg-zinc-950/70 backdrop:backdrop-blur-[2px]"
    >
        <header class="flex items-center justify-between gap-4 border-b border-zinc-800 bg-zinc-900 px-5 py-3.5">
            <div class="flex items-center gap-2">
                <span class="size-2 rounded-full bg-emerald-400" aria-hidden="true"></span>
                <strong id="modal-titulo" class="font-mono text-xs font-semibold text-zinc-100">Comprovante de ponto</strong>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="#"
                    id="modal-download"
                    download
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-1.5 text-xs font-semibold text-zinc-100 transition-colors hover:bg-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                >
                    Baixar ticket
                </a>
                <button
                    type="button"
                    id="modal-fechar"
                    class="inline-flex cursor-pointer items-center justify-center rounded-lg bg-zinc-800 px-3 py-1.5 text-xs font-semibold text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
                >
                    Fechar
                </button>
            </div>
        </header>
        <div class="max-h-[78dvh] overflow-auto bg-stone-100 p-5 text-center">
            <img id="modal-imagem" src="" alt="Foto do ticket de ponto" class="mx-auto h-auto max-w-full rounded-lg border border-stone-300 bg-white shadow-md">
        </div>
    </dialog>

    <script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
</body>
</html>
