<?php
// controllers/DashboardController.php (Versão Final e Correta)

class DashboardController
{
    public function index()
    {
        // 1. Camada de Segurança: Valida a sessão do usuário
        require_once __DIR__ . '/../security/session_manager.php';
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        // 2. Preparação de Dados para a View
        $pageTitle = "Dashboard";
        $pageScripts = []; // O dashboard não precisa de scripts JS específicos

        // 3. Renderização da View
        // Captura o conteúdo da view específica do dashboard em uma variável
        ob_start();
        require_once __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();

        // 4. Carrega o Layout Principal, que usará as variáveis $pageTitle, $content, etc.
        require_once __DIR__ . '/../views/layout.php';
    }
}
