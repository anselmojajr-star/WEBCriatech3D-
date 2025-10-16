<?php
// controllers/ControleAcessoController.php

require_once __DIR__ . '/../config/db.php';

class ControleAcessoController
{
    /**
     * NOVO: Exibe a página principal do Controle de Acesso por Horário.
     */
    public function index()
    {
        // 1. Segurança
        require_once __DIR__ . '/../security/session_manager.php';
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        // 2. Prepara dados para a View (lógica do antigo controle_acesso.php)
        $pageTitle = "Controle de Acesso por Horário";

        $perfis = GerenteController::getAllPerfisGerenciaveis();
        $perfilSelecionadoId = $_GET['perfil_id'] ?? null;
        $regrasPerfil = [];
        if ($perfilSelecionadoId) {
            $regrasPerfil = self::getRegrasPorPerfil($perfilSelecionadoId);
        }

        $usuarios = GerenteController::getAllUsuarios();
        $usuarioSelecionadoId = $_GET['usuario_id'] ?? null;
        $regrasUsuario = [];
        if ($usuarioSelecionadoId) {
            $regrasUsuario = self::getRegrasPorUsuario($usuarioSelecionadoId);
        }

        $diasDaSemana = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];

        // 3. Renderiza a View dentro do Layout
        ob_start();
        require_once __DIR__ . '/../views/controle_acesso/index.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    /**
     * Busca as regras de horário de acesso para um perfil específico.
     */
    public static function getRegrasPorPerfil(int $perfilId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM acesso_perfil_horario WHERE id_perfil = ?");
        $stmt->execute([$perfilId]);

        $regras = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $regra) {
            $regras[$regra['dia_semana']] = $regra;
        }
        return $regras;
    }

    /**
     * Salva as regras de horário de acesso para um perfil.
     */
    public static function salvarRegrasPerfil(int $perfilId, array $regrasInput): bool
    {
        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            $stmt_del = $pdo->prepare("DELETE FROM acesso_perfil_horario WHERE id_perfil = ?");
            $stmt_del->execute([$perfilId]);

            $sql_insert = "INSERT INTO acesso_perfil_horario 
                (id_perfil, dia_semana, acesso_liberado, hora_inicio_1, hora_fim_1, hora_inicio_2, hora_fim_2) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);

            foreach ($regrasInput as $dia => $regrasDia) {
                $acessoLiberado = isset($regrasDia['acesso_liberado']) ? 1 : 0;
                $horaInicio1 = !empty($regrasDia['hora_inicio_1']) ? $regrasDia['hora_inicio_1'] : null;
                $horaFim1 = !empty($regrasDia['hora_fim_1']) ? $regrasDia['hora_fim_1'] : null;
                $horaInicio2 = !empty($regrasDia['hora_inicio_2']) ? $regrasDia['hora_inicio_2'] : null;
                $horaFim2 = !empty($regrasDia['hora_fim_2']) ? $regrasDia['hora_fim_2'] : null;

                $stmt_insert->execute([$perfilId, $dia, $acessoLiberado, $horaInicio1, $horaFim1, $horaInicio2, $horaFim2]);
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao salvar regras de acesso por perfil: " . $e->getMessage());
            return false;
        }
    }

    // --- NOVOS MÉTODOS PARA USUÁRIO ---

    /**
     * Busca as regras de horário de acesso para um usuário específico.
     */
    public static function getRegrasPorUsuario(int $usuarioId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM acesso_usuario_horario WHERE id_usuario = ?");
        $stmt->execute([$usuarioId]);

        $regras = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $regra) {
            $regras[$regra['dia_semana']] = $regra;
        }
        return $regras;
    }

    /**
     * Salva as regras de horário de acesso para um usuário.
     */
    public static function salvarRegrasUsuario(int $usuarioId, array $regrasInput): bool
    {
        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            $stmt_del = $pdo->prepare("DELETE FROM acesso_usuario_horario WHERE id_usuario = ?");
            $stmt_del->execute([$usuarioId]);

            $sql_insert = "INSERT INTO acesso_usuario_horario 
                (id_usuario, dia_semana, acesso_liberado, hora_inicio_1, hora_fim_1, hora_inicio_2, hora_fim_2) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insert);

            foreach ($regrasInput as $dia => $regrasDia) {
                $acessoLiberado = isset($regrasDia['acesso_liberado']) ? 1 : 0;
                $horaInicio1 = !empty($regrasDia['hora_inicio_1']) ? $regrasDia['hora_inicio_1'] : null;
                $horaFim1 = !empty($regrasDia['hora_fim_1']) ? $regrasDia['hora_fim_1'] : null;
                $horaInicio2 = !empty($regrasDia['hora_inicio_2']) ? $regrasDia['hora_inicio_2'] : null;
                $horaFim2 = !empty($regrasDia['hora_fim_2']) ? $regrasDia['hora_fim_2'] : null;

                $stmt_insert->execute([$usuarioId, $dia, $acessoLiberado, $horaInicio1, $horaFim1, $horaInicio2, $horaFim2]);
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao salvar regras de acesso por usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * NOVO: Processa o salvamento das regras de um PERFIL vindo da rota.
     */
    public function storeRegrasPerfil()
    {
        // 1. Pega os dados enviados pelo formulário
        $perfilId = (int)($_POST['perfil_id'] ?? 0);
        $regras = $_POST['regras'] ?? [];

        // 2. Valida se recebeu os dados necessários
        if (!$perfilId) {
            header('Location: ' . BASE_PATH . '/controle-acesso?status=error_perfil_id');
            exit;
        }

        // 3. Chama a sua lógica de salvamento original (que é estática)
        $success = self::salvarRegrasPerfil($perfilId, $regras);

        // 4. Redireciona o usuário de volta para a página
        $redirectUrl = BASE_PATH . '/controle-acesso?perfil_id=' . $perfilId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * NOVO: Processa o salvamento das regras de um USUÁRIO vindo da rota.
     */
    public function storeRegrasUsuario()
    {
        // 1. Pega os dados enviados pelo formulário
        $usuarioId = (int)($_POST['usuario_id'] ?? 0);
        $regras = $_POST['regras'] ?? [];

        // 2. Valida se recebeu os dados necessários
        if (!$usuarioId) {
            header('Location: ' . BASE_PATH . '/controle-acesso?status=error_usuario_id');
            exit;
        }

        // 3. Chama a sua lógica de salvamento original (que é estática)
        $success = self::salvarRegrasUsuario($usuarioId, $regras);

        // 4. Redireciona o usuário de volta para a página
        $redirectUrl = BASE_PATH . '/controle-acesso?usuario_id=' . $usuarioId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }
}
