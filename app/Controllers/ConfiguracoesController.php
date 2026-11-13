<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ConfiguracoesController extends Controller
{
    /**
     * @return array{nome: string, carga_diaria_minutos: int, tolerancia_minutos: int}
     */
    public static function obterUsuario(): array
    {
        $usuario = Database::connection()
            ->query('SELECT nome, carga_diaria_minutos, tolerancia_minutos FROM usuarios WHERE id = 1')
            ->fetch() ?: [];

        return [
            'nome' => (string) ($usuario['nome'] ?? 'Meu Ponto'),
            'carga_diaria_minutos' => (int) ($usuario['carga_diaria_minutos'] ?? 480),
            'tolerancia_minutos' => (int) ($usuario['tolerancia_minutos'] ?? 10),
        ];
    }

    public function index(): void
    {
        $usuario = self::obterUsuario();
        $minutos = $usuario['carga_diaria_minutos'];

        $this->view('configuracoes/index', [
            'titulo' => 'Configurações',
            'nome' => $usuario['nome'],
            'cargaHoraria' => sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60),
            'tolerancia' => $usuario['tolerancia_minutos'],
        ]);
    }

    public function salvar(): void
    {
        $nome = trim($_POST['nome'] ?? '') ?: 'Meu Ponto';
        $minutosTotais = max(1, RegistrarController::paraMinutos($_POST['carga_horaria'] ?? null) ?? 480);
        $tolerancia = max(0, min(60, (int) ($_POST['tolerancia_minutos'] ?? 10)));

        $stmt = Database::connection()->prepare(
            'UPDATE usuarios
             SET nome = :nome, carga_diaria_minutos = :minutos, tolerancia_minutos = :tolerancia
             WHERE id = 1'
        );
        $stmt->execute([
            'nome' => $nome,
            'minutos' => $minutosTotais,
            'tolerancia' => $tolerancia,
        ]);

        $this->redirect('/configuracoes', ['salvo' => true]);
    }
}
