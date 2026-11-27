<?php
    declare (strict_types=1);

    use App\Controllers\RegistrarController;
    use App\Core\Auth;
    use App\Core\Csrf;

    /** @var string $conteudo */
    /** @var string|null $titulo */
    /** @var string|null $rotaAtual */
    $rotaAtual = $rotaAtual ?? '/';
    $usuarioAtual = Auth::usuario();
    $nomeUsuario = (string) ($usuarioAtual['nome_completo'] ?? 'Minha conta');
    $primeiroNome = trim((string) ($usuarioAtual['nome'] ?? '')) ?: $nomeUsuario;
    $emailUsuario = (string) ($usuarioAtual['email'] ?? '');
    $inicialUsuario = strtoupper(substr($primeiroNome, 0, 1) ?: 'J');
    $emRegistrar = str_starts_with($rotaAtual, '/registrar');
    $emJornada = str_starts_with($rotaAtual, '/jornada');
    $emRelatorios = str_starts_with($rotaAtual, '/relatorios');
    $emConfig = str_starts_with($rotaAtual, '/configuracoes');

    // Preserva o mês em exibição ao alternar entre Painel, Espelho e Relatórios.
    $mesNav = $mes ?? $mesAtual ?? null;
    if ($mesNav === null && $emRegistrar && isset($_GET['data']) && is_string($_GET['data']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['data'])) {
        $mesNav = substr($_GET['data'], 0, 7);
    }
    $mesNav = is_string($mesNav) && preg_match('/^\d{4}-\d{2}$/', $mesNav) ? $mesNav : null;
    $sufixoMes = $mesNav !== null ? '?mes=' . urlencode($mesNav) : '';

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
    <?php require __DIR__ . '/identidade-visual.php'; ?>
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/app.css') ?>">
</head>

