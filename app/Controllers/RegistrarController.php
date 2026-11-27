<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;

class RegistrarController extends Controller
{
    public const CAMPOS_BATIDA = [
        'entrada' => 'Entrada',
        'saida_intervalo' => 'Saída para intervalo',
        'retorno_intervalo' => 'Retorno do intervalo',
        'saida' => 'Saída',
    ];

    public const CAMPOS_BATIDA_CURTOS = [
        'entrada' => 'Entrada',
        'saida_intervalo' => 'Saí. int.',
        'retorno_intervalo' => 'Retorno',
        'saida' => 'Saída',
    ];

    public const ROTULOS_TIPO = [
        'normal' => 'Normal',
        'atestado' => 'Atestado',
        'folga' => 'Folga',
        'feriado' => 'Feriado',
        'falta' => 'Falta',
    ];

    public const DIAS_SEMANA = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    public const DIAS_SEMANA_COMPLETOS = [
        'Domingo',
        'Segunda-feira',
        'Terça-feira',
        'Quarta-feira',
        'Quinta-feira',
        'Sexta-feira',
        'Sábado',
    ];

    public const CLASSES_TIPO = [
        'normal' => 'bg-stone-100 text-zinc-700 ring-1 ring-inset ring-stone-300/80',
        'atestado' => 'bg-sky-50 text-sky-800 ring-1 ring-inset ring-sky-600/20',
        'folga' => 'bg-teal-50 text-teal-800 ring-1 ring-inset ring-teal-600/20',
        'feriado' => 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20',
        'falta' => 'bg-red-50 text-red-800 ring-1 ring-inset ring-red-600/20',
    ];

    public const CLASSES_TIPO_ESCURO = [
        'normal' => 'bg-zinc-800 text-zinc-300 ring-1 ring-inset ring-zinc-700',
        'atestado' => 'bg-sky-500/15 text-sky-300 ring-1 ring-inset ring-sky-400/30',
        'folga' => 'bg-teal-500/15 text-teal-300 ring-1 ring-inset ring-teal-400/30',
        'feriado' => 'bg-emerald-500/15 text-emerald-300 ring-1 ring-inset ring-emerald-400/30',
        'falta' => 'bg-red-500/15 text-red-300 ring-1 ring-inset ring-red-400/30',
    ];

    public const CLASSES_COMPROVACAO = [
        'comprovado' => 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20',
        'parcial' => 'bg-teal-50 text-teal-800 ring-1 ring-inset ring-teal-600/20',
        'pendente' => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20',
        'isento' => 'bg-stone-100 text-zinc-500 ring-1 ring-inset ring-stone-300/70',
    ];

    public const CLASSES_COMPROVACAO_ESCURO = [
        'comprovado' => 'bg-emerald-500/15 text-emerald-300 ring-1 ring-inset ring-emerald-400/30',
        'parcial' => 'bg-teal-500/15 text-teal-300 ring-1 ring-inset ring-teal-400/30',
        'pendente' => 'bg-amber-500/15 text-amber-300 ring-1 ring-inset ring-amber-400/30',
        'isento' => 'bg-zinc-800 text-zinc-400 ring-1 ring-inset ring-zinc-700',
    ];

    public const TIPOS_OCORRENCIA = ['atestado', 'feriado', 'folga'];

    /**
     * Ocorrências que podem ser lançadas direto no /registrar, sem horários.
     */
    public const TIPOS_LANCAMENTO = ['falta', 'folga', 'feriado', 'atestado'];

    public const REGISTRO_VAZIO = [
        'tipo' => 'normal',
        'entrada' => null,
        'saida_intervalo' => null,
        'retorno_intervalo' => null,
        'saida' => null,
        'foto_entrada' => null,
        'foto_saida_intervalo' => null,
        'foto_retorno_intervalo' => null,
        'foto_saida' => null,
        'observacao' => null,
    ];

    public function index(): void
    {
        // A edição do dia inteiro pertence ao Espelho do mês.
        if (($_GET['modo'] ?? '') === 'completo') {
            $data = (string) ($_GET['data'] ?? '');
            $this->redirect('/jornada/editar' . (self::dataValida($data) ? '?data=' . urlencode($data) : ''));
        }

        $this->renderizarFormulario('individual');
    }

    public function editarDia(): void
    {
        $this->renderizarFormulario('completo');
    }

