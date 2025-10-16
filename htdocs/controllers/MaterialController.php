<?php
// HTDOCS/controllers/MaterialController.php
// --- VERSÃO CORRIGIDA COM INCLUDES NO TOPO ---

// DEPENDÊNCIAS MOVEM-SE PARA CIMA PARA FICAREM DISPONÍVEIS GLOBALMENTE NO ARQUIVO
require_once __DIR__ . '/../config/db.php';
//require_once __DIR__ . '/../security/AuthManager.php';
require_once __DIR__ . '/../vendor/autoload.php';

class MaterialController
{
    /**
     * ID do módulo "Material" no banco de dados.
     * Tabela: modulos
     */
    private const MODULO_ID = 32;

    public function index()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Gerenciamento de Material";
        $pageScripts = ['dist/js/materiais-search.js'];
        $moduloId = self::MODULO_ID;

        ob_start();
        require_once __DIR__ . '/../views/material/index.php'; // View que vamos criar
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    public function create()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Novo Material";
        $isEditing = false;
        $material = null;
        $medidas = self::getMedidas(); // Reutilizando seu método estático

        ob_start();
        require_once __DIR__ . '/../views/material/form.php'; // View que vamos criar
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

        $pageTitle = "Editar Material";
        $isEditing = true;
        $material = self::getMaterialById((int)$id); // Reutilizando seu método estático
        $medidas = self::getMedidas(); // Reutilizando seu método estático

        ob_start();
        require_once __DIR__ . '/../views/material/form.php'; // View que vamos criar
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    // NOVO MÉTODO
    public function store()
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        // Movendo a lógica do seu método estático handleRequest() para cá
        $userId = $_SESSION['user_id'];
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $isEdit = !empty($id);
        $permissaoNecessaria = $isEdit ? 'editar' : 'criar';

