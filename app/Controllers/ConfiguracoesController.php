<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;

class ConfiguracoesController extends Controller
{
    /**
     * @return array{id: int, nome: string, sobrenome: string, nome_completo: string, email: ?string, carga_diaria_minutos: int, tolerancia_minutos: int}
     */
    public static function obterUsuario(): array
    {
        $usuario = Auth::usuario() ?? [];

        $nome = (string) ($usuario['nome'] ?? 'Meu Ponto');
        $sobrenome = (string) ($usuario['sobrenome'] ?? '');

        return [
            'id' => (int) ($usuario['id'] ?? 0),
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'nome_completo' => trim($nome . ' ' . $sobrenome),
            'email' => $usuario['email'] ?? null,
            'carga_diaria_minutos' => (int) ($usuario['carga_diaria_minutos'] ?? 480),
            'tolerancia_minutos' => (int) ($usuario['tolerancia_minutos'] ?? 10),
        ];
    }

    public function index(): void
    {
        $usuario = self::obterUsuario();
        $minutos = $usuario['carga_diaria_minutos'];

        $this->view('configuracoes/index', [
            'titulo' => 'Ajustes',
            'nome' => $usuario['nome'],
            'sobrenome' => $usuario['sobrenome'],
            'nomeCompleto' => $usuario['nome_completo'],
            'email' => $usuario['email'],
            'cargaHoraria' => RegistrarController::formatarHora($minutos),
            'tolerancia' => $usuario['tolerancia_minutos'],
            'versao' => 'dev',
        ]);
    }

    public function salvar(): void
    {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $sobrenome = trim((string) ($_POST['sobrenome'] ?? ''));
        $minutosTotais = max(1, RegistrarController::paraMinutos($_POST['carga_horaria'] ?? null) ?? 480);
        $tolerancia = max(0, min(60, (int) ($_POST['tolerancia_minutos'] ?? 10)));
        $usuarioId = Auth::id();

        if ($usuarioId === null) {
            $this->redirect('/login');
            return;
        }

        if ($nome === '' || $sobrenome === '') {
            $this->redirect('/configuracoes', ['erro' => 'Informe seu nome e sobrenome.']);
            return;
        }

        if (strlen($nome) > 100 || strlen($sobrenome) > 100) {
            $this->redirect('/configuracoes', ['erro' => 'Nome e sobrenome devem ter no máximo 100 caracteres.']);
            return;
        }

        $stmt = Database::connection()->prepare(
            'UPDATE usuarios
             SET nome = :nome, sobrenome = :sobrenome, carga_diaria_minutos = :minutos, tolerancia_minutos = :tolerancia
             WHERE id = :id'
        );
        $stmt->execute([
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'minutos' => $minutosTotais,
            'tolerancia' => $tolerancia,
            'id' => $usuarioId,
        ]);

        $this->redirect('/configuracoes', ['salvo' => true]);
    }

    public function alterarSenha(): void
    {
        $usuarioId = Auth::id();

        if ($usuarioId === null) {
            $this->redirect('/login');
            return;
        }

        $atual = (string) ($_POST['senha_atual'] ?? '');
        $nova = (string) ($_POST['nova_senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmar_nova_senha'] ?? '');

        $stmt = Database::connection()->prepare(
            'SELECT senha_hash FROM usuarios WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $usuarioId]);
        $hashAtual = $stmt->fetchColumn();

        if (!is_string($hashAtual) || !password_verify($atual, $hashAtual)) {
            $this->redirect('/configuracoes', ['erro' => 'A senha atual não confere.']);
            return;
        }

        if (strlen($nova) < 8) {
            $this->redirect('/configuracoes', ['erro' => 'A nova senha deve ter pelo menos 8 caracteres.']);
            return;
        }

        if ($nova !== $confirmacao) {
            $this->redirect('/configuracoes', ['erro' => 'A confirmação da nova senha não confere.']);
            return;
        }

        if ($nova === $atual) {
            $this->redirect('/configuracoes', ['erro' => 'A nova senha precisa ser diferente da atual.']);
            return;
        }

        Database::connection()
            ->prepare('UPDATE usuarios SET senha_hash = :hash WHERE id = :id')
            ->execute([
                'hash' => password_hash($nova, PASSWORD_DEFAULT),
                'id' => $usuarioId,
            ]);

        // Renova o id de sessão após alterar uma credencial.
        session_regenerate_id(true);

        $this->redirect('/configuracoes', ['senha_alterada' => true]);
    }
}