    private function renderizarFormulario(string $modo): void
    {
        $pdo = Database::connection();
        $hoje = date('Y-m-d');
        $agora = date('H:i');

        $dataAlvo = (string) ($_GET['data'] ?? $hoje);
        if (!self::dataValida($dataAlvo)) {
            $dataAlvo = $hoje;
        }

        $usuario = ConfiguracoesController::obterUsuario();
        $usuarioId = $usuario['id'];
        $cargaDiaria = $usuario['carga_diaria_minutos'];
        $tolerancia = $usuario['tolerancia_minutos'];

        $stmtDia = $pdo->prepare(
            'SELECT * FROM registros_ponto WHERE usuario_id = :usuario_id AND data = :data'
        );
        $stmtDia->execute([
            'usuario_id' => $usuarioId,
            'data' => $dataAlvo,
        ]);
        $registroAtual = $stmtDia->fetch() ?: self::REGISTRO_VAZIO + ['data' => $dataAlvo];

        $proximaBatida = self::proximaBatida($registroAtual);

        $mesAlvo = substr($dataAlvo, 0, 7);
        $stmtTodos = $pdo->prepare(
            'SELECT data, tipo, entrada, saida_intervalo, retorno_intervalo, saida
             FROM registros_ponto WHERE usuario_id = :usuario_id'
        );
        $stmtTodos->execute(['usuario_id' => $usuarioId]);
        $todosRegistros = $stmtTodos->fetchAll();

        $saldoAnteriorMes = 0;
        foreach ($todosRegistros as $reg) {
            $dataReg = (string) $reg['data'];
            if (str_starts_with($dataReg, $mesAlvo) && $dataReg !== $dataAlvo) {
                $s = self::calcularSaldoDia($reg, $cargaDiaria, $tolerancia);
                if ($s !== null) {
                    $saldoAnteriorMes += $s;
                }
            }
        }

        $assistente = self::calcularAssistenteDia($registroAtual, $cargaDiaria, $saldoAnteriorMes);

        $this->view('registrar/index', [
            'titulo' => $modo === 'completo'
                ? 'Editando dia ' . date('d/m/Y', strtotime($dataAlvo))
                : 'Registrar ponto',
            'hoje' => $hoje,
            'agora' => $agora,
            'modo' => $modo,
            'camposBatida' => self::CAMPOS_BATIDA,
            'proximaBatida' => $proximaBatida,
            'registroAtual' => $registroAtual,
            'assistente' => $assistente,
            'mapaCalendario' => self::obterMapaCalendario($todosRegistros),
        ]);
    }

    public function salvar(): void
    {
        $this->salvarRegistro('individual');
    }

    public function salvarDia(): void
    {
        $this->salvarRegistro('completo');
    }

    private function salvarRegistro(string $modo): void
    {
        $pdo = Database::connection();
        $usuarioId = (int) Auth::id();
        $data = (string) ($_POST['data'] ?? date('Y-m-d'));
        if (!self::dataValida($data)) {
            $data = date('Y-m-d');
        }

        $urlVoltar = $modo === 'completo'
            ? '/jornada/editar?data=' . urlencode($data)
            : '/registrar?data=' . urlencode($data);

        $stmtDia = $pdo->prepare(
            'SELECT * FROM registros_ponto WHERE usuario_id = :usuario_id AND data = :data'
        );
        $stmtDia->execute([
            'usuario_id' => $usuarioId,
            'data' => $data,
        ]);
        $existente = $stmtDia->fetch() ?: self::REGISTRO_VAZIO;

        $limparHora = static fn(?string $v): ?string => $v === null ? null : (self::horaCurta(trim($v)) ?: null);

        $candidato = [
            'tipo' => $existente['tipo'] ?? 'normal',
            'entrada' => self::horaCurta($existente['entrada']) ?: null,
            'saida_intervalo' => self::horaCurta($existente['saida_intervalo']) ?: null,
            'retorno_intervalo' => self::horaCurta($existente['retorno_intervalo']) ?: null,
            'saida' => self::horaCurta($existente['saida']) ?: null,
            'foto_entrada' => $existente['foto_entrada'] ?? null,
            'foto_saida_intervalo' => $existente['foto_saida_intervalo'] ?? null,
            'foto_retorno_intervalo' => $existente['foto_retorno_intervalo'] ?? null,
            'foto_saida' => $existente['foto_saida'] ?? null,
            'observacao' => $existente['observacao'] ?? null,
        ];

        if ($modo === 'individual') {
            $tipoExistente = (string) ($existente['tipo'] ?? 'normal');
            if ($tipoExistente !== 'normal') {
                $rotuloTipo = self::ROTULOS_TIPO[$tipoExistente] ?? $tipoExistente;
                $this->redirect($urlVoltar, [
                    'erro' => "Este dia está marcado como {$rotuloTipo}. Reverta a ocorrência para registrar horários, ou edite o dia completo no Espelho do mês.",
                ]);
            }

            $campo = $_POST['campo'] ?? '';
            if (!array_key_exists($campo, self::CAMPOS_BATIDA)) {
                $this->redirect($urlVoltar, [
                    'erro' => 'Selecione uma etapa válida da jornada.',
                ]);
            }

            if (!empty($existente[$campo])) {
                $rotulo = self::CAMPOS_BATIDA[$campo];
                $horaExistente = self::horaCurta($existente[$campo]);
                $this->redirect($urlVoltar, [
                    'erro' => "{$rotulo} já foi registrada às {$horaExistente} neste dia. Para corrigir um horário já lançado, use o Espelho do mês.",
                ]);
            }

            $hora = $limparHora($_POST['hora'] ?? null) ?? date('H:i');
            $candidato[$campo] = $hora;

            $erro = self::validarBatidas($candidato);
            if ($erro !== null) {
                $this->redirect($urlVoltar, ['erro' => $erro]);
            }

            if (!empty($_FILES['foto']) && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                [$caminhoFoto, $erroFoto] = $this->processarUploadFoto($_FILES['foto'], $data, $campo, $candidato['foto_' . $campo]);
                if ($erroFoto !== null) {
                    $this->redirect($urlVoltar, ['erro' => $erroFoto]);
                }
                $candidato['foto_' . $campo] = $caminhoFoto;
            }
        } else {
            $tipo = $_POST['tipo'] ?? 'normal';
            if (!array_key_exists($tipo, self::ROTULOS_TIPO)) {
                $tipo = 'normal';
            }

            $candidato['tipo'] = $tipo;
            $candidato['entrada'] = $limparHora($_POST['entrada'] ?? null);
            $candidato['saida_intervalo'] = $limparHora($_POST['saida_intervalo'] ?? null);
            $candidato['retorno_intervalo'] = $limparHora($_POST['retorno_intervalo'] ?? null);
            $candidato['saida'] = $limparHora($_POST['saida'] ?? null);
            $candidato['observacao'] = trim($_POST['observacao'] ?? '') ?: null;

            $erro = self::validarBatidas($candidato);
            if ($erro !== null) {
                $this->redirect($urlVoltar, ['erro' => $erro]);
            }

            foreach (array_keys(self::CAMPOS_BATIDA) as $campo) {
                $colFoto = 'foto_' . $campo;
                $inputKey = 'foto_' . $campo;

                if ($candidato[$campo] === null && $candidato[$colFoto] !== null) {
                    $this->removerArquivoFoto($candidato[$colFoto]);
                    $candidato[$colFoto] = null;
                    continue;
                }

                if (!empty($_FILES[$inputKey]) && ($_FILES[$inputKey]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    if ($candidato[$campo] === null) {
                        $rotulo = self::CAMPOS_BATIDA[$campo];
                        $this->redirect($urlVoltar, [
                            'erro' => "Preencha o horário de {$rotulo} para anexar o comprovante.",
                        ]);
                    }

                    [$caminhoFoto, $erroFoto] = $this->processarUploadFoto($_FILES[$inputKey], $data, $campo, $candidato[$colFoto]);
                    if ($erroFoto !== null) {
                        $this->redirect($urlVoltar, ['erro' => $erroFoto]);
                    }
                    $candidato[$colFoto] = $caminhoFoto;
                }
            }
        }

        $stmt = $pdo->prepare(
            'INSERT INTO registros_ponto
                (usuario_id, data, tipo, entrada, saida_intervalo, retorno_intervalo, saida,
                 foto_entrada, foto_saida_intervalo, foto_retorno_intervalo, foto_saida, observacao)
             VALUES
                (:usuario_id, :data, :tipo, :entrada, :saida_intervalo, :retorno_intervalo, :saida,
                 :foto_entrada, :foto_saida_intervalo, :foto_retorno_intervalo, :foto_saida, :observacao)
             ON DUPLICATE KEY UPDATE
                tipo = VALUES(tipo),
                entrada = VALUES(entrada),
                saida_intervalo = VALUES(saida_intervalo),
                retorno_intervalo = VALUES(retorno_intervalo),
                saida = VALUES(saida),
                foto_entrada = VALUES(foto_entrada),
                foto_saida_intervalo = VALUES(foto_saida_intervalo),
                foto_retorno_intervalo = VALUES(foto_retorno_intervalo),
                foto_saida = VALUES(foto_saida),
                observacao = VALUES(observacao)'
        );

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'data' => $data,
            'tipo' => $candidato['tipo'],
            'entrada' => $candidato['entrada'],
            'saida_intervalo' => $candidato['saida_intervalo'],
            'retorno_intervalo' => $candidato['retorno_intervalo'],
            'saida' => $candidato['saida'],
            'foto_entrada' => $candidato['foto_entrada'],
            'foto_saida_intervalo' => $candidato['foto_saida_intervalo'],
            'foto_retorno_intervalo' => $candidato['foto_retorno_intervalo'],
            'foto_saida' => $candidato['foto_saida'],
            'observacao' => $candidato['observacao'],
        ]);

