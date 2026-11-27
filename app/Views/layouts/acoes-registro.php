<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

/** @var array<string, mixed> $item */
/** @var string $mes */
/** @var string $estilo 'cartao' (mobile) ou 'tabela' (desktop) */

$tamanho = $estilo === 'tabela' ? 'size-8' : 'size-9';
$classeEditar = $estilo === 'tabela'
    ? "inline-flex {$tamanho} items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
    : "inline-flex {$tamanho} items-center justify-center rounded-lg border border-stone-200 bg-white text-zinc-600 transition-colors hover:border-teal-600/40 hover:bg-teal-50 hover:text-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600";
$classeExcluir = $estilo === 'tabela'
    ? "inline-flex {$tamanho} cursor-pointer items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600"
    : "inline-flex {$tamanho} cursor-pointer items-center justify-center rounded-lg border border-stone-200 bg-white text-zinc-600 transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600";

$dataBr = date('d/m/Y', strtotime((string) $item['data']));
?>
<a
    href="/jornada/editar?data=<?= urlencode($item['data']) ?>"
    class="<?= $classeEditar ?>"
    title="Editar"
    aria-label="Editar registro de <?= htmlspecialchars($dataBr) ?>"
>
    <?= RegistrarController::icone('editar') ?>
</a>
<form
    method="post"
    action="/registrar/excluir"
    class="inline-flex"
    data-confirmar="O registro de <?= $dataBr ?> será excluído permanentemente, junto com os comprovantes anexados."
    data-confirmar-titulo="Excluir registro?"
    data-confirmar-acao="Excluir"
>
    <?= \App\Core\Csrf::campo() ?>
    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
    <input type="hidden" name="mes" value="<?= htmlspecialchars($mes) ?>">
    <button
        type="submit"
        class="<?= $classeExcluir ?>"
        title="Excluir"
        aria-label="Excluir registro de <?= htmlspecialchars($dataBr) ?>"
    >
        <?= RegistrarController::icone('excluir') ?>
    </button>
</form>
