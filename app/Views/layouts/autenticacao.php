<?php
declare(strict_types=1);

/** @var string $conteudo */
/** @var string|null $titulo */
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Acesso') ?> — Jornada</title>
    <?php require __DIR__ . '/identidade-visual.php'; ?>
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/app.css') ?>">
</head>

<body class="flex min-h-dvh flex-col bg-stone-100 font-sans text-base leading-normal text-zinc-900 antialiased selection:bg-teal-100 selection:text-teal-950">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-6 sm:px-6 sm:py-8">
        <header class="flex items-center justify-between gap-4">
            <a href="/login" class="group inline-flex items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                <img
                    src="/assets/brand/logo-jornada-mark.svg?v=<?= $versaoBrand ?>"
                    alt=""
                    width="36"
                    height="36"
                    class="size-9 rounded-lg shadow-2xs transition-transform group-hover:scale-[1.02]"
                >
                <div class="flex flex-col">
                    <span class="text-base font-bold leading-none tracking-tight text-zinc-900">Jornada</span>
                    <span class="mt-0.5 font-mono text-[10px] font-medium text-zinc-500">ponto &amp; auditoria</span>
                </div>
            </a>

            <span class="inline-flex items-center gap-1.5 rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-semibold text-zinc-600 ring-1 ring-inset ring-stone-200">
                <span class="size-1.5 rounded-full bg-teal-500" aria-hidden="true"></span>
                Acesso individual
            </span>
        </header>

        <main class="flex flex-1 items-center justify-center py-8 sm:py-10">
            <?= $conteudo ?>
        </main>

        <footer class="flex flex-col items-center justify-between gap-2 border-t border-stone-200 pt-5 text-center text-[11px] text-zinc-500 sm:flex-row sm:text-left">
            <p class="m-0">Cada conta acessa somente os próprios registros de ponto.</p>
            <p class="m-0 font-mono">Jornada · <?= date('Y') ?></p>
        </footer>
    </div>

    <script src="/assets/js/pwa.js?v=<?= filemtime(__DIR__ . '/../../../public/assets/js/pwa.js') ?>"></script>
</body>
</html>
