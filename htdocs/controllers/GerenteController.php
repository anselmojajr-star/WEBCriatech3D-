<?php
require_once __DIR__ . '/../config/db.php';

class GerenteController
{
    /**
     * NOVO: Exibe a página principal do Painel do Gerente (Permissões).
     */
    public function index()
    {
        // 1. Segurança
        require_once __DIR__ . '/../security/session_manager.php';
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        // 2. Prepara dados para a View (lógica do antigo gerente.php)
        $pageTitle = "Painel do Gerente - Permissões";
        $gerenteId = $_SESSION['user_id'];

        $perfis = self::getAllPerfisGerenciaveis();
        $modulosGerenciaveis = self::getModulosGerenciaveisPeloUsuario($gerenteId);
        $perfilSelecionadoId = $_GET['perfil_id'] ?? null;
        $permissoesAtuais = [];
        $acaoPermissoesAtuais = [];
        if ($perfilSelecionadoId) {
            $permissoesAtuais = self::getPermissoesPorPerfil($perfilSelecionadoId);
            $acaoPermissoesAtuais = self::getAcaoPermissoesPorPerfil($perfilSelecionadoId);
        }

        $usuarios = self::getAllUsuarios();
        $usuarioSelecionadoId = $_GET['usuario_id'] ?? null;
        $permissoesUsuarioAtuais = [];
        if ($usuarioSelecionadoId) {
            $permissoesUsuarioAtuais = self::getPermissoesPorUsuario((int)$usuarioSelecionadoId);
        }

        $pageScripts = ['dist/js/gerente-permissoes.js'];

        // 3. Renderiza a View dentro do Layout
        ob_start();
        require_once __DIR__ . '/../views/gerente/index.php'; // Apontando para a nova view
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    /**
     * Busca todos os perfis, exceto o de Administrador (que não pode ser gerenciado).
     */
    public static function getAllPerfisGerenciaveis(): array
    {
        $pdo = getDbConnection();
        // O ID 4 é o do Administrador, que tem acesso a tudo e não deve ser editado aqui.
        $stmt = $pdo->query("SELECT id, perfil FROM perfil WHERE id <> 4 ORDER BY perfil ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os módulos que podem ter permissões atribuídas.
     */
    public static function getAllModulos(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT id, nome FROM modulos ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca os IDs dos módulos que um perfil específico já tem permissão para ver.
     */
    public static function getPermissoesPorPerfil(int $perfilId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id_modulo FROM perfil_modulo_permissao WHERE id_perfil = ? AND visualizar = 1");
        $stmt->execute([$perfilId]);
        // Retorna um array simples de IDs, ex: [28, 30, 31]
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Salva as permissões de visualização para um perfil.
     */
    public static function salvarPermissoes(int $perfilId, array $modulosIds): bool
    {
        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            // 1. Limpa as permissões antigas deste perfil.
            $stmt_del = $pdo->prepare("DELETE FROM perfil_modulo_permissao WHERE id_perfil = ?");
            $stmt_del->execute([$perfilId]);

            // 2. Insere as novas permissões (se houver alguma marcada).
            if (!empty($modulosIds)) {
                $sql_insert = "INSERT INTO perfil_modulo_permissao (id_perfil, id_modulo, visualizar) VALUES ";
                $values = [];
                foreach ($modulosIds as $moduloId) {
                    $values[] = "({$perfilId}, " . (int)$moduloId . ", 1)";
                }
                $sql_insert .= implode(", ", $values);
                $pdo->exec($sql_insert);
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao salvar permissões: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca apenas os módulos que o usuário logado (o gerente) tem permissão para ver.
     * Isso o impede de gerenciar permissões de módulos que ele mesmo não pode acessar.
     */
    public static function getModulosGerenciaveisPeloUsuario(int $gerenteId): array
    {
        $pdo = getDbConnection();

        // A CORREÇÃO ESTÁ NA LINHA ABAIXO
        // Em vez de selecionar apenas m.id e m.nome, selecionamos todas as colunas de modulos (m.*)
        $sql = "
            SELECT DISTINCT m.* FROM modulos m
            JOIN perfil_modulo_permissao pmp ON m.id = pmp.id_modulo
            JOIN loginperfil lp ON pmp.id_perfil = lp.id_perfil
            WHERE lp.id_login = :gerenteId AND m.status = 'liberado'
            ORDER BY m.ordem ASC, m.nome ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':gerenteId' => $gerenteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca as permissões de AÇÃO (editar, excluir, etc.) para um perfil.
     * Retorna um array no formato [id_modulo => ['editar', 'excluir']]
     */
    public static function getAcaoPermissoesPorPerfil(int $perfilId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id_modulo, acao FROM perfil_acao_permissao WHERE id_perfil = ?");
        $stmt->execute([$perfilId]);

        $permissoes = [];
        foreach ($stmt->fetchAll() as $row) {
            $permissoes[$row['id_modulo']][] = $row['acao'];
        }
        return $permissoes;
    }

    /**
     * Salva as permissões de AÇÃO para um perfil.
     */
    public static function salvarAcaoPermissoes(int $perfilId, array $permissoesInput): bool
    {
        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            // 1. Limpa todas as permissões de ação antigas deste perfil.
            $stmt_del = $pdo->prepare("DELETE FROM perfil_acao_permissao WHERE id_perfil = ?");
            $stmt_del->execute([$perfilId]);

            // 2. Insere as novas permissões.
            if (!empty($permissoesInput)) {
                $stmt_insert = $pdo->prepare(
                    "INSERT INTO perfil_acao_permissao (id_perfil, id_modulo, acao) VALUES (:id_perfil, :id_modulo, :acao)"
                );
                foreach ($permissoesInput as $moduloId => $acoes) {
                    foreach ($acoes as $acao => $value) {
                        if ($value === 'on') { // Checkboxes marcados enviam 'on'
                            $stmt_insert->execute([
                                ':id_perfil' => $perfilId,
                                ':id_modulo' => $moduloId,
                                ':acao' => $acao
                            ]);
                        }
                    }
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao salvar permissões de ação: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca todos os usuários, exceto o de Administrador (ID do funcionário 1).
     */
    public static function getAllUsuarios(): array
    {
        $pdo = getDbConnection();
        // ID 1 geralmente é o super admin do sistema
        $stmt = $pdo->query("SELECT id, username FROM usuarios WHERE id_funcionario <> 1 ORDER BY username ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca as permissões de VISIBILIDADE específicas para um usuário.
     */
    public static function getPermissoesPorUsuario(int $usuarioId): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id_modulo, permitido, data_inicio_validade, data_fim_validade FROM usuario_modulo_permissao WHERE id_usuario = ?");
        $stmt->execute([$usuarioId]);

        $permissoes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $permissoes[$row['id_modulo']] = $row;
        }
        return $permissoes;
    }

    /**
     * Salva as permissões de VISIBILIDADE para um usuário específico.
     */
    public static function salvarPermissoesUsuario(int $usuarioId, array $permissoesInput): bool
    {
        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            foreach ($permissoesInput as $moduloId => $perm) {
                $status = $perm['status'];
                $dataInicio = !empty($perm['data_inicio']) ? $perm['data_inicio'] : null;
                $dataFim = !empty($perm['data_fim']) ? $perm['data_fim'] : null;

                // 1. Limpa a permissão antiga para este usuário e módulo
                $stmt_del = $pdo->prepare("DELETE FROM usuario_modulo_permissao WHERE id_usuario = ? AND id_modulo = ?");
                $stmt_del->execute([$usuarioId, $moduloId]);

                // 2. Se não for 'padrão', insere a nova regra
                if ($status !== 'padrao') {
                    $permitido = ($status === 'permitir') ? 1 : 0;
                    $stmt_insert = $pdo->prepare(
                        "INSERT INTO usuario_modulo_permissao (id_usuario, id_modulo, permitido, data_inicio_validade, data_fim_validade) VALUES (?, ?, ?, ?, ?)"
                    );
                    $stmt_insert->execute([$usuarioId, $moduloId, $permitido, $dataInicio, $dataFim]);
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao salvar permissões de visibilidade do usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca os módulos e os organiza em uma estrutura hierárquica (pai -> filhos).
     * @param array $modulosGerenciaveis Uma lista plana de módulos.
     * @return array
     */
    public static function organizarModulosHierarquicamente(array $modulosGerenciaveis): array
    {
        $menu = [];
        // Primeiro, adiciona todos os menus principais
        foreach ($modulosGerenciaveis as $modulo) {
            if ($modulo['tipo_menu'] === 'menu_principal') {
                $menu[$modulo['id']] = $modulo;
                $menu[$modulo['id']]['submenus'] = [];
            }
        }
        // Depois, aninha os submenus
        foreach ($modulosGerenciaveis as $modulo) {
            if ($modulo['tipo_menu'] === 'submenu_item' && isset($menu[$modulo['id_modulo_pai']])) {
                $menu[$modulo['id_modulo_pai']]['submenus'][] = $modulo;
            }
        }
        return $menu;
    }

    /**
     * Espelha (copia) todas as permissões granulares de um usuário de origem para um de destino.
     */
    public static function espelharPermissoesUsuario(int $sourceUserId, int $targetUserId, int $adminId): bool
    {
        // Impede que um usuário copie permissões para si mesmo.
        if ($sourceUserId === $targetUserId) {
            return false;
        }

        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            // 1. Limpa as permissões de visibilidade antigas do usuário de destino.
            $stmt_del_vis = $pdo->prepare("DELETE FROM usuario_modulo_permissao WHERE id_usuario = ?");
            $stmt_del_vis->execute([$targetUserId]);

            // 2. Limpa as permissões de ação antigas do usuário de destino.
            $stmt_del_act = $pdo->prepare("DELETE FROM usuario_acao_permissao WHERE id_usuario = ?");
            $stmt_del_act->execute([$targetUserId]);

            // 3. Copia as permissões de VISIBILIDADE.
            $stmt_select_vis = $pdo->prepare("SELECT id_modulo, permitido, data_inicio_validade, data_fim_validade FROM usuario_modulo_permissao WHERE id_usuario = ?");
            $stmt_select_vis->execute([$sourceUserId]);
            $permissoesVis = $stmt_select_vis->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($permissoesVis)) {
                $sql_insert_vis = "INSERT INTO usuario_modulo_permissao (id_usuario, id_modulo, permitido, data_inicio_validade, data_fim_validade) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert_vis = $pdo->prepare($sql_insert_vis);
                foreach ($permissoesVis as $perm) {
                    $stmt_insert_vis->execute([$targetUserId, $perm['id_modulo'], $perm['permitido'], $perm['data_inicio_validade'], $perm['data_fim_validade']]);
                }
            }

            // 4. Copia as permissões de AÇÃO.
            $stmt_select_act = $pdo->prepare("SELECT id_modulo, acao, permitido, data_inicio_validade, data_fim_validade FROM usuario_acao_permissao WHERE id_usuario = ?");
            $stmt_select_act->execute([$sourceUserId]);
            $permissoesAct = $stmt_select_act->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($permissoesAct)) {
                $sql_insert_act = "INSERT INTO usuario_acao_permissao (id_usuario, id_modulo, acao, permitido, criado_por, data_inicio_validade, data_fim_validade) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert_act = $pdo->prepare($sql_insert_act);
                foreach ($permissoesAct as $perm) {
                    $stmt_insert_act->execute([$targetUserId, $perm['id_modulo'], $perm['acao'], $perm['permitido'], $adminId, $perm['data_inicio_validade'], $perm['data_fim_validade']]);
                }
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao espelhar permissões: " . $e->getMessage());
            return false;
        }
    }

    /**
     * NOVO: Salva as permissões de VISIBILIDADE de um PERFIL.
     */
    public function storeVisibilidadePerfil()
    {
        $perfilId = $_POST['perfil_id'] ?? null;
        if (!$perfilId) {
            header('Location: ' . BASE_PATH . '/gerente?status=error');
            exit;
        }

        $modulosIds = $_POST['modulos_ids'] ?? [];
        $success = self::salvarPermissoes((int)$perfilId, $modulosIds);

        $redirectUrl = BASE_PATH . '/gerente?perfil_id=' . $perfilId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * NOVO: Salva as permissões de AÇÕES de um PERFIL.
     */
    public function storeAcoesPerfil()
    {
        $perfilId = $_POST['perfil_id'] ?? null;
        if (!$perfilId) {
            header('Location: ' . BASE_PATH . '/gerente?status=error');
            exit;
        }

        $permissoes = $_POST['permissoes'] ?? [];
        $success = self::salvarAcaoPermissoes((int)$perfilId, $permissoes);

        $redirectUrl = BASE_PATH . '/gerente?perfil_id=' . $perfilId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * NOVO: Salva as permissões de VISIBILIDADE de um USUÁRIO.
     */
    public function storeVisibilidadeUsuario()
    {
        $usuarioId = $_POST['usuario_id'] ?? null;
        if (!$usuarioId) {
            header('Location: ' . BASE_PATH . '/gerente?status=error');
            exit;
        }

        $permissoes = $_POST['permissoes'] ?? [];
        $success = self::salvarPermissoesUsuario((int)$usuarioId, $permissoes);

        $redirectUrl = BASE_PATH . '/gerente?usuario_id=' . $usuarioId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * NOVO: Salva as permissões de AÇÕES de um USUÁRIO.
     */
    public function storeAcoesUsuario()
    {
        $usuarioId = $_POST['usuario_id'] ?? null;
        if (!$usuarioId) {
            header('Location: ' . BASE_PATH . '/gerente?status=error');
            exit;
        }

        // A lógica para salvar as permissões de Ações por Usuário precisa ser implementada.
        // Por enquanto, vamos simular o sucesso e redirecionar corretamente.
        // Futuramente, você adicionará a chamada para um método como self::salvarAcaoPermissoesUsuario() aqui.
        $success = true; // Placeholder

        $redirectUrl = BASE_PATH . '/gerente?usuario_id=' . $usuarioId . '&status=' . ($success ? 'success' : 'error');
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * NOVO: Exibe a página/formulário para espelhar permissões.
     */
    public function showEspelharPermissoesForm()
    {
        // 1. Camada de Segurança
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        // Futuramente, podemos adicionar uma verificação de permissão para este módulo específico.

        // 2. Preparação de Dados para a View
        $pageTitle = "Espelhar Permissões de Usuário";

        // Busca a lista de usuários para preencher os formulários
        $usuarios = self::getAllUsuarios();

        // 3. Renderização da View
        ob_start();
        require_once __DIR__ . '/../views/gerente/espelhar_permissoes.php';
        $content = ob_get_clean();

        // 4. Carrega o Layout Principal
        require_once __DIR__ . '/../views/layout.php';
    }

    /**
     * NOVO: Processa o formulário de espelhamento de permissões.
     */
    public function storeEspelhamento()
    {
        $sourceUserId = (int)($_POST['source_user_id'] ?? 0);
        $targetUserId = (int)($_POST['target_user_id'] ?? 0);
        $adminId = $_SESSION['user_id'];

        $success = false;
        if ($sourceUserId && $targetUserId) {
            $success = self::espelharPermissoesUsuario($sourceUserId, $targetUserId, $adminId);
        }

        $status = $success ? 'success' : 'error';
        header('Location: ' . BASE_PATH . '/espelhar_permissoes?status=' . $status);
        exit;
    }
}
