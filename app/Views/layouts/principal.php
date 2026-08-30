<?php
    declare (strict_types=1);
    /** @var string $conteudo */
    /** @var string|null $titulo */
    # Layout principal do sistema, que será usado em todas as páginas
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Jornada') ?></title>
    <link rel="stylesheet" href="/assets/css/simple.min.css">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= filemtime(__DIR__ . '/../../../public/assets/css/app.css') ?>">
</head>

<body>
    <div class="app">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/"><h1>Jornada</h1></a>
            </div>

            <nav class="sidebar-nav">
                <a href="/">Dashboard</a>
                <a href="/jornada">Minha jornada</a>
                <a href="/registros">Registros</a>
                <a href="/relatorios">Relatórios</a>
                <a href="/sobre">Sobre</a>
            </nav>

            <div class="sidebar-footer">
                <a href="/configuracoes">Configurações</a>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div>
                    <h1><?= htmlspecialchars($titulo ?? 'Jornada') ?></h1>
                </div>
            </header>

            <main class="content">
                <?= $conteudo ?>
            </main>
        </div>

        <script src="/assets/js/app.js"></script>
    </div>
</body>
</html>
