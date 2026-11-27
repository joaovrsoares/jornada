<?php
declare(strict_types=1);

/*
 * Identidade visual compartilhada pelos layouts.
 * Centraliza favicons, manifest e metadados OpenGraph/Twitter.
 */

$host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'jornada.joaovrsoares.dev'));
$hostSemPorta = strtolower((string) preg_replace('/:\d+$/', '', $host));

$hostLocal = in_array($hostSemPorta, ['localhost', '127.0.0.1', '::1', '[::1]'], true)
    || str_ends_with($hostSemPorta, '.localhost');

$esquema = $hostLocal ? 'http' : 'https';
$urlBase = $esquema . '://' . ($host !== '' ? $host : 'jornada.joaovrsoares.dev');
$urlAtual = $urlBase . ($_SERVER['REQUEST_URI'] ?? '/');

$tituloSocial = 'Jornada';
$descricaoSocial = 'ponto & auditoria';

// Cache-busting único para os arquivos de marca. Quando qualquer asset muda,
// os links de favicon, manifest, logo e OpenGraph são renovados juntos.
$diretorioBrand = __DIR__ . '/../../../public/assets/brand';
$versaoBrand = 1;
foreach ([
    'logo-jornada-mark.svg',
    'og-jornada.png',
    'favicon.ico',
    'favicon.svg',
    'favicon-16x16.png',
    'favicon-32x32.png',
    'app-icon-192.png',
    'app-icon-512.png',
    'app-icon-maskable-192.png',
    'app-icon-maskable-512.png',
    'app-icon-monochrome-512.png',
    'apple-touch-icon-152x152.png',
    'apple-touch-icon-167x167.png',
    'apple-touch-icon-180x180.png',
    'apple-touch-icon.png',
    'safari-pinned-tab.svg',
    'site.webmanifest',
] as $arquivoBrand) {
    $modificadoEm = @filemtime($diretorioBrand . '/' . $arquivoBrand);
    if ($modificadoEm !== false) {
        $versaoBrand = max($versaoBrand, $modificadoEm);
    }
}

$imagemOg = $urlBase . '/assets/brand/og-jornada.png?v=' . $versaoBrand;

$e = static fn(string $valor): string => htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
?>
    <meta name="description" content="<?= $e($descricaoSocial) ?>">
    <meta name="application-name" content="Jornada">
    <meta name="theme-color" content="#18181b">
    <meta name="color-scheme" content="light">

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Jornada">
    <meta name="msapplication-TileColor" content="#18181b">
    <meta name="msapplication-TileImage" content="/assets/brand/app-icon-192.png?v=<?= $versaoBrand ?>">

    <link rel="icon" href="/assets/brand/favicon.ico?v=<?= $versaoBrand ?>" sizes="any">
    <link rel="icon" type="image/svg+xml" href="/assets/brand/favicon.svg?v=<?= $versaoBrand ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/brand/favicon-32x32.png?v=<?= $versaoBrand ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/brand/favicon-16x16.png?v=<?= $versaoBrand ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/brand/app-icon-192.png?v=<?= $versaoBrand ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/brand/apple-touch-icon-152x152.png?v=<?= $versaoBrand ?>">
    <link rel="apple-touch-icon" sizes="167x167" href="/assets/brand/apple-touch-icon-167x167.png?v=<?= $versaoBrand ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/brand/apple-touch-icon-180x180.png?v=<?= $versaoBrand ?>">
    <link rel="mask-icon" href="/assets/brand/safari-pinned-tab.svg?v=<?= $versaoBrand ?>" color="#18181b">
    <link rel="manifest" href="/assets/brand/site.webmanifest?v=<?= $versaoBrand ?>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Jornada">
    <meta property="og:title" content="<?= $e($tituloSocial) ?>">
    <meta property="og:description" content="<?= $e($descricaoSocial) ?>">
    <meta property="og:url" content="<?= $e($urlAtual) ?>">
    <meta property="og:image" content="<?= $e($imagemOg) ?>">
<?php if ($esquema === 'https'): ?>
    <meta property="og:image:secure_url" content="<?= $e($imagemOg) ?>">
<?php endif; ?>
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Jornada — ponto &amp; auditoria">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $e($tituloSocial) ?>">
    <meta name="twitter:description" content="<?= $e($descricaoSocial) ?>">
    <meta name="twitter:image" content="<?= $e($imagemOg) ?>">
    <meta name="twitter:image:alt" content="Jornada — ponto &amp; auditoria">