        $this->redirect($urlVoltar, ['salvo' => true]);
    }

    /**
     * Lança uma ocorrência (falta, folga, feriado ou atestado) direto no
     * /registrar, sem exigir horários. Também reverte um dia para normal.
     */
    public function salvarOcorrencia(): void
    {
        $pdo = Database::connection();
        $usuarioId = (int) Auth::id();
        $data = (string) ($_POST['data'] ?? date('Y-m-d'));
        if (!self::dataValida($data)) {
            $data = date('Y-m-d');
        }

        $urlVoltar = '/registrar?data=' . urlencode($data);
        $tipo = (string) ($_POST['tipo'] ?? '');

        $stmtDia = $pdo->prepare(
            'SELECT * FROM registros_ponto WHERE usuario_id = :usuario_id AND data = :data'
        );
        $stmtDia->execute([
            'usuario_id' => $usuarioId,
            'data' => $data,
        ]);
        $existente = $stmtDia->fetch();

        if ($tipo === 'normal') {
            if ($existente === false) {
                $this->redirect($urlVoltar);
            }

            $pdo->prepare(
                'UPDATE registros_ponto SET tipo = :tipo WHERE usuario_id = :usuario_id AND data = :data'
            )->execute([
                'tipo' => 'normal',
                'usuario_id' => $usuarioId,
                'data' => $data,
            ]);

            $this->redirect($urlVoltar, [
                'ocorrencia' => 'Ocorrência removida. ' . date('d/m/Y', strtotime($data)) . ' voltou a ser um dia normal.',
            ]);
        }

        if (!in_array($tipo, self::TIPOS_LANCAMENTO, true)) {
            $this->redirect($urlVoltar, ['erro' => 'Selecione o tipo de ocorrência que deseja lançar.']);
        }

        // Uma ocorrência é um dia sem batidas: os horários e comprovantes que
        // porventura existam são removidos para não ficarem horários fantasmas.
        $fotosAnteriores = [];
        $tinhaHorario = false;
        if ($existente !== false) {
            foreach (array_keys(self::CAMPOS_BATIDA) as $campo) {
                $tinhaHorario = $tinhaHorario || !empty($existente[$campo]);
                $fotosAnteriores[] = $existente['foto_' . $campo] ?? null;
            }
        }

        $stmt = $pdo->prepare(
            'INSERT INTO registros_ponto
                (usuario_id, data, tipo, entrada, saida_intervalo, retorno_intervalo, saida,
                 foto_entrada, foto_saida_intervalo, foto_retorno_intervalo, foto_saida, observacao)
             VALUES
                (:usuario_id, :data, :tipo, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, :observacao)
             ON DUPLICATE KEY UPDATE
                tipo = VALUES(tipo),
                entrada = NULL,
                saida_intervalo = NULL,
                retorno_intervalo = NULL,
                saida = NULL,
                foto_entrada = NULL,
                foto_saida_intervalo = NULL,
                foto_retorno_intervalo = NULL,
                foto_saida = NULL,
                observacao = VALUES(observacao)'
        );

        $stmt->execute([
            'usuario_id' => $usuarioId,
            'data' => $data,
            'tipo' => $tipo,
            'observacao' => $existente['observacao'] ?? null,
        ]);

        foreach ($fotosAnteriores as $foto) {
            $this->removerArquivoFoto($foto);
        }

        $this->redirect($urlVoltar, [
            'ocorrencia' => 'Ocorrência registrada: ' . self::ROTULOS_TIPO[$tipo] . ' em ' . date('d/m/Y', strtotime($data)) . '.'
                . ($tinhaHorario ? ' Os horários anteriores foram removidos.' : ''),
        ]);
    }

    public function removerFoto(): void
    {
        $data = (string) ($_POST['data'] ?? '');
        $campo = (string) ($_POST['remover_foto'] ?? '');
        $dataSegura = self::dataValida($data) ? $data : date('Y-m-d');
        $voltar = '/jornada/editar?data=' . urlencode($dataSegura);

        if (!self::dataValida($data) || !array_key_exists($campo, self::CAMPOS_BATIDA)) {
            $this->redirect($voltar, ['erro' => 'Não foi possível identificar o comprovante a remover.']);
        }

        $pdo = Database::connection();
        $usuarioId = (int) Auth::id();
        $colFoto = 'foto_' . $campo;
        $relativo = $this->fotoRelativa($data, $campo);

        if ($relativo === null) {
            $this->redirect($voltar, ['erro' => 'Este comprovante já não está anexado ao registro.']);
        }

        $this->removerArquivoFoto($relativo);

        $stmtAtualiza = $pdo->prepare(
            "UPDATE registros_ponto SET {$colFoto} = NULL WHERE usuario_id = :usuario_id AND data = :data"
        );
        $stmtAtualiza->execute([
            'usuario_id' => $usuarioId,
            'data' => $data,
        ]);

        $this->redirect($voltar, ['foto_removida' => self::CAMPOS_BATIDA[$campo]]);
    }

    public function comprovante(): void
    {
        $data = (string) ($_GET['data'] ?? '');
        $campo = (string) ($_GET['campo'] ?? '');

        if (!self::dataValida($data) || !array_key_exists($campo, self::CAMPOS_BATIDA)) {
            $this->naoEncontrado('Comprovante não encontrado.');
            return;
        }

        $relativo = $this->fotoRelativa($data, $campo);

        if ($relativo === null) {
            $this->naoEncontrado('Comprovante não encontrado.');
            return;
        }

        $baseDir = realpath(__DIR__ . '/../../storage/comprovantes');
        $caminhoAbsoluto = $baseDir ? realpath($baseDir . '/' . $relativo) : false;

        if (!$baseDir || !$caminhoAbsoluto || !str_starts_with($caminhoAbsoluto, $baseDir) || !is_file($caminhoAbsoluto)) {
            $this->naoEncontrado('Arquivo de comprovante não encontrado.');
            return;
        }

        $info = @getimagesize($caminhoAbsoluto);
        $mime = $info['mime'] ?? 'image/webp';
        $ext = pathinfo($caminhoAbsoluto, PATHINFO_EXTENSION) ?: 'webp';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string) filesize($caminhoAbsoluto));
        header('Cache-Control: private, no-cache');

        if (!empty($_GET['download'])) {
            $nomeDownload = "comprovante-{$data}-{$campo}.{$ext}";
            header("Content-Disposition: attachment; filename=\"{$nomeDownload}\"");
        }

        readfile($caminhoAbsoluto);
        exit;
    }

    public function excluir(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $mes = is_string($_POST['mes'] ?? null) ? $_POST['mes'] : date('Y-m');
        if (preg_match('/^\d{4}-\d{2}$/', $mes) !== 1) {
            $mes = date('Y-m');
        }

        if ($id > 0) {
            $pdo = Database::connection();
            $usuarioId = (int) Auth::id();
            $stmtBusca = $pdo->prepare(
                'SELECT data, foto_entrada, foto_saida_intervalo, foto_retorno_intervalo, foto_saida
                 FROM registros_ponto WHERE id = :id AND usuario_id = :usuario_id'
            );
            $stmtBusca->execute([
                'id' => $id,
                'usuario_id' => $usuarioId,
            ]);
            $reg = $stmtBusca->fetch();

            if ($reg) {
                foreach (array_keys(self::CAMPOS_BATIDA) as $campo) {
                    $this->removerArquivoFoto($reg['foto_' . $campo] ?? null);
                }
            }

            $stmt = $pdo->prepare(
                'DELETE FROM registros_ponto WHERE id = :id AND usuario_id = :usuario_id'
            );
            $stmt->execute([
                'id' => $id,
                'usuario_id' => $usuarioId,
            ]);

            if ($reg) {
                $this->redirect(
                    "/jornada?mes={$mes}",
                    ['excluido' => 'Registro de ' . date('d/m/Y', strtotime((string) $reg['data'])) . ' excluído, junto com os comprovantes anexados.']
                );
            }
        }

        $this->redirect("/jornada?mes={$mes}", ['salvo' => true]);
    }

    private function fotoRelativa(string $data, string $campo): ?string
    {
        $colFoto = 'foto_' . $campo;
        $stmt = Database::connection()->prepare(
            "SELECT {$colFoto} FROM registros_ponto WHERE usuario_id = :usuario_id AND data = :data"
        );
        $stmt->execute([
            'usuario_id' => (int) Auth::id(),
            'data' => $data,
        ]);
        $relativo = $stmt->fetchColumn();

        return $relativo ? (string) $relativo : null;
    }

    private function naoEncontrado(string $mensagem): void
    {
        http_response_code(404);
        echo $mensagem;
    }

    /**
     * @return array{0: ?string, 1: ?string} [caminhoRelativo, mensagemErro]
     */
    private function processarUploadFoto(array $arquivo, string $data, string $campo, ?string $fotoAnterior): array
    {
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [null, 'Falha no envio da imagem do comprovante (limite do servidor excedido ou upload incompleto).'];
        }

        if (($arquivo['size'] ?? 0) > 3 * 1024 * 1024) {
            return [null, 'A imagem do comprovante deve ter no máximo 3 MB.'];
        }

        $tmp = $arquivo['tmp_name'] ?? '';
        $info = $tmp !== '' ? @getimagesize($tmp) : false;
        if ($info === false) {
            $extNome = strtolower(pathinfo((string) ($arquivo['name'] ?? ''), PATHINFO_EXTENSION));
            if (in_array($extNome, ['heic', 'heif'], true)) {
                return [null, 'Fotos em HEIC do iPhone não são suportadas diretamente. Converta a imagem para JPG ou tire a foto novamente pelo navegador.'];
            }
            return [null, 'O arquivo enviado não é uma imagem válida.'];
        }

        $mapaExt = [
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
        ];

        $tipoImg = $info[2] ?? 0;
        if (!isset($mapaExt[$tipoImg])) {
            return [null, 'Formato de imagem não suportado. Use WebP, JPG ou PNG.'];
        }

        $ext = $mapaExt[$tipoImg];
        $subpasta = substr($data, 0, 7);
        $dirDestino = __DIR__ . '/../../storage/comprovantes/' . $subpasta;

        if (!is_dir($dirDestino) && !mkdir($dirDestino, 0775, true) && !is_dir($dirDestino)) {
            return [null, 'Não foi possível criar a pasta de armazenamento de comprovantes.'];
        }

        $nomeArquivo = (int) Auth::id() . "_{$data}_{$campo}.{$ext}";
        $relativo = "{$subpasta}/{$nomeArquivo}";
        $destinoAbsoluto = "{$dirDestino}/{$nomeArquivo}";

        if ($fotoAnterior !== null && $fotoAnterior !== $relativo) {
            $this->removerArquivoFoto($fotoAnterior);
        }

        if (!move_uploaded_file($tmp, $destinoAbsoluto)) {
            return [null, 'Não foi possível salvar a foto do comprovante.'];
        }

        return [$relativo, null];
    }

    private function removerArquivoFoto(?string $relativo): void
    {
        if (!$relativo) {
            return;
        }
        $caminho = __DIR__ . '/../../storage/comprovantes/' . $relativo;
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }

    public static function proximaBatida(array $registro): ?string
    {
        foreach (array_keys(self::CAMPOS_BATIDA) as $campo) {
            if (empty($registro[$campo])) {
                return $campo;
            }
        }

        return null;
    }

    public static function statusComprovacao(array $r): array
    {
        $batidas = 0;
        $fotos = 0;

        foreach (array_keys(self::CAMPOS_BATIDA) as $campo) {
            if (!empty($r[$campo])) {
                $batidas++;
                if (!empty($r['foto_' . $campo])) {
                    $fotos++;
                }
            }
        }

        if ($batidas === 0) {
            return ['batidas' => 0, 'fotos' => 0, 'status' => 'isento', 'rotulo' => '—'];
        }

        return [
            'batidas' => $batidas,
            'fotos' => $fotos,
            'status' => $fotos === $batidas ? 'comprovado' : ($fotos > 0 ? 'parcial' : 'pendente'),
            'rotulo' => $fotos > 0 ? "Fotos {$fotos}/{$batidas}" : 'Sem foto',
        ];
    }

    public static function enriquecerRegistro(array $r, int $cargaDiariaMinutos, int $toleranciaMinutos): array
    {
        $comp = self::statusComprovacao($r);
        $tipo = (string) ($r['tipo'] ?? 'normal');

        $r['minutos_trabalhados'] = self::calcularMinutos($r);
        $r['saldo_minutos'] = self::calcularSaldoDia($r, $cargaDiariaMinutos, $toleranciaMinutos);
        $r['classe_saldo'] = self::classeBadgeSaldo($r['saldo_minutos']);
        $r['classe_comprovacao'] = self::CLASSES_COMPROVACAO[$comp['status']];
        $r['classe_tipo'] = self::CLASSES_TIPO[$tipo] ?? self::CLASSES_TIPO['normal'];
        $r['rotulo_tipo'] = self::ROTULOS_TIPO[$tipo] ?? $tipo;
        $r['rotulo_comprovacao'] = $comp['rotulo'];
        $r['texto_comprovacao'] = $comp['batidas'] === 0
            ? 'Nenhuma batida registrada'
            : "{$comp['fotos']} de {$comp['batidas']} batidas com foto do comprovante";

        return $r;
    }

    public static function renderizarCelulaBatida(array $item, string $campo): string
    {
        $valor = self::horaCurta($item[$campo] ?? null);
        if ($valor === '') {
            return '—';
        }

        if (empty($item['foto_' . $campo])) {
            return htmlspecialchars($valor);
        }

        $dataIso = (string) ($item['data'] ?? '');
        $dataBr = date('d/m/Y', strtotime($dataIso));
        $rotulo = self::CAMPOS_BATIDA[$campo] ?? $campo;
        $url = '/comprovante?data=' . urlencode($dataIso) . '&campo=' . urlencode($campo);
        $titulo = "{$rotulo} — {$dataBr} às {$valor}";

        return sprintf(
            '<button type="button" class="group -m-2 inline-flex cursor-pointer items-center rounded-lg p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600" data-comprovante-url="%s" data-comprovante-titulo="%s" title="Ver comprovante de %s">'
            . '<span class="inline-flex items-center gap-1 rounded-md border border-emerald-200/80 bg-emerald-50/70 px-1.5 py-0.5 font-mono text-xs font-semibold tabular-nums text-emerald-900 transition-colors group-hover:border-emerald-300 group-hover:bg-emerald-100">%s <span class="text-[10px] font-bold text-emerald-600" aria-hidden="true">✓</span></span>'
            . '</button>',
            htmlspecialchars($url),
            htmlspecialchars($titulo),
            htmlspecialchars(strtolower($rotulo)),
            htmlspecialchars($valor)
        );
    }

    public static function icone(string $nome, string $classe = 'size-4', array $atributos = []): string
    {
        $icones = [
            'editar' => ['0 0 24 24', '<path d="M21 20.9998H13M2.5 21.4998L8.04927 19.3655C8.40421 19.229 8.58168 19.1607 8.74772 19.0716C8.8952 18.9924 9.0358 18.901 9.16804 18.7984C9.31692 18.6829 9.45137 18.5484 9.72028 18.2795L21 6.99982C22.1046 5.89525 22.1046 4.10438 21 2.99981C19.8955 1.89525 18.1046 1.89524 17 2.99981L5.72028 14.2795C5.45138 14.5484 5.31692 14.6829 5.20139 14.8318C5.09877 14.964 5.0074 15.1046 4.92823 15.2521C4.83911 15.4181 4.77085 15.5956 4.63433 15.9506L2.5 21.4998ZM2.5 21.4998L4.55812 16.1488C4.7054 15.7659 4.77903 15.5744 4.90534 15.4867C5.01572 15.4101 5.1523 15.3811 5.2843 15.4063C5.43533 15.4351 5.58038 15.5802 5.87048 15.8703L8.12957 18.1294C8.41967 18.4195 8.56472 18.5645 8.59356 18.7155C8.61877 18.8475 8.58979 18.9841 8.51314 19.0945C8.42545 19.2208 8.23399 19.2944 7.85107 19.4417L2.5 21.4998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
            'excluir' => ['0 0 24 24', '<path d="M16 6V5.2C16 4.0799 16 3.51984 15.782 3.09202C15.5903 2.71569 15.2843 2.40973 14.908 2.21799C14.4802 2 13.9201 2 12.8 2H11.2C10.0799 2 9.51984 2 9.09202 2.21799C8.71569 2.40973 8.40973 2.71569 8.21799 3.09202C8 3.51984 8 4.0799 8 5.2V6M10 11.5V16.5M14 11.5V16.5M3 6H21M19 6V17.2C19 18.8802 19 19.7202 18.673 20.362C18.3854 20.9265 17.9265 21.3854 17.362 21.673C16.7202 22 15.8802 22 14.2 22H9.8C8.11984 22 7.27976 22 6.63803 21.673C6.07354 21.3854 5.6146 20.9265 5.32698 20.362C5 19.7202 5 18.8802 5 17.2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'],
            'calendario' => ['0 0 20 20', '<path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />'],
            'chevron' => ['0 0 20 20', '<path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />'],
            'camera' => ['0 0 20 20', '<path fill-rule="evenodd" d="M1 8a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 8.07 3h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 16.07 6H17a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8Zm13.5 3a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM10 14a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />'],
            'ajustes' => ['0 0 20 20', '<path fill-rule="evenodd" d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />'],
        ];

        [$viewBox, $conteudo] = $icones[$nome] ?? ['0 0 20 20', ''];
        $preenchimento = $viewBox === '0 0 24 24' ? 'fill="none"' : 'fill="currentColor"';

        $extras = '';
        foreach ($atributos as $atributo => $valor) {
            $extras .= ' ' . $atributo . '="' . $valor . '"';
        }

        return '<svg class="' . $classe . '"' . $extras . ' viewBox="' . $viewBox . '" ' . $preenchimento . ' aria-hidden="true">' . $conteudo . '</svg>';
    }

    public static function horaCurta(?string $hora): string
    {
        return $hora ? substr($hora, 0, 5) : '';
    }

    /**
     * @param array<string, string> $mapa
     */
    public static function jsonMapaCalendario(array $mapa): string
    {
        return htmlspecialchars(
            json_encode($mapa, JSON_UNESCAPED_UNICODE) ?: '{}',
            ENT_QUOTES,
            'UTF-8'
        );
    }

    public static function dataValida(string $data): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $m)) {
            return false;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    public static function mesValido(string $mes): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})$/', $mes, $m)) {
            return false;
        }

        return checkdate((int) $m[2], 1, (int) $m[1]);
    }

    /**
     * Sábados e domingos são a única folga semanal considerada pelo sistema,
     * então a sexta-feira é sempre o último dia útil da semana.
     */
    public static function ehUltimoDiaUtilDaSemana(string $data): bool
    {
        return (int) date('N', strtotime($data)) === 5;
    }

    public static function deslocarMes(string $anoMes, int $deltaMeses): string
    {
        $ts = strtotime($anoMes . '-01 ' . ($deltaMeses >= 0 ? "+{$deltaMeses}" : (string) $deltaMeses) . ' month');
        return date('Y-m', $ts ?: time());
    }

    /**
     * @return array<string, string>
     */
    public static function obterMapaCalendario(?array $registros = null): array
    {
        if ($registros === null) {
            $stmt = Database::connection()->prepare(
                'SELECT data, tipo, entrada, saida FROM registros_ponto WHERE usuario_id = :usuario_id'
            );
            $stmt->execute(['usuario_id' => (int) Auth::id()]);
            $registros = $stmt->fetchAll();
        }

        $mapa = [];
        foreach ($registros as $reg) {
            $status = self::statusDoRegistro($reg);
            if ($status !== 'vazio') {
                $mapa[(string) $reg['data']] = $status;
            }
        }

        return $mapa;
    }

    public static function statusDoRegistro(array $r): string
    {
        $tipo = (string) ($r['tipo'] ?? 'normal');

        if ($tipo === 'falta') {
            return 'falta';
        }

        if (in_array($tipo, self::TIPOS_OCORRENCIA, true)) {
            return 'ocorrencia';
        }

        if (!empty($r['saida'])) {
            return 'concluido';
        }

        if (!empty($r['entrada'])) {
            return 'andamento';
        }

        return 'vazio';
    }

    public static function paraMinutos(?string $hora): ?int
    {
        if ($hora === null || $hora === '') {
            return null;
        }

        $curta = substr(trim($hora), 0, 5);
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $curta)) {
            return null;
        }

        [$h, $m] = explode(':', $curta);
        return ((int) $h * 60) + (int) $m;
    }

    public static function validarBatidas(array $d): ?string
    {
        foreach (self::CAMPOS_BATIDA as $campo => $rotulo) {
            if (!empty($d[$campo]) && self::paraMinutos((string) $d[$campo]) === null) {
                return "Horário inválido em {$rotulo}. Utilize o formato HH:MM (00:00 a 23:59).";
            }
        }

        $e1 = $d['entrada'] ?? null;
        $s1 = $d['saida_intervalo'] ?? null;
        $e2 = $d['retorno_intervalo'] ?? null;
        $s2 = $d['saida'] ?? null;

        if (($s1 !== null || $e2 !== null || $s2 !== null) && $e1 === null) {
            return 'Registre o horário de Entrada antes de lançar as demais batidas do dia.';
        }

        if ($e2 !== null && $s1 === null) {
            return 'Registre a Saída para intervalo antes de lançar o Retorno do intervalo.';
        }

        if ($s2 !== null && $s1 !== null && $e2 === null) {
            return 'Registre o Retorno do intervalo antes de lançar a Saída final.';
        }

        $sequencia = [];
        foreach (self::CAMPOS_BATIDA as $campo => $rotulo) {
            $min = self::paraMinutos($d[$campo] ?? null);
            if ($min !== null) {
                $sequencia[] = [
                    'rotulo' => $rotulo,
                    'hora' => self::horaCurta($d[$campo]),
                    'min' => $min,
                ];
            }
        }

        for ($i = 1, $n = count($sequencia); $i < $n; $i++) {
            $anterior = $sequencia[$i - 1];
            $atual = $sequencia[$i];
            if ($atual['min'] <= $anterior['min']) {
                return sprintf(
                    'Horário inválido: %s (%s) deve ser posterior a %s (%s).',
                    $atual['rotulo'],
                    $atual['hora'],
                    $anterior['rotulo'],
                    $anterior['hora']
                );
            }
        }

        return null;
    }

    public static function calcularAssistenteDia(array $r, int $cargaDiariaMinutos, int $saldoAnteriorMes): array
    {
        $vazio = ['saida_prevista' => null, 'nota_intervalo' => null, 'compensacao' => null];

        if (($r['tipo'] ?? 'normal') !== 'normal') {
            return $vazio;
        }

        $dataRef = (string) ($r['data'] ?? date('Y-m-d'));
        $diaSemana = (int) date('w', strtotime($dataRef));
        if ($diaSemana === 0 || $diaSemana === 6) {
            return $vazio;
        }

        $e1 = self::paraMinutos($r['entrada'] ?? null);
        $s1 = self::paraMinutos($r['saida_intervalo'] ?? null);
        $e2 = self::paraMinutos($r['retorno_intervalo'] ?? null);
        $s2 = self::paraMinutos($r['saida'] ?? null);

        $saidaPrevista = null;
        $saidaPrevistaMin = null;
        $notaIntervalo = null;

        if ($e1 !== null && $s2 === null) {
            if ($s1 !== null && $e2 !== null) {
                $intervalo = $e2 - $s1;
                $saidaPrevistaMin = $e1 + $cargaDiariaMinutos + $intervalo;
                $notaIntervalo = sprintf('considerando %d min de intervalo realizado', $intervalo);
            } elseif ($s1 !== null && $e2 === null) {
                $saidaPrevistaMin = $e1 + $cargaDiariaMinutos + 60;
                $notaIntervalo = 'em intervalo — estimando 1h de pausa';
            } else {
                $saidaPrevistaMin = $e1 + $cargaDiariaMinutos + 60;
                $notaIntervalo = 'estimando 1h de intervalo (ou ' . self::formatarHora($e1 + $cargaDiariaMinutos) . ' direto)';
            }
            $saidaPrevista = self::formatarHora($saidaPrevistaMin);
        }

        $compensacao = null;
        $mesCorrente = substr($dataRef, 0, 7) === date('Y-m');
        if ($saldoAnteriorMes < 0 && $mesCorrente && $s2 === null) {
            $debito = abs($saldoAnteriorMes);
            $diasUteisRestantes = self::contarDiasUteisRestantes($dataRef);

            if ($debito <= 20 || $diasUteisRestantes <= 1) {
                $minutosHoje = min($debito, 120);
            } else {
                // Espalha o débito igualmente pelos dias úteis que ainda restam
                // no mês (sem piso artificial, para não encurtar o prazo).
                $minutosHoje = min(120, (int) ceil($debito / $diasUteisRestantes));
            }
            $diasNecessarios = (int) ceil($debito / $minutosHoje);

            $compensacao = [
                'debito_total' => self::formatarMinutos($debito),
                'minutos_por_dia' => $minutosHoje,
                'dias_necessarios' => $diasNecessarios,
                'dias_uteis_restantes' => $diasUteisRestantes,
                'saida_com_compensacao' => $saidaPrevistaMin !== null ? self::formatarHora($saidaPrevistaMin + $minutosHoje) : null,
            ];
        }

        return [
            'saida_prevista' => $saidaPrevista,
            'nota_intervalo' => $notaIntervalo,
            'compensacao' => $compensacao,
        ];
    }

    /**
     * Dias úteis que ainda restam no mês depois da data de referência. A
     * própria data não entra na conta: a compensação começa no dia seguinte.
     */
    private static function contarDiasUteisRestantes(string $dataIso): int
    {
        $ts = strtotime($dataIso);
        $ultimoDia = (int) date('t', $ts);
        $anoMes = date('Y-m-', $ts);

        $uteis = 0;
        for ($d = (int) date('j', $ts) + 1; $d <= $ultimoDia; $d++) {
            $sem = (int) date('w', strtotime($anoMes . sprintf('%02d', $d)));
            if ($sem >= 1 && $sem <= 5) {
                $uteis++;
            }
        }

        return $uteis;
    }

    public static function calcularMinutos(array $r): int
    {
        $e1 = self::paraMinutos($r['entrada'] ?? null);
        $s1 = self::paraMinutos($r['saida_intervalo'] ?? null);
        $e2 = self::paraMinutos($r['retorno_intervalo'] ?? null);
        $s2 = self::paraMinutos($r['saida'] ?? null);

        if ($e1 !== null && $s1 !== null && $e2 !== null && $s2 !== null) {
            return max(0, ($s1 - $e1) + ($s2 - $e2));
        }

        if ($e1 !== null && $s2 !== null && $s1 === null && $e2 === null) {
            return max(0, $s2 - $e1);
        }

        if ($e1 !== null && $s1 !== null && $s2 === null) {
            return max(0, $s1 - $e1);
        }

        return 0;
    }

    public static function calcularSaldoDia(array $r, int $cargaDiariaMinutos, int $toleranciaMinutos = 0): ?int
    {
        $tipo = $r['tipo'] ?? 'normal';
        $trabalhado = self::calcularMinutos($r);

        if (in_array($tipo, self::TIPOS_OCORRENCIA, true)) {
            return $trabalhado;
        }

        $diaSemana = isset($r['data']) ? (int) date('w', strtotime((string) $r['data'])) : 1;
        $fimDeSemana = ($diaSemana === 0 || $diaSemana === 6);
        $metaDia = $fimDeSemana ? 0 : $cargaDiariaMinutos;

        if ($tipo === 'falta') {
            return -$metaDia;
        }

        if (empty($r['saida'])) {
            return null;
        }

        $diferenca = $trabalhado - $metaDia;

        if (!$fimDeSemana && $toleranciaMinutos > 0 && abs($diferenca) <= $toleranciaMinutos) {
            return 0;
        }

        return $diferenca;
    }

    public static function formatarHora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60) % 24, $minutos % 60);
    }

    public static function formatarMinutos(int $minutos, bool $exibirSinal = false): string
    {
        $sinal = $minutos < 0 ? '-' : ($exibirSinal && $minutos > 0 ? '+' : '');
        $abs = abs($minutos);
        return sprintf('%s%02dh %02dmin', $sinal, intdiv($abs, 60), $abs % 60);
    }

    public static function formatarMesAno(string $anoMes): string
    {
        $meses = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro',
        ];
        [$ano, $mes] = array_pad(explode('-', $anoMes), 2, '');
        return ($meses[$mes] ?? $mes) . ' de ' . $ano;
    }

    public static function classeBadgeSaldo(?int $saldo): string
    {
        if ($saldo === null) {
            return 'bg-stone-100 text-zinc-600 ring-1 ring-inset ring-stone-300/80';
        }
        if ($saldo > 0) {
            return 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20';
        }
        if ($saldo < 0) {
            return 'bg-red-50 text-red-800 ring-1 ring-inset ring-red-600/20';
        }
        return self::CLASSES_TIPO['normal'];
    }
}
