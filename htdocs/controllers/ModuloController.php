<?php
// HTDOCS/controllers/ModuloController.php

// As linhas de depuração (ini_set, error_reporting) foram removidas pois o erro foi encontrado.
// Você pode adicioná-las novamente no futuro se precisar depurar outros problemas.

require_once __DIR__ . '/../config/db.php';
//require_once __DIR__ . '/../security/AuthManager.php';
require_once __DIR__ . '/../vendor/autoload.php';

class ModuloController
{
    /**
     * ID do módulo "Material" no banco de dados.
     * Tabela: modulos
     */
    private const MODULO_ID = 24;

    // NOVO MÉTODO
    public function index()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Gerenciamento de Módulos";
        $pageScripts = ['dist/js/modulos-search.js'];
        $moduloIdAtual = self::MODULO_ID;

        ob_start();
        require_once __DIR__ . '/../views/modulos/index.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    // NOVO MÉTODO
    public function create()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Novo Módulo";
        $isEditing = false;
        $modulo = null;
        $moduleId = null; // <-- ADICIONE ESTA LINHA
        $allProfiles = self::getAllProfiles();
        $parents = self::listParentModules();
        $modulePermissions = [];

        ob_start();
        require_once __DIR__ . '/../views/modulos/form.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    // NOVO MÉTODO
    public function edit($id)
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Editar Módulo";
        $isEditing = true;
        $moduleId = (int)$id;
        $modulo = self::getModuleById($moduleId);
        $allProfiles = self::getAllProfiles();
        $parents = self::listParentModules();
        $modulePermissions = self::getModulePermissions($moduleId);

        ob_start();
        require_once __DIR__ . '/../views/modulos/form.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    // NOVO MÉTODO
    public function store()
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        $userId = $_SESSION['user_id'];
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $isEdit = !empty($id);
        $permissaoNecessaria = $isEdit ? 'editar' : 'criar';