        if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/material?status=no_permission');
            exit;
        }

        try {
            // Lógica de salvamento que já existe em handleRequest()
            $pdo = getDbConnection();
            $material = trim($_POST['material']);
            $descricao = trim($_POST['descricao']);
            $codigoMaterial = trim($_POST['codigoMaterial']);
            $id_medida = (int)$_POST['id_medida'];
            $ativo = isset($_POST['ativo']) ? 's' : 'n';

            if ($id) {
                $stmt = $pdo->prepare("UPDATE p_material SET material = ?, descricao = ?, codigoMaterial = ?, id_medida = ?, ativo = ?, ultima_alteracao_por = ?, data_ultima_alteracao = NOW() WHERE id = ?");
                $stmt->execute([$material, $descricao, $codigoMaterial, $id_medida, $ativo, $userId, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO p_material (material, descricao, codigoMaterial, id_medida, ativo, criado_por) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$material, $descricao, $codigoMaterial, $id_medida, $ativo, $userId]);
            }
            // REDIRECIONAMENTO CORRIGIDO
            header('Location: ' . BASE_PATH . '/material?status=success');
        } catch (PDOException $e) {
            error_log("Erro ao salvar material: " . $e->getMessage());
            $redirectUrl = $id ? '/material/editar/' . $id : '/material/novo';
            header('Location: ' . BASE_PATH . $redirectUrl . '?status=error');
        }
        exit;
    }

    // NOVO MÉTODO
    public function delete($id)
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/material?status=no_permission');
            exit;
        }

        self::deleteMaterial((int)$id); // Reutilizando seu método estático
        header('Location: ' . BASE_PATH . '/material?status=deleted');
        exit;
    }

    // NOVO MÉTODO
    public function search()
    {
        if (!AuthManager::validateSession()) {
            http_response_code(403);
            exit('Acesso negado.');
        }

        // Lógica do seu arquivo get_materiais_ajax.php
        $search = $_GET['search'] ?? '';
        $materiais = self::getMateriais($search);
        $canEdit = PermissionManager::can('editar', self::MODULO_ID);
        $canDelete = PermissionManager::can('excluir', self::MODULO_ID);

        if (empty($materiais)) {
            echo '<tr><td colspan="4" class="text-center text-muted">Nenhum material encontrado.</td></tr>';
            exit;
        }

        foreach ($materiais as $material) {
            echo "<tr>
                    <td>" . htmlspecialchars($material['codigoMaterial']) . "</td>
                    <td>" . htmlspecialchars($material['material']) . "</td>
                    <td>" . htmlspecialchars($material['unidade_medida']) . "</td>
                    <td><div class='btn-group btn-group-sm'>";
            if ($canEdit) {
                // LINK CORRIGIDO
                echo "<a href='" . BASE_PATH . "/material/editar/{$material['id']}' class='btn btn-primary'><i class='fas fa-edit'></i></a>";
            }
            if ($canDelete) {
                // LINK CORRIGIDO
                echo "<a href='" . BASE_PATH . "/material/excluir/{$material['id']}' class='btn btn-danger' onclick=\"return confirm('Tem certeza?')\"><i class='fas fa-trash'></i></a>";
            }
            echo "</div></td></tr>";
        }
    }

    /**
     * Processa a requisição para salvar (criar ou atualizar) um material.
     */
    public static function handleRequest(array $postData)
    {
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

        $id = !empty($postData['id']) ? (int)$postData['id'] : null;
        $material = trim($postData['material']);
        $descricao = trim($postData['descricao']);
        $codigoMaterial = trim($postData['codigoMaterial']);
        $id_medida = (int)$postData['id_medida'];
        $ativo = isset($postData['ativo']) ? 's' : 'n';

        try {
            $pdo = getDbConnection(); // Agora esta função será encontrada
            if ($id) {
                // Lógica de ATUALIZAÇÃO com auditoria
                $stmt = $pdo->prepare(
                    "UPDATE p_material SET 
                        material = ?, descricao = ?, codigoMaterial = ?, id_medida = ?, ativo = ?, 
                        ultima_alteracao_por = ?, data_ultima_alteracao = NOW() 
                    WHERE id = ?"
                );
                $stmt->execute([$material, $descricao, $codigoMaterial, $id_medida, $ativo, $userId, $id]);
            } else {
                // Lógica de INSERÇÃO com auditoria
                $stmt = $pdo->prepare(
                    "INSERT INTO p_material 
                        (material, descricao, codigoMaterial, id_medida, ativo, criado_por) 
                    VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$material, $descricao, $codigoMaterial, $id_medida, $ativo, $userId]);
            }
            header('Location: ../public/material.php?view=list&status=success');
        } catch (PDOException $e) {
            error_log("Erro ao salvar material: " . $e->getMessage());
            header('Location: ../public/material.php?view=form&id=' . $id . '&status=error');
        }
        exit;
    }

    /**
     * Busca uma lista de materiais com filtro de pesquisa.
     */
    public static function getMateriais($search = '')
    {
        $pdo = getDbConnection(); // Agora esta função será encontrada

        $sql = "SELECT mat.id, mat.material, mat.codigoMaterial, med.unidade as unidade_medida
                FROM p_material mat
                LEFT JOIN medidas med ON mat.id_medida = med.id";

        if (!empty($search)) {
            $sql .= " WHERE mat.material LIKE ? OR mat.codigoMaterial LIKE ? OR med.unidade LIKE ?";
            $stmt = $pdo->prepare($sql);
            $searchTerm = '%' . $search . '%';
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        } else {
            $sql .= " ORDER BY mat.material ASC";
            $stmt = $pdo->query($sql);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um único material pelo seu ID.
     */
    public static function getMaterialById($id)
    {
        $pdo = getDbConnection(); // Agora esta função será encontrada
        $stmt = $pdo->prepare("SELECT * FROM p_material WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Exclui um material do banco de dados.
     */
    public static function deleteMaterial($id)
    {
        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            // Retorna falso para indicar que a operação não foi permitida
            return false;
        }

        try {
            $pdo = getDbConnection(); // Agora esta função será encontrada
            $stmt = $pdo->prepare("DELETE FROM p_material WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao excluir material: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca todas as unidades de medida disponíveis para popular o dropdown.
     */
    public static function getMedidas()
    {
        $pdo = getDbConnection(); // Agora esta função será encontrada
        $stmt = $pdo->query("SELECT id, unidade FROM medidas WHERE ativo = 's' ORDER BY unidade ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os materiais distintos associados a um serviço específico.
     *
     * @param int $servicoId O ID do serviço.
     * @return array A lista de materiais encontrados.
     */
    public static function getMateriaisPorServico(int $servicoId): array
    {
        if (!$servicoId) {
            return [];
        }

        try {
            $pdo = getDbConnection();
            // Esta é a mesma consulta que estava no seu arquivo AJAX
            $sql = "SELECT DISTINCT m.id, m.codigoMaterial, m.material
                    FROM p_material m
                    JOIN estrutura_materiais em ON m.id = em.id_material
                    JOIN estruturas e ON em.id_estrutura = e.id
                    WHERE e.id_servico = ?
                    ORDER BY m.material ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$servicoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar materiais por serviço: " . $e->getMessage());
            // Em caso de erro, retorna um array vazio para não quebrar a API.
            return [];
        }
    }
}
