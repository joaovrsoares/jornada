<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Autenticação por sessão.
 *
 * A sessão guarda apenas o id do usuário; os dados são recarregados do banco
 * a cada requisição para refletir alterações de nome, jornada e tolerância.
 */
final class Auth
{
    private static ?array $usuario = null;

    private static bool $carregado = false;

    public static function iniciar(): void
    {
        self::carregar();
    }

    public static function check(): bool
    {
        return self::usuario() !== null;
    }

    public static function id(): ?int
    {
        $usuario = self::usuario();

        return $usuario === null ? null : (int) $usuario['id'];
    }

    /**
     * @return array{id: int, nome: string, sobrenome: string, nome_completo: string, email: ?string, carga_diaria_minutos: int, tolerancia_minutos: int}|null
     */
    public static function usuario(): ?array
    {
        self::carregar();

        return self::$usuario;
    }

    public static function login(array $usuario): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Evita fixação de sessão: o id antigo (pré-login) deixa de valer.
            session_regenerate_id(true);
        }

        $_SESSION['usuario_id'] = (int) $usuario['id'];

        $usuario['nome'] = (string) ($usuario['nome'] ?? '');
        $usuario['sobrenome'] = (string) ($usuario['sobrenome'] ?? '');
        $usuario['nome_completo'] = trim($usuario['nome'] . ' ' . $usuario['sobrenome']);

        self::$usuario = $usuario;
        self::$carregado = true;
    }

    public static function logout(): void
    {
        self::$usuario = null;
        self::$carregado = true;

        $_SESSION = [];

        if (ini_get('session.use_cookies') !== '0') {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) ($params['secure'] ?? false),
                'httponly' => (bool) ($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function emailEmUso(string $email, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE email = :email';
        $parametros = ['email' => $email];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> :id';
            $parametros['id'] = $ignorarId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Localiza a conta criada antes do multiusuário (sem e-mail nem senha).
     * Ela pode ser adotada no primeiro cadastro para preservar o histórico.
     *
     * @return array{id: int, nome: string}|null
     */
    public static function contaLegada(): ?array
    {
        $stmt = Database::connection()->query(
            "SELECT id, nome
             FROM usuarios
             WHERE (email IS NULL OR email = '')
               AND (senha_hash IS NULL OR senha_hash = '')
             ORDER BY id ASC
             LIMIT 1"
        );

        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        return [
            'id' => (int) $usuario['id'],
            'nome' => (string) $usuario['nome'],
        ];
    }

    /**
     * A conta legada só é oferecida enquanto nenhuma conta com login existe,
     * evitando que um novo cadastro se aproprie de um histórico já vinculado.
     *
     * @return array{id: int, nome: string}|null
     */
    public static function legadoDisponivel(): ?array
    {
        $legado = self::contaLegada();

        if ($legado === null) {
            return null;
        }

        $comCredenciais = (int) Database::connection()
            ->query("SELECT COUNT(*) FROM usuarios WHERE email IS NOT NULL AND email <> ''")
            ->fetchColumn();

        return $comCredenciais === 0 ? $legado : null;
    }

    private static function carregar(): void
    {
        if (self::$carregado) {
            return;
        }

        self::$carregado = true;

        $id = (int) ($_SESSION['usuario_id'] ?? 0);

        if ($id <= 0) {
            return;
        }

        $stmt = Database::connection()->prepare(
            'SELECT id, nome, sobrenome, email, carga_diaria_minutos, tolerancia_minutos
             FROM usuarios
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            unset($_SESSION['usuario_id']);

            return;
        }

        $nome = (string) $usuario['nome'];
        $sobrenome = (string) ($usuario['sobrenome'] ?? '');

        self::$usuario = [
            'id' => (int) $usuario['id'],
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'nome_completo' => trim($nome . ' ' . $sobrenome),
            'email' => $usuario['email'] !== null ? (string) $usuario['email'] : null,
            'carga_diaria_minutos' => (int) $usuario['carga_diaria_minutos'],
            'tolerancia_minutos' => (int) $usuario['tolerancia_minutos'],
        ];
    }
}