        if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/modulos?status=no_permission');
            exit;
        }

        try {
            // Lógica de salvamento que já existe em handleRequest()
            $pdo = getDbConnection();
            $pdo->beginTransaction();

            $nome = $_POST['nome'] ?? '';
            $rota = $_POST['rota'] ?? '';
            $icone = $_POST['icone'] ?? null;
            $id_modulo_pai = empty($_POST['id_modulo_pai']) ? null : (int)$_POST['id_modulo_pai'];
            $ordem = $_POST['ordem'] ?? 0;
            $tipo_menu = $_POST['tipo_menu'] ?? 'funcionalidade';
            $status = $_POST['status'] ?? 'rascunho';
            $perfil_ids = $_POST['perfil_ids'] ?? [];

            $params = [
                'nome' => $nome,
                'rota' => $rota,
                'icone' => $icone,
                'id_modulo_pai' => $id_modulo_pai,
                'ordem' => $ordem,
                'tipo_menu' => $tipo_menu,
                'status' => $status,
                'ultima_alteracao_por' => $userId
            ];

            if ($id) {
                $sql = "UPDATE modulos SET nome = :nome, rota = :rota, icone = :icone, id_modulo_pai = :id_modulo_pai, ordem = :ordem, tipo_menu = :tipo_menu, status = :status, ultima_alteracao_por = :ultima_alteracao_por WHERE id = :id";
                $params['id'] = $id;
            } else {
                $sql = "INSERT INTO modulos (nome, rota, icone, id_modulo_pai, ordem, tipo_menu, status, ultima_alteracao_por) VALUES (:nome, :rota, :icone, :id_modulo_pai, :ordem, :tipo_menu, :status, :ultima_alteracao_por)";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            if (!$id) {
                $id = $pdo->lastInsertId();
            }

            self::saveModulePermissions($pdo, $id, $perfil_ids);

            $pdo->commit();
            header('Location: ' . BASE_PATH . '/modulos?status=success');
            exit;
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            error_log("Erro no ModuloController->store(): " . $e->getMessage());
            header('Location: ' . BASE_PATH . '/modulos?status=error&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // NOVO MÉTODO
    public function search()
    {
        if (!AuthManager::validateSession()) {
            http_response_code(403);
            exit('Acesso negado.');
        }

        $search = trim($_GET['search'] ?? '');
        $modulos = self::searchModules($search);

        // Adicionamos a verificação de permissão para editar e excluir
        $canEdit = PermissionManager::can('editar', self::MODULO_ID);
        $canDelete = PermissionManager::can('excluir', self::MODULO_ID);

        if (empty($modulos)) {
            echo '<tr><td colspan="6" class="text-center text-muted">Nenhum módulo encontrado.</td></tr>';
            exit;
        }

        foreach ($modulos as $mod) {
            $id = (int)($mod['id'] ?? 0);
            $nome = htmlspecialchars($mod['nome'] ?? '');
            $rota = htmlspecialchars($mod['rota'] ?? '');
            $status = htmlspecialchars($mod['status'] ?? '');
            $tipo = htmlspecialchars($mod['tipo_menu'] ?? '');

            // ATUALIZAÇÃO DA COLUNA DE AÇÕES
            echo "<tr>
                <td>{$id}</td>
                <td>{$nome}</td>
                <td>{$rota}</td>
                <td>{$status}</td>
                <td>{$tipo}</td>
                <td>
                    <div class='btn-group btn-group-sm'>";

            if ($canEdit) {
                echo "<a href='" . BASE_PATH . "/modulos/editar/{$id}' class='btn btn-primary'><i class='fas fa-edit'></i></a>";
            }
            if ($canDelete) {
                echo "<a href='" . BASE_PATH . "/modulos/excluir/{$id}' class='btn btn-danger' onclick=\"return confirm('Tem a certeza que deseja excluir este módulo?')\"><i class='fas fa-trash'></i></a>";
            }

            echo "  </div>
                </td>
              </tr>";
        }
    }

    public function delete($id)
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        // O ID do módulo "Módulos" é 24, para a verificação de permissão.
        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/modulos?status=no_permission');
            exit;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM modulos WHERE id = ?");
            $stmt->execute([(int)$id]);
            header('Location: ' . BASE_PATH . '/modulos?status=deleted');
        } catch (PDOException $e) {
            error_log("Erro ao excluir módulo (ID: $id): " . $e->getMessage());
            // Se o módulo não puder ser excluído por ter submódulos associados, por exemplo.
            header('Location: ' . BASE_PATH . '/modulos?status=error&msg=' . urlencode('Este módulo não pode ser excluído, pois pode ter outros registros associados a ele.'));
        }
        exit;
    }

    /**
     * Processa as requisições POST para salvar ou atualizar um módulo.
     */
    public static function handleRequest()
    {

        /* ATENÇÃO EU
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        */
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: ../public/login.php');
            exit;
        }

        // ---> INÍCIO DA ADIÇÃO DA GUARITA DE SEGURANÇA <---

        // 1. Determinar se é uma edição (tem um ID) ou uma criação (não tem ID)
        $isEdit = !empty($postData['id']);
        $permissaoNecessaria = $isEdit ? 'editar' : 'criar';

        // 2. Chamar o PermissionManager para verificar a permissão
        if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
            // 3. Se não tiver permissão, redireciona com uma mensagem de erro e termina a execução.
            header('Location: ../public/material.php?status=no_permission');
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
            try {
                $pdo = getDbConnection();
                $pdo->beginTransaction();

                $id = $_POST['id'] ?? null;
                $nome = $_POST['nome'] ?? '';
                $rota = $_POST['rota'] ?? '';
                $icone = $_POST['icone'] ?? null;
                $id_modulo_pai = empty($_POST['id_modulo_pai']) ? null : $_POST['id_modulo_pai'];
                $ordem = $_POST['ordem'] ?? 0;
                $tipo_menu = $_POST['tipo_menu'] ?? 'funcionalidade';
                $status = $_POST['status'] ?? 'rascunho';
                $perfil_ids = $_POST['perfil_ids'] ?? [];

                $userId = $_SESSION['user_id'] ?? null;

                if (!$userId) {
                    throw new Exception("Usuário não autenticado.");
                }

                $params = [
                    'nome' => $nome,
                    'rota' => $rota,
                    'icone' => $icone,
                    'id_modulo_pai' => $id_modulo_pai,
                    'ordem' => $ordem,
                    'tipo_menu' => $tipo_menu,
                    'status' => $status,
                    'ultima_alteracao_por' => $userId
                ];

                if ($id) {
                    $sql = "UPDATE modulos SET 
                            nome = :nome, rota = :rota, icone = :icone, id_modulo_pai = :id_modulo_pai, 
                            ordem = :ordem, tipo_menu = :tipo_menu, status = :status, 
                            ultima_alteracao_por = :ultima_alteracao_por
                            WHERE id = :id";
                    $params['id'] = $id;
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                } else {
                    $sql = "INSERT INTO modulos (nome, rota, icone, id_modulo_pai, ordem, tipo_menu, status, ultima_alteracao_por) 
                            VALUES (:nome, :rota, :icone, :id_modulo_pai, :ordem, :tipo_menu, :status, :ultima_alteracao_por)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $id = $pdo->lastInsertId();
                }

                self::saveModulePermissions($pdo, $id, $perfil_ids);

                $pdo->commit();
                header('Location: ../public/modulos.php?status=success');
                exit;
            } catch (PDOException $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Erro no ModuloController: " . $e->getMessage());
                header('Location: ../public/modulos.php?status=error&msg=Erro ao salvar o módulo.');
                exit;
            } catch (Exception $e) {
                header('Location: ../public/modulos.php?status=error&msg=' . urlencode($e->getMessage()));
                exit;
            }
        }
    }

    public static function listAllModules(): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT * FROM modulos ORDER BY id_modulo_pai ASC, ordem ASC, nome ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar módulos: " . $e->getMessage());
            return [];
        }
    }

    public static function listParentModules(): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT id, nome FROM modulos WHERE tipo_menu = 'menu_principal' ORDER BY nome ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar módulos pais: " . $e->getMessage());
            return [];
        }
    }

    public static function getModuleById(int $moduleId): ?array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT * FROM modulos WHERE id = ?");
            $stmt->execute([$moduleId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erro ao obter módulo por ID: " . $e->getMessage());
            return null;
        }
    }

    public static function getAllProfiles(): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT id, perfil FROM perfil ORDER BY perfil ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter todos os perfis: " . $e->getMessage());
            return [];
        }
    }

    public static function getModulePermissions(int $moduleId): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT id_perfil FROM perfil_modulo_permissao WHERE id_modulo = ?");
            $stmt->execute([$moduleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter permissões do módulo: " . $e->getMessage());
            return [];
        }
    }

    private static function saveModulePermissions(PDO $pdo, int $moduleId, array $perfil_ids)
    {
        $stmt_del = $pdo->prepare("DELETE FROM perfil_modulo_permissao WHERE id_modulo = ?");
        $stmt_del->execute([$moduleId]);
        if (!empty($perfil_ids)) {
            $sql = "INSERT INTO perfil_modulo_permissao (id_perfil, id_modulo, visualizar) VALUES ";
            $values = [];
            foreach ($perfil_ids as $perfilId) {
                $values[] = "(" . (int)$perfilId . ", " . (int)$moduleId . ", 1)";
            }
            $sql .= implode(", ", $values);
            $stmt_insert = $pdo->prepare($sql);
            $stmt_insert->execute();
        }
    }

    public static function searchModules(string $search): array
    {
        try {
            $pdo = getDbConnection();
            error_log("Executando busca por módulos com termo: " . $search);
            $sql = "SELECT id, nome, rota, status, tipo_menu FROM modulos WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $sql .= " AND (nome LIKE :search_nome OR rota LIKE :search_rota OR status LIKE :search_status OR tipo_menu LIKE :search_tipo)";
                $searchTerm = "%{$search}%";
                $params = [
                    ':search_nome' => $searchTerm,
                    ':search_rota' => $searchTerm,
                    ':search_status' => $searchTerm,
                    ':search_tipo' => $searchTerm,
                ];
            }
            $sql .= " ORDER BY nome ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Resultados encontrados: " . count($resultados));
            return $resultados;
        } catch (PDOException $e) {
            error_log("Erro na busca de módulos: " . $e->getMessage());
            return [];
        }
    }
}
// A chave extra foi removida daqui.