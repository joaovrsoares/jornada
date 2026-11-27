<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Proteção CSRF baseada em token de sessão.
 *
 * Todos os formulários POST enviam o campo `_csrf`; a validação acontece uma
 * única vez no bootstrap da aplicação, antes de qualquer regra de negócio.
 */
final class Csrf
{
    private const CHAVE_SESSAO = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::CHAVE_SESSAO]) || !is_string($_SESSION[self::CHAVE_SESSAO])) {
            $_SESSION[self::CHAVE_SESSAO] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CHAVE_SESSAO];
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="_csrf" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
            . '">';
    }

    public static function valido(): bool
    {
        $enviado = $_POST['_csrf'] ?? '';

        return is_string($enviado)
            && $enviado !== ''
            && hash_equals(self::token(), $enviado);
    }

    public static function exigir(): void
    {
        if (self::valido()) {
            return;
        }

        http_response_code(419);
        header('Content-Type: text/html; charset=UTF-8');

        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<title>Sessão expirada — Jornada</title></head>'
            . '<body style="margin:0;display:grid;min-height:100dvh;place-items:center;background:#f5f5f4;'
            . 'font-family:system-ui,-apple-system,Segoe UI,sans-serif;color:#18181b">'
            . '<main style="max-width:28rem;padding:2rem;text-align:center">'
            . '<h1 style="font-size:1.25rem;margin:0 0 .5rem">Sessão expirada</h1>'
            . '<p style="margin:0 0 1.25rem;color:#52525b;line-height:1.6">'
            . 'O formulário ficou aberto por muito tempo ou a página foi recarregada. '
            . 'Volte e envie novamente.</p>'
            . '<a href="/" style="display:inline-block;border-radius:.75rem;background:#0f766e;'
            . 'padding:.75rem 1.25rem;color:#fff;font-weight:700;text-decoration:none">Voltar ao Jornada</a>'
            . '</main></body></html>';
        exit;
    }
}
