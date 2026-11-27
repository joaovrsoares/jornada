<?php
declare(strict_types=1);

use App\Core\Csrf;

/** @var string $email */
/** @var string|null $erro */
$email = $email ?? '';
$erro = $erro ?? null;
?>
<section class="w-full max-w-md">
    <div class="mb-5 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">Entrar na sua conta</h1>
        <p class="mt-1.5 text-sm text-zinc-600">Seu ponto, seus comprovantes e seus relatórios em um só lugar.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-xs">
        <?php if ($erro !== null): ?>
            <p role="alert" class="border-b border-red-200 border-l-4 border-l-red-600 bg-red-50 px-5 py-3.5 text-sm font-medium text-red-900">
                <?= htmlspecialchars($erro) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="/login" class="space-y-4 p-5 sm:p-6">
            <?= Csrf::campo() ?>

            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-800">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    autocomplete="email"
                    inputmode="email"
                    required
                    autofocus
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                    placeholder="voce@exemplo.com"
                >
            </div>

            <div>
                <label for="senha" class="block text-xs font-semibold text-zinc-800">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    autocomplete="current-password"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                >
            </div>

            <button
                type="submit"
                class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2"
            >
                Entrar
            </button>
        </form>

        <div class="border-t border-stone-200/90 bg-stone-50/70 px-5 py-4 text-center text-xs text-zinc-600 sm:px-6">
            Ainda não tem conta?
            <a href="/cadastro" class="font-semibold text-teal-700 underline decoration-teal-700/30 underline-offset-2 hover:text-teal-800">
                Criar conta gratuitamente
            </a>
        </div>
    </div>

    <p class="mt-4 text-center text-[11px] leading-relaxed text-zinc-500">
        Ao entrar, você concorda que os registros de ponto e comprovantes ficam vinculados apenas à sua conta.
    </p>
</section>
