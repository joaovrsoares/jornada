<?php
declare(strict_types=1);

use App\Controllers\RegistrarController;

/** @var array<string, mixed> $item */
/** @var bool $ehHoje */
/** @var bool $mostrarAcoes */
/** @var string $mes */

$ts = strtotime((string) $item['data']);
$diaSem = RegistrarController::DIAS_SEMANA[(int) date('w', $ts)];
?>
<article class="rounded-xl border bg-white shadow-2xs <?= $ehHoje ? 'border-teal-600/30' : 'border-stone-200/90' ?>">
    <div class="px-4 py-3.5">
        <header class="flex flex-wrap items-start justify-between gap-2">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <strong class="font-mono text-sm font-bold text-zinc-900 tabular-nums"><?= htmlspecialchars(date('d/m/Y', $ts)) ?></strong>
                <span class="text-xs text-zinc-500"><?= $diaSem ?></span>
                <?php if ($ehHoje): ?>
                    <span class="inline-flex items-center rounded-md bg-teal-50 px-1.5 py-0.5 text-[11px] font-semibold text-teal-800 ring-1 ring-inset ring-teal-600/20">Hoje</span>
                <?php endif; ?>
                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[11px] font-medium <?= $item['classe_tipo'] ?>">
                    <?= htmlspecialchars($item['rotulo_tipo']) ?>
                </span>
            </div>
            <span
                class="inline-flex shrink-0 items-center rounded-md px-2 py-0.5 text-[11px] font-semibold <?= $item['classe_comprovacao'] ?>"
                title="<?= htmlspecialchars($item['texto_comprovacao']) ?>"
            >
                <?= htmlspecialchars($item['rotulo_comprovacao']) ?>
            </span>
        </header>

        <dl class="mt-3 grid grid-cols-4 divide-x divide-stone-200/80 overflow-hidden rounded-lg border border-stone-200/80 bg-stone-50/70">
            <?php foreach (RegistrarController::CAMPOS_BATIDA_CURTOS as $campo => $rotuloCurto): ?>
                <div class="min-w-0 px-2.5 py-2">
                    <dt class="truncate text-[10px] font-medium text-zinc-500"><?= $rotuloCurto ?></dt>
                    <dd class="mt-0.5 font-mono text-sm font-semibold text-zinc-900 tabular-nums">
                        <?= RegistrarController::renderizarCelulaBatida($item, $campo) ?>
                    </dd>
                </div>
            <?php endforeach; ?>
        </dl>

        <?php if (!empty($item['observacao'])): ?>
            <p class="mt-2.5 text-xs leading-relaxed text-zinc-500">
                <span class="font-medium text-zinc-600">Obs.:</span> <?= htmlspecialchars($item['observacao']) ?>
            </p>
        <?php endif; ?>

        <footer class="mt-3 flex flex-wrap items-center gap-2 <?= $mostrarAcoes ? 'justify-between' : '' ?>">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-zinc-500">
                    Trabalhado
                    <strong class="ml-1 font-mono text-xs font-semibold text-zinc-900 tabular-nums"><?= RegistrarController::formatarMinutos($item['minutos_trabalhados']) ?></strong>
                </span>
                <span class="inline-flex items-center rounded-md px-2 py-0.5 font-mono text-[11px] font-semibold tabular-nums <?= $item['classe_saldo'] ?>">
                    <?= $item['saldo_minutos'] === null ? 'Aberto' : RegistrarController::formatarMinutos($item['saldo_minutos'], true) ?>
                </span>
            </div>

            <?php if ($mostrarAcoes): ?>
                <div class="ml-auto inline-flex items-center gap-1">
                    <?php $estilo = 'cartao'; require __DIR__ . '/acoes-registro.php'; ?>
                </div>
            <?php endif; ?>
        </footer>
    </div>
</article>