<body class="flex min-h-dvh flex-col bg-stone-100 pb-20 font-sans text-base leading-normal text-zinc-900 antialiased selection:bg-teal-100 selection:text-teal-950 md:pb-0 print:bg-white print:pb-0">
    <header class="sticky top-0 z-30 border-b border-stone-200/90 bg-white/95 backdrop-blur-md print:hidden">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="/<?= $sufixoMes ?>" class="group inline-flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <img
                        src="/assets/brand/logo-jornada-mark.svg?v=<?= $versaoBrand ?>"
                        alt=""
                        width="36"
                        height="36"
                        class="size-9 rounded-lg shadow-2xs transition-transform group-hover:scale-[1.02]"
                    >
                    <div class="flex flex-col">
                        <span class="text-base font-bold tracking-tight text-zinc-900 leading-none">Jornada</span>
                        <span class="mt-0.5 font-mono text-[10px] font-medium text-zinc-500">ponto &amp; auditoria</span>
                    </div>
                </a>
            </div>

            <nav class="hidden md:flex md:items-center md:gap-1 md:rounded-xl md:bg-stone-100 md:p-1 md:ring-1 md:ring-inset md:ring-stone-200/80" aria-label="Navegação principal">
                <a href="/<?= $sufixoMes ?>" class="<?= $navDesktopBase ?> <?= $rotaAtual === '/' ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Painel
                </a>
                <a href="/registrar" class="<?= $navDesktopBase ?> <?= $emRegistrar ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Registrar
                </a>
                <a href="/jornada<?= $sufixoMes ?>" class="<?= $navDesktopBase ?> <?= $emJornada ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Espelho do mês
                </a>
                <a href="/relatorios<?= $sufixoMes ?>" class="<?= $navDesktopBase ?> <?= $emRelatorios ? $navDesktopAtivo : $navDesktopInativo ?>">
                    Relatórios
                </a>
            </nav>

            <div class="relative flex min-w-0 items-center justify-end" data-menu-perfil>
                <button
                    type="button"
                    data-menu-perfil-botao
                    aria-expanded="false"
                    aria-controls="menu-perfil-painel"
                    aria-label="Abrir menu da conta"
                    title="<?= htmlspecialchars($nomeUsuario) ?> — abrir menu da conta"
                    class="inline-flex min-w-0 cursor-pointer items-center gap-2 rounded-full p-0.5 text-sm font-semibold text-zinc-800 transition-colors hover:bg-stone-100 hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2 aria-expanded:bg-stone-100"
                >
                    <span class="max-w-24 truncate pl-1.5 md:max-w-40 lg:max-w-64">
                        <?= htmlspecialchars($primeiroNome) ?>
                    </span>
                    <span
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-xs font-bold text-white ring-1 ring-inset ring-zinc-700"
                        aria-hidden="true"
                    >
                        <?= htmlspecialchars($inicialUsuario) ?>
                    </span>
                </button>

                <div
                    id="menu-perfil-painel"
                    data-menu-perfil-painel
                    class="absolute top-full right-0 z-50 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-xl"
                >
                    <div class="border-b border-stone-200/90 px-4 py-3.5">
                        <p class="truncate text-sm font-bold text-zinc-900"><?= htmlspecialchars($nomeUsuario) ?></p>
                        <p class="mt-0.5 truncate text-[11px] text-zinc-500"><?= htmlspecialchars($emailUsuario !== '' ? $emailUsuario : 'Conta Jornada') ?></p>
                    </div>

                    <div class="p-1.5">
                        <a
                            href="/configuracoes"
                            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-stone-100 hover:text-zinc-900 focus:outline-none focus-visible:bg-stone-100 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal-600"
                        >
                            <?= RegistrarController::icone('ajustes', 'size-4 text-zinc-500') ?>
                            Ajustes
                        </a>

                        <form method="post" action="/logout" class="mt-0.5">
                            <?= Csrf::campo() ?>
                            <button
                                type="submit"
                                class="flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 transition-colors hover:bg-red-50 hover:text-red-800 focus:outline-none focus-visible:bg-red-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-red-600"
                            >
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>

                <noscript>
                    <span class="ml-2 flex items-center gap-2">
                        <a href="/configuracoes" class="rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-2 text-xs font-medium text-zinc-700 hover:bg-stone-100">Ajustes</a>
                        <form method="post" action="/logout">
                            <?= Csrf::campo() ?>
                            <button type="submit" class="cursor-pointer rounded-lg border border-stone-200 bg-stone-50 px-2.5 py-2 text-xs font-medium text-red-700 hover:bg-red-50">Sair</button>
                        </form>
                    </span>
                </noscript>
            </div>
        </div>
    </header>

    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 pt-6 pb-10 sm:px-6 sm:pt-8 print:max-w-none print:p-0">
        <header class="hidden print:mb-4 print:flex print:items-center print:justify-between print:border-b print:border-zinc-300 print:pb-2">
            <h1 class="text-lg font-bold tracking-tight text-zinc-900"><?= htmlspecialchars($titulo ?? 'Jornada') ?></h1>
            <span class="font-mono text-xs text-zinc-500 tabular-nums"><?= date('d/m/Y') ?></span>
        </header>

        <main class="flex-1">
            <h1 class="sr-only"><?= htmlspecialchars($titulo ?? 'Jornada') ?></h1>
            <?= $conteudo ?>
        </main>
    </div>

    <nav
        aria-label="Navegação rápida mobile"
        class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur-md md:hidden print:hidden"
    >
        <div class="mx-auto grid h-16 max-w-md grid-cols-5 items-center px-2">
            <a href="/<?= $sufixoMes ?>" class="<?= $navMobileBase ?> <?= $rotaAtual === '/' ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                </svg>
                <span>Painel</span>
            </a>

            <a href="/registrar" class="<?= $navMobileBase ?> <?= $emRegistrar ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                </svg>
                <span>Registrar</span>
            </a>

            <a href="/jornada<?= $sufixoMes ?>" class="<?= $navMobileBase ?> <?= $emJornada ? $navMobileAtivo : $navMobileInativo ?>">
                <?= RegistrarController::icone('calendario', 'size-5') ?>
                <span>Espelho</span>
            </a>

            <a href="/relatorios<?= $sufixoMes ?>" class="<?= $navMobileBase ?> <?= $emRelatorios ? $navMobileAtivo : $navMobileInativo ?>">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" />
                </svg>
                <span>Relatórios</span>
            </a>

            <a href="/configuracoes" class="<?= $navMobileBase ?> <?= $emConfig ? $navMobileAtivo : $navMobileInativo ?>">
                <?= RegistrarController::icone('ajustes', 'size-5') ?>
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
                    Baixar comprovante
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
            <img id="modal-imagem" src="" alt="Foto do comprovante de ponto" class="mx-auto h-auto max-w-full rounded-lg border border-stone-300 bg-white shadow-md">
        </div>
    </dialog>

    <dialog
        id="dialogo-confirmacao"
        aria-labelledby="confirmacao-titulo"
        aria-describedby="confirmacao-mensagem"
        class="m-auto w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 p-0 text-white shadow-2xl backdrop:bg-zinc-950/70 backdrop:backdrop-blur-[2px]"
    >
        <div class="flex items-start gap-3 px-5 pt-5 pb-4 sm:px-6">
            <span
                id="confirmacao-icone"
                class="mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-red-500/15 text-red-300 ring-1 ring-inset ring-red-400/30"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <div class="min-w-0">
                <strong id="confirmacao-titulo" class="block text-base font-bold tracking-tight text-white">Confirmar ação</strong>
                <p id="confirmacao-mensagem" class="mt-1 text-sm leading-relaxed text-zinc-400"></p>
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-zinc-800 bg-zinc-950 px-5 py-3.5 sm:flex-row sm:justify-end sm:px-6">
            <button
                type="button"
                id="confirmacao-cancelar"
                class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl border border-zinc-700 bg-zinc-800 px-4 py-2.5 text-sm font-semibold text-zinc-200 transition-colors hover:bg-zinc-700 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 sm:w-auto"
            >
                Cancelar
            </button>
            <button
                type="button"
                id="confirmacao-aceitar"
                class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-xs transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-900 sm:w-auto"
            >
                Confirmar
            </button>
        </div>
    </dialog>

    <script src="/assets/js/menu-perfil.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/menu-perfil.js') ?>"></script>
    <script src="/assets/js/pwa.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/pwa.js') ?>"></script>
    <script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/app.js') ?>"></script>
</body>
</html>
