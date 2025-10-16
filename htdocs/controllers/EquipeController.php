<?php
// controllers/EquipeController.php

require_once __DIR__ . '/../config/db.php';

class EquipeController
{
    /**
     * ID do módulo "Equipes" na tabela 'modulos'.
     * ATENÇÃO: Verifique o ID correto na sua base de dados!
     */
    private const MODULO_ID = 65; // <<<---- CONFIRME ESTE NÚMERO

    public function index()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
        $pageTitle = "Gestão de Equipes";
        $pageScripts = ['dist/js/equipes-search.js'];
        $moduloId = self::MODULO_ID;
        ob_start();
        require_once __DIR__ . '/../views/equipe/index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layout.php';
    }

    public function search()
    {
        if (!AuthManager::validateSession()) {
            exit('Acesso negado.');
        }

        $search = $_GET['search'] ?? '';
        $equipes = self::getEquipes($search);
        $canEdit = PermissionManager::can('editar', self::MODULO_ID);
        $canDelete = PermissionManager::can('excluir', self::MODULO_ID);

        if (empty($equipes)) {
            echo '<tr><td colspan="4" class="text-center text-muted">Nenhuma equipe encontrada.</td></tr>';
            exit;
        }

        foreach ($equipes as $equipe) {
            $ativoBadge = $equipe['ativo'] ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
            echo "<tr>
                    <td>" . htmlspecialchars($equipe['nome_equipe']) . "</td>
                    <td>" . htmlspecialchars($equipe['lider_nome'] ?? 'N/D') . "</td>
                    <td>" . $ativoBadge . "</td>
                    <td><div class='btn-group btn-group-sm'>";
            if ($canEdit) {
                echo "<a href='" . BASE_PATH . "/equipes/editar/{$equipe['id']}' class='btn btn-primary'><i class='fas fa-edit'></i></a>";
            }
            if ($canDelete) {
                echo "<a href='" . BASE_PATH . "/equipes/excluir/{$equipe['id']}' class='btn btn-danger' onclick=\"return confirm('Tem certeza?')\"><i class='fas fa-trash'></i></a>";
            }
            echo "</div></td></tr>";
        }
    }

    public function create()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
        $pageTitle = "Nova Equipe";
        $pageScripts = ['plugins/select2/js/select2.full.min.js', 'dist/js/equipe-form.js'];
        $isEditing = false;
        $equipe = [];
        $membrosDaEquipe = [];

        // >>> NOVAS VARIÁVEIS PARA O FORMULÁRIO <<<
        $lideres = self::getUsuariosParaLideres();
        $todosOsUsuarios = self::getAllUsuariosComPerfis();
        $perfis = self::getAllPerfis();
        $usuariosJaEmEquipe = self::getUsuariosEmEquipes();

        ob_start();
        require_once __DIR__ . '/../views/equipe/form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layout.php';
    }

    public function edit($id)
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
        $pageTitle = "Editar Equipe";
        $pageScripts = ['plugins/select2/js/select2.full.min.js', 'dist/js/equipe-form.js'];
        $isEditing = true;

        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM equipes WHERE id = ?");
        $stmt->execute([(int)$id]);
        $equipe = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtMembros = $pdo->prepare("SELECT id_usuario FROM equipe_membros WHERE id_equipe = ?");
        $stmtMembros->execute([(int)$id]);
        $membrosDaEquipe = $stmtMembros->fetchAll(PDO::FETCH_COLUMN);

        // >>> NOVAS VARIÁVEIS PARA O FORMULÁRIO <<<
        $lideres = self::getUsuariosParaLideres();
        $todosOsUsuarios = self::getAllUsuariosComPerfis();
        $perfis = self::getAllPerfis();
        // Ao editar, excluímos os membros da equipa atual da verificação de duplicidade
        $usuariosJaEmEquipe = self::getUsuariosEmEquipes((int)$id);

        ob_start();
        require_once __DIR__ . '/../views/equipe/form.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../views/layout.php';
    }

    public function store()
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        $id = $_POST['id'] ?? null;
        $isEdit = !empty($id);
        $permissaoNecessaria = $isEdit ? 'editar' : 'criar';

        if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/equipes?status=no_permission');
            exit;
        }

        $pdo = getDbConnection();
        try {
            $pdo->beginTransaction();

            $params = [
                'nome_equipe' => trim($_POST['nome_equipe']),
                'id_lider' => empty($_POST['id_lider']) ? null : (int)$_POST['id_lider'],
                'descricao' => trim($_POST['descricao']),
                'ativo' => isset($_POST['ativo']) ? 1 : 0,
                'user_id' => $_SESSION['user_id']
            ];

            if ($isEdit) {
                $sql = "UPDATE equipes SET nome_equipe = :nome_equipe, id_lider = :id_lider, descricao = :descricao, ativo = :ativo, ultima_alteracao_por = :user_id WHERE id = :id";
                $params['id'] = $id;
            } else {
                $sql = "INSERT INTO equipes (nome_equipe, id_lider, descricao, ativo, criado_por) VALUES (:nome_equipe, :id_lider, :descricao, :ativo, :user_id)";
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $equipeId = $isEdit ? $id : $pdo->lastInsertId();

            $membrosIds = $_POST['membros_ids'] ?? [];
            $stmtDel = $pdo->prepare("DELETE FROM equipe_membros WHERE id_equipe = ?");
            $stmtDel->execute([$equipeId]);

            if (!empty($membrosIds)) {
                $stmtIns = $pdo->prepare("INSERT INTO equipe_membros (id_equipe, id_usuario) VALUES (?, ?)");
                foreach ($membrosIds as $membroId) {
                    $stmtIns->execute([$equipeId, (int)$membroId]);
                }
            }

            $pdo->commit();
            header('Location: ' . BASE_PATH . '/equipes?status=success');
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Erro ao salvar equipe: " . $e->getMessage());
            header('Location: ' . BASE_PATH . '/equipes?status=error&msg=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function delete($id)
    {
        if (!AuthManager::validateSession()) {
            exit;
        }
        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/equipes?status=no_permission');
            exit;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM equipes WHERE id = ?");
            $stmt->execute([(int)$id]);
            header('Location: ' . BASE_PATH . '/equipes?status=deleted');
        } catch (Exception $e) {
            error_log("Erro ao excluir equipe: " . $e->getMessage());
            header('Location: ' . BASE_PATH . '/equipes?status=error');
        }
        exit;
    }

    private static function getEquipes(string $search = ''): array
    {
        $pdo = getDbConnection();
        $sql = "SELECT e.*, u.username as lider_nome 
                FROM equipes e
                LEFT JOIN usuarios u ON e.id_lider = u.id";

        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE e.nome_equipe LIKE ? OR u.username LIKE ?";
            $params = ['%' . $search . '%', '%' . $search . '%'];
        }
        $sql .= " ORDER BY e.nome_equipe ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca apenas usuários que podem ser líderes e já formata o nome com o perfil
    private static function getUsuariosParaLideres(): array
    {
        $pdo = getDbConnection();
        $idPerfilEquipe = 6; // Assumindo ID 6 para o perfil 'equipe'

        $sql = "SELECT u.id, u.matricula, CONCAT('[', u.matricula, '] ', u.username, ' [', p.perfil, ']') as nome_com_cargo
                FROM usuarios u
                JOIN loginperfil lp ON u.id = lp.id_login
                JOIN perfil p ON lp.id_perfil = p.id
                WHERE u.ativo = 1
                AND u.id NOT IN (
                    SELECT id_login FROM loginperfil WHERE id_perfil = ?
                )
                GROUP BY u.id
                ORDER BY u.username ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idPerfilEquipe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // >>> NOVA FUNÇÃO <<<
    // Busca todos os usuários, mas agora também traz os perfis de cada um para a filtragem
    private static function getAllUsuariosComPerfis(): array
    {
        $pdo = getDbConnection();
        $sql = "SELECT u.id, u.username, u.matricula, GROUP_CONCAT(lp.id_perfil) as perfis_ids
                FROM usuarios u
                LEFT JOIN loginperfil lp ON u.id = lp.id_login
                WHERE u.ativo = 1
                GROUP BY u.id
                ORDER BY u.username ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // >>> NOVA FUNÇÃO <<<
    private static function getAllPerfis(): array
    {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT id, perfil FROM perfil ORDER BY perfil ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // >>> NOVA FUNÇÃO <<<
    // Retorna um array com os IDs de todos os usuários que já estão noutra equipa
    private static function getUsuariosEmEquipes(int $ignorarEquipeId = null): array
    {
        $pdo = getDbConnection();
        $sql = "SELECT DISTINCT id_usuario FROM equipe_membros";
        $params = [];

        if ($ignorarEquipeId) {
            $sql .= " WHERE id_equipe <> ?";
            $params[] = $ignorarEquipeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
