<?php
// controllers/LiberacaoAcessoController.php

require_once __DIR__ . '/../config/db.php';

class LiberacaoAcessoController
{
    /**
     * NOVO: Exibe a página principal do módulo de Liberação de Acesso.
     */
    public function index()
    {
        // 1. Camada de Segurança (reaproveitando a lógica do arquivo antigo)
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $moduloIdAtual = 47; // ID do módulo "Liberação de Acesso"
        if (!PermissionManager::can('visualizar', $moduloIdAtual)) {
            // Podemos criar uma view de acesso negado no futuro
            die('Acesso negado.');
        }

        // 2. Preparação dos Dados para a View
        $pageTitle = "Liberação de Acesso";
        $pageScripts = ['dist/js/liberacao-acesso-search.js'];

        // 3. Renderização da View
        // Captura o conteúdo da view em uma variável
        ob_start();
        // O conteúdo que estava em 'public/liberacao_acesso.php' agora estará neste novo local
        require_once __DIR__ . '/../views/liberacao_acesso/index.php';
        $content = ob_get_clean();

        // 4. Carrega o Layout Principal com o conteúdo da nossa página
        require_once __DIR__ . '/../views/layout.php';
    }

    /**
     * Busca todas as sessões de usuários que estão atualmente ativas no sistema.
     * Permite filtrar por nome de usuário.
     */
    public static function getActiveSessions(string $search = ''): array
    {
        $pdo = getDbConnection();
        $sql = "SELECT s.id, u.username, s.ip, s.data_login
                FROM sessoes_ativas s
                JOIN usuarios u ON s.id_usuario = u.id";

        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE u.username LIKE ?";
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY u.username ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * NOVO: Processa a requisição da ROTA para liberar (encerrar) uma sessão.
     * @param int $sessionId O ID da sessão que vem da URL.
     */
    public function release($sessionId)
    {
        // Chama a lógica de exclusão que já existe na classe (o método estático)
        $success = self::releaseSession((int)$sessionId);

        // Redireciona de volta para a página principal com uma mensagem de status
        $status = $success ? 'released' : 'error';
        header('Location: ' . BASE_PATH . '/liberacao_acesso?status=' . $status);
        exit;
    }

    /**
     * NOVO: Processa a requisição para liberar (encerrar) uma sessão.
     */
    public static function releaseSession(int $sessionId): bool
    {
        // Impede que um administrador encerre a própria sessão por este método
        if (isset($_SESSION['session_db_id']) && $_SESSION['session_db_id'] == $sessionId) {
            return false;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM sessoes_ativas WHERE id = ?");
            return $stmt->execute([$sessionId]);
        } catch (PDOException $e) {
            error_log("Erro ao tentar liberar sessão (ID: {$sessionId}): " . $e->getMessage());
            return false;
        }
    }
}
