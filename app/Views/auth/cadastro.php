<?php
declare(strict_types=1);

use App\Core\Csrf;

/** @var string $nome */
/** @var string $sobrenome */
/** @var string $email */
/** @var array<int, string> $erros */
/** @var array{id: int, nome: string}|null $contaLegada */
/** @var bool $vincularLegado */
$nome = $nome ?? '';
$sobrenome = $sobrenome ?? '';
$email = $email ?? '';
$erros = $erros ?? [];
$contaLegada = $contaLegada ?? null;
$vincularLegado = $vincularLegado ?? false;
?>
<section class="w-full max-w-lg">
    <div class="mb-5 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl">Criar sua conta</h1>
        <p class="mt-1.5 text-sm text-zinc-600">Um histórico de ponto separado, privado e acessível só para você.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-xs">
        <?php if ($erros !== []): ?>
            <div role="alert" class="border-b border-red-200 border-l-4 border-l-red-600 bg-red-50 px-5 py-3.5 text-sm text-red-900">
                <strong class="block font-semibold">Não foi possível criar a conta:</strong>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/cadastro" class="space-y-4 p-5 sm:p-6">
            <?= Csrf::campo() ?>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="nome" class="block text-xs font-semibold text-zinc-800">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($nome) ?>"
                        maxlength="100"
                        minlength="2"
                        autocomplete="given-name"
                        required
                        autofocus
                        class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                        placeholder="João Vitor"
                    >
                </div>

                <div>
                    <label for="sobrenome" class="block text-xs font-semibold text-zinc-800">Sobrenome</label>
                    <input
                        type="text"
                        id="sobrenome"
                        name="sobrenome"
                        value="<?= htmlspecialchars($sobrenome) ?>"
                        maxlength="100"
                        minlength="2"
                        autocomplete="family-name"
                        required
                        class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                        placeholder="Soares da Rosa"
                    >
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-zinc-800">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    maxlength="150"
                    autocomplete="email"
                    inputmode="email"
                    required
                    class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors placeholder:text-zinc-400 focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                    placeholder="voce@exemplo.com"
                >
                <small class="mt-1.5 block text-[11px] text-zinc-500">Será o seu login. Não compartilhamos seu e-mail.</small>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="senha" class="block text-xs font-semibold text-zinc-800">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                    >
                    <small class="mt-1.5 block text-[11px] text-zinc-500">Mínimo de 8 caracteres.</small>
                </div>

                <div>
                    <label for="confirmar_senha" class="block text-xs font-semibold text-zinc-800">Confirmar senha</label>
                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        class="mt-1.5 block w-full rounded-xl border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-zinc-900 shadow-2xs transition-colors focus:border-teal-600 focus:outline-none focus:ring-3 focus:ring-teal-600/15"
                    >
                </div>
            </div>

            <?php if ($contaLegada !== null): ?>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5">
                    <input
                        type="checkbox"
                        name="vincular_legado"
                        value="1"
                        <?= $vincularLegado ? 'checked' : '' ?>
                        class="mt-0.5 size-4 shrink-0 cursor-pointer rounded border-stone-300 text-teal-700 focus:ring-teal-600"
                    >
                    <span class="text-xs leading-relaxed text-amber-900">
                        <strong class="block font-semibold">Vincular o histórico de ponto existente</strong>
                        Encontramos uma conta antiga (“<?= htmlspecialchars($contaLegada['nome']) ?>”) sem login.
                        Marque para trazer os registros e comprovantes dela para esta nova conta.
                    </span>
                </label>
            <?php endif; ?>

            <button
                type="submit"
                class="inline-flex w-full cursor-pointer items-center justify-center rounded-xl bg-teal-700 px-5 py-3 text-sm font-bold text-white shadow-xs transition-colors hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2"
            >
                Criar conta e entrar
            </button>
        </form>

        <div class="border-t border-stone-200/90 bg-stone-50/70 px-5 py-4 text-center text-xs text-zinc-600 sm:px-6">
            Já tem uma conta?
            <a href="/login" class="font-semibold text-teal-700 underline decoration-teal-700/30 underline-offset-2 hover:text-teal-800">
                Entrar
            </a>
        </div>
    </div>
</section>
