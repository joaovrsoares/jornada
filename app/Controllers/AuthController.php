<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use PDOException;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('auth/login', [
            'titulo' => 'Entrar',
            'email' => '',
        ], 'autenticacao');
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $senha = (string) ($_POST['senha'] ?? '');

        if ($email === '' || $senha === '') {
            $this->renderizarLogin($email, 'Informe seu e-mail e sua senha.');
            return;
        }

        $stmt = Database::connection()->prepare(
            'SELECT id, nome, sobrenome, email, senha_hash, carga_diaria_minutos, tolerancia_minutos
             FROM usuarios
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        // password_verify também é executado para um hash descartável quando o
        // e-mail não existe, evitando revelar quais contas estão cadastradas.
        $hash = (string) ($usuario['senha_hash'] ?? '');
        $hashParaVerificar = $hash !== ''
            ? $hash
            : '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $senhaConfere = password_verify($senha, $hashParaVerificar);

        if (!$usuario || $hash === '' || !$senhaConfere) {
            $this->renderizarLogin($email, 'E-mail ou senha inválidos.');
            return;
        }

        unset($usuario['senha_hash']);

        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            Database::connection()
                ->prepare('UPDATE usuarios SET senha_hash = :hash WHERE id = :id')
                ->execute([
                    'hash' => password_hash($senha, PASSWORD_DEFAULT),
                    'id' => (int) $usuario['id'],
                ]);
        }

        Auth::login($usuario);
        $this->redirect('/');
    }

    public function cadastroForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('auth/cadastro', [
            'titulo' => 'Criar conta',
            'nome' => '',
            'sobrenome' => '',
            'email' => '',
            'contaLegada' => Auth::legadoDisponivel(),
        ], 'autenticacao');
    }

    public function cadastrar(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $sobrenome = trim((string) ($_POST['sobrenome'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $senha = (string) ($_POST['senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmar_senha'] ?? '');
        $contaLegada = Auth::legadoDisponivel();
        $vincularLegado = $contaLegada !== null && ($_POST['vincular_legado'] ?? '') === '1';

        $erros = [];

        if (strlen($nome) < 2 || strlen($nome) > 100) {
            $erros[] = 'Informe um nome entre 2 e 100 caracteres.';
        }

        if (strlen($sobrenome) < 2 || strlen($sobrenome) > 100) {
            $erros[] = 'Informe um sobrenome entre 2 e 100 caracteres.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif (Auth::emailEmUso($email)) {
            $erros[] = 'Já existe uma conta com este e-mail.';
        }

        if (strlen($senha) < 8) {
            $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
        }

        if ($senha !== $confirmacao) {
            $erros[] = 'A confirmação de senha não confere.';
        }

        if ($erros !== []) {
            $this->renderizarCadastro($nome, $sobrenome, $email, $erros, $contaLegada, $vincularLegado);
            return;
        }

        $pdo = Database::connection();
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $id = null;

            if ($vincularLegado && $contaLegada !== null) {
                // Adota a conta antiga (id 1) sem perder os registros de ponto.
                $stmt = $pdo->prepare(
                    "UPDATE usuarios
                     SET nome = :nome, sobrenome = :sobrenome, email = :email, senha_hash = :hash
                     WHERE id = :id
                       AND (email IS NULL OR email = '')
                       AND (senha_hash IS NULL OR senha_hash = '')"
                );
                $stmt->execute([
                    'nome' => $nome,
                    'sobrenome' => $sobrenome,
                    'email' => $email,
                    'hash' => $hash,
                    'id' => $contaLegada['id'],
                ]);

                if ($stmt->rowCount() > 0) {
                    $id = $contaLegada['id'];
                }
            }

            if ($id === null) {
                $stmt = $pdo->prepare(
                    'INSERT INTO usuarios (nome, sobrenome, email, senha_hash, carga_diaria_minutos, tolerancia_minutos)
                     VALUES (:nome, :sobrenome, :email, :hash, 480, 10)'
                );
                $stmt->execute([
                    'nome' => $nome,
                    'sobrenome' => $sobrenome,
                    'email' => $email,
                    'hash' => $hash,
                ]);
                $id = (int) $pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            $duplicado = (int) ($e->errorInfo[1] ?? 0) === 1062 || str_contains($e->getMessage(), 'Duplicate');

            $this->renderizarCadastro(
                $nome,
                $sobrenome,
                $email,
                [$duplicado ? 'Já existe uma conta com este e-mail.' : 'Não foi possível criar a conta. Tente novamente.'],
                $contaLegada,
                $vincularLegado
            );
            return;
        }

        $stmt = $pdo->prepare(
            'SELECT id, nome, sobrenome, email, carga_diaria_minutos, tolerancia_minutos
             FROM usuarios WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            $this->renderizarCadastro($nome, $sobrenome, $email, ['Não foi possível concluir o cadastro.'], $contaLegada, $vincularLegado);
            return;
        }

        Auth::login($usuario);
        $this->redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }

    private function renderizarLogin(string $email, string $erro): void
    {
        $this->view('auth/login', [
            'titulo' => 'Entrar',
            'email' => $email,
            'erro' => $erro,
        ], 'autenticacao');
    }

    /**
     * @param array<int, string> $erros
     * @param array{id: int, nome: string}|null $contaLegada
     */
    private function renderizarCadastro(
        string $nome,
        string $sobrenome,
        string $email,
        array $erros,
        ?array $contaLegada,
        bool $vincularLegado
    ): void {
        $this->view('auth/cadastro', [
            'titulo' => 'Criar conta',
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'email' => $email,
            'erros' => $erros,
            'contaLegada' => $contaLegada,
            'vincularLegado' => $vincularLegado,
        ], 'autenticacao');
    }
}
