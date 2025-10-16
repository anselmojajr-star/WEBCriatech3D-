<?php
// HTDOCS/controllers/FuncionarioController.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';
//require_once __DIR__ . '/../security/PermissionManager.php';
//require_once __DIR__ . '/../security/AuthManager.php';
require_once __DIR__ . '/../security/session_manager.php';
require_once __DIR__ . '/../vendor/autoload.php';

class FuncionarioController
{
    /**
     * ID do módulo "Colaborador" no banco de dados.
     * Use 'const' para valores que não mudam.
     */
    private const MODULO_ID = 31;

    public function index()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Gerenciamento de Colaboradores";
        $pageScripts = ['dist/js/funcionarios-search.js'];

        // LINHA ADICIONADA AQUI:
        $moduloIdAtual = 31;

        ob_start();
        require_once __DIR__ . '/../views/funcionario/index.php'; // Esta linha chama a view
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    public function create()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageTitle = "Novo Colaborador";
        $isEdit = false;
        $funcionario = [];
        $endereco = [];
        $usuario = [];
        $perfis = GerenteController::getAllPerfisGerenciaveis();
        $perfilUsuario = null;
        $estados = self::getEstados();

        ob_start();
        // Vamos criar este arquivo de view no próximo passo
        require_once __DIR__ . '/../views/funcionario/form.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    public function edit($id)
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $pageScripts = ['dist/js/funcionario-form.js'];
        $idFuncionario = (int)$id;
        $pageTitle = "Editar Colaborador";
        $isEdit = true; // Essencial para a view saber que está em modo de edição

        // Lógica para buscar todos os dados do funcionário pelo ID
        $pdo = getDbConnection();

        // Busca dados da tabela 'funcionario'
        $stmt_func = $pdo->prepare("SELECT * FROM funcionario WHERE id = ?");
        $stmt_func->execute([$idFuncionario]);
        $funcionario = $stmt_func->fetch(PDO::FETCH_ASSOC) ?: [];

        // Busca dados da tabela 'enderecos_pessoas'
        $stmt_end = $pdo->prepare("SELECT * FROM enderecos_pessoas WHERE id_funcionario = ?");
        $stmt_end->execute([$idFuncionario]);
        $endereco = $stmt_end->fetch(PDO::FETCH_ASSOC) ?: [];

        // Busca dados da tabela 'usuarios'
        $stmt_user = $pdo->prepare("SELECT * FROM usuarios WHERE id_funcionario = ?");
        $stmt_user->execute([$idFuncionario]);
        $usuario = $stmt_user->fetch(PDO::FETCH_ASSOC) ?: [];

        // Busca o perfil do usuário na tabela 'loginperfil'
        $perfilUsuario = null;
        if (!empty($usuario['id'])) {
            $stmt_perfil = $pdo->prepare("SELECT id_perfil FROM loginperfil WHERE id_login = ?");
            $stmt_perfil->execute([$usuario['id']]);
            $perfilUsuario = $stmt_perfil->fetchColumn();
        }

        // Busca dados necessários para os <select> do formulário
        $perfis = GerenteController::getAllPerfisGerenciaveis();
        $estados = self::getEstados();

        // Renderiza a view do formulário, passando todas as variáveis com os dados
        ob_start();
        require_once __DIR__ . '/../views/funcionario/form.php';
        $content = ob_get_clean();

        require_once __DIR__ . '/../views/layout.php';
    }

    public function store()
    {
        if (!AuthManager::validateSession()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }

        $action = $_POST['action'] ?? '';
        $idFuncionario = $_POST['id'] ?? $_POST['id_funcionario'] ?? null;
        $isEdit = !empty($idFuncionario);
        $permissaoNecessaria = $isEdit ? 'editar' : 'criar';

        if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
            $redirectUrl = $idFuncionario ? '/funcionario/editar/' . $idFuncionario : '/funcionario';
            header('Location: ' . BASE_PATH . $redirectUrl . '?status=no_permission');
            exit;
        }

        try {
            $pdo = getDbConnection();
            $pdo->beginTransaction();

            switch ($action) {
                case 'salvarPessoais':
                    $idFuncionario = self::salvarDadosPessoais($pdo);
                    break;
                case 'salvarAcesso':
                    self::salvarDadosAcesso($pdo);
                    break;
                case 'salvarEndereco':
                    self::salvarDadosEndereco($pdo);
                    break;
                default:
                    throw new Exception("Ação de salvamento inválida.");
            }

            $pdo->commit();

            // Redirecionamento CORRETO para a nova rota de edição
            header('Location: ' . BASE_PATH . '/funcionario/editar/' . $idFuncionario . '?status=success');
            exit;
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            error_log("Erro no FuncionarioController->store(): " . $e->getMessage());

            // Redirecionamento CORRETO para a nova rota de erro
            $redirectUrl = $idFuncionario ? '/funcionario/editar/' . $idFuncionario : '/funcionario/novo';
            $errorMessage = urlencode($e->getMessage());
            header('Location: ' . BASE_PATH . $redirectUrl . '?status=error&msg=' . $errorMessage);
            exit;
        }
    }


    public function delete($id)
    {
        if (!AuthManager::validateSession()) {
            exit;
        }

        // 1. Adicionamos a verificação de permissão
        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            header('Location: ' . BASE_PATH . '/funcionario?status=no_permission');
            exit;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM funcionario WHERE id = :id");
            $stmt->execute(['id' => (int)$id]);

            // 2. Redirecionamento de sucesso
            header('Location: ' . BASE_PATH . '/funcionario?status=deleted');
        } catch (PDOException $e) {
            error_log("Erro ao excluir funcionário (ID: $id): " . $e->getMessage());
            $errorMsg = 'Ocorreu um erro ao tentar excluir o colaborador.';

            // Se a exclusão falhar por causa de uma chave estrangeira (ex: o funcionário tem um usuário)
            if ($e->getCode() == '23000') {
                $errorMsg = 'Este colaborador não pode ser excluído, pois está associado a um usuário do sistema.';
            }

            // 3. Redirecionamento de erro
            header('Location: ' . BASE_PATH . '/funcionario?status=error&msg=' . urlencode($errorMsg));
        }

        exit;
    }

    public function search()
    {
        // 1. Validação de segurança (já estava correta)
        if (!AuthManager::validateSession()) {
            http_response_code(401);
            exit('Sessão expirada');
        }

        // 2. Busca os dados (lógica do arquivo ajax antigo)
        $search = $_GET['search'] ?? '';
        $funcionarios = self::getFuncionarios($search);
        $moduloIdAtual = self::MODULO_ID; // Usando a constante da classe

        // 3. Verifica as permissões
        $canEdit = PermissionManager::can('editar', $moduloIdAtual);
        $canDelete = PermissionManager::can('excluir', $moduloIdAtual);

        // 4. Gera a resposta HTML (lógica do arquivo ajax antigo)
        if (empty($funcionarios)) {
            echo '<tr><td colspan="6" class="text-center text-muted">Nenhum colaborador encontrado.</td></tr>';
            exit;
        }

        foreach ($funcionarios as $f) {
            $id           = (int)($f['id'] ?? 0);
            $nome         = htmlspecialchars($f['nome'] ?? '');
            $cpf          = htmlspecialchars($f['cpf'] ?? '');
            $naturalidade = htmlspecialchars($f['naturalidade'] ?? '');
            $email        = htmlspecialchars($f['email'] ?? '—');
            $telefone     = htmlspecialchars($f['telefone_contato'] ?? '—');

            echo "<tr>
                <td>{$nome}</td>
                <td>{$cpf}</td>
                <td>{$naturalidade}</td>
                <td>{$email}</td>
                <td>{$telefone}</td>
                <td>
                  <div class='btn-group btn-group-sm'>";

            if ($canEdit) {
                // CORREÇÃO: Apontando para a nova rota
                echo "<a href='" . BASE_PATH . "/funcionario/editar/{$id}' class='btn btn-primary'><i class='fas fa-edit'></i></a>";
            }
            if ($canDelete) {
                // CORREÇÃO: Apontando para a nova rota
                echo "<a href='" . BASE_PATH . "/funcionario/excluir/{$id}' class='btn btn-danger' onclick=\"return confirm('Excluir colaborador?')\"><i class='fas fa-trash'></i></a>";
            }

            echo "  </div>
                </td>
              </tr>";
        }
    }

    /**
     * Processa o envio do formulário de funcionário
     */
    public static function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if (session_status() == PHP_SESSION_NONE) session_start();

            $isEdit = !empty($_POST['id_funcionario']) || !empty($_POST['id']);
            $permissaoNecessaria = $isEdit ? 'editar' : 'criar';
            if (!PermissionManager::can($permissaoNecessaria, self::MODULO_ID)) {
                header('Location: ../public/funcionario.php?status=error&msg=' . urlencode('Você não tem permissão para executar esta ação.'));
                exit;
            }

            try {
                $pdo = getDbConnection();
                $pdo->beginTransaction();

                $idFuncionario = null; // Inicia a variável
                switch ($action) {
                    case 'salvarPessoais':
                        $idFuncionario = self::salvarDadosPessoais($pdo); // AGORA CAPTURA o ID retornado
                        break;
                    case 'salvarAcesso':
                        self::salvarDadosAcesso($pdo);
                        $idFuncionario = $_POST['id_funcionario'];
                        break;
                    case 'salvarEndereco':
                        self::salvarDadosEndereco($pdo);
                        $idFuncionario = $_POST['id_funcionario'];
                        break;
                    default:
                        throw new Exception("Ação de salvamento inválida.");
                }

                $pdo->commit();
                // A linha que pegava o ID do POST não é mais a principal fonte
                header('Location: ../public/funcionario.php?view=form&id=' . $idFuncionario . '&status=success');
                exit;
            } catch (Exception $e) {
                if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
                error_log("Erro no FuncionarioController: " . $e->getMessage());
                $errorMsg = 'Ocorreu um erro ao salvar os dados: ' . $e->getMessage();
                header('Location: ../public/funcionario.php?status=error&msg=' . urlencode($errorMsg));
                exit;
            }
        }
    }

    /**
     * Salva todos os dados de uma vez (para compatibilidade)
     */
    private static function salvarTodosDados($pdo)
    {
        // Processa upload da foto
        $fotoPerfilPath = self::processarUploadFoto();

        // Insere dados de acesso
        $id_usuario = self::salvarUsuario($pdo, $fotoPerfilPath);

        // Insere dados pessoais
        self::salvarFuncionario($pdo, $id_usuario);

        // Insere perfil
        if (!empty($_POST['perfil'])) {
            self::salvarPerfil($pdo, $id_usuario, $_POST['perfil']);
        }
    }

    /**
     * Salva apenas os dados pessoais
     */
    private static function salvarDadosPessoais($pdo)
    {
        $id = $_POST['id'] ?? null;
        $cpf = $_POST['cpf'] ?? '';

        // --- ADIÇÃO AQUI ---
        // Remove todos os caracteres não numéricos do CPF
        $cpf_numerico = preg_replace('/[^0-9]/', '', $cpf);
        // --- FIM DA ADIÇÃO ---

        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'cpf' => $cpf, // Mantém o CPF formatado
            'cpf_numerico' => $cpf_numerico, // Salva o CPF apenas com números
            'naturalidade' => $_POST['naturalidade'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
            'telefone_contato' => $_POST['telefone_contato'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];

        if ($id) {
            // Atualiza funcionário existente
            $stmt = $pdo->prepare("UPDATE funcionario SET nome = :nome, cpf = :cpf, cpf_numerico = :cpf_numerico, naturalidade = :naturalidade, data_nascimento = :data_nascimento, telefone_contato = :telefone_contato, email = :email WHERE id = :id");
            $dados['id'] = $id;
            $stmt->execute($dados);
            return $id;
        } else {
            // Insere novo funcionário
            $stmt = $pdo->prepare("INSERT INTO funcionario (nome, cpf, cpf_numerico, naturalidade, data_nascimento, telefone_contato, email) VALUES (:nome, :cpf, :cpf_numerico, :naturalidade, :data_nascimento, :telefone_contato, :email)");
            $stmt->execute($dados);
            return $pdo->lastInsertId();
        }
    }

    /**
     * Salva apenas os dados de acesso
     */
    private static function salvarDadosAcesso($pdo)
    {
        $matricula = $_POST['matricula'] ?? '';
        if (empty($matricula)) {
            throw new Exception("O campo Matrícula é obrigatório.");
        }

        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_funcionario = $_POST['id_funcionario'] ?? null;
        $id_perfil = $_POST['id_perfil'] ?? null;

        if (!$id_funcionario) {
            throw new Exception("ID do funcionário é obrigatório.");
        }

        // 1. Chama o método auxiliar para criar ou atualizar o usuário na tabela 'usuarios'
        $id_usuario_salvo = self::salvarOuAtualizarUsuario($pdo, $id_usuario, $id_funcionario);

        // 2. Chama o método auxiliar para associar o perfil na tabela 'loginperfil'
        if ($id_perfil) {
            self::salvarPerfil($pdo, $id_usuario_salvo, $id_perfil);
        }
    }

    /**
     * MÉTODO AUXILIAR NOVO: Cria um novo usuário ou atualiza um existente.
     * @return int ID do usuário salvo (seja novo ou existente).
     */
    private static function salvarOuAtualizarUsuario($pdo, $id_usuario, $id_funcionario)
    {
        $password = $_POST['password'] ?? '';
        $params = [
            'username' => $_POST['username'] ?? '',
            'matricula' => $_POST['matricula'] ?? '',
            'ativo' => isset($_POST['ativo']) ? 1 : 0,
        ];

        if ($id_usuario) { // Se um id_usuario já existe, é uma ATUALIZAÇÃO
            if (!empty($password)) {
                // Atualiza com nova senha
                $sql = "UPDATE usuarios SET username=:username, matricula=:matricula, senha=:senha, ativo=:ativo WHERE id=:id";
                $params['senha'] = password_hash($password, PASSWORD_DEFAULT);
            } else {
                // Atualiza sem alterar a senha
                $sql = "UPDATE usuarios SET username=:username, matricula=:matricula, ativo=:ativo WHERE id=:id";
            }
            $params['id'] = $id_usuario;
        } else { // Se não há id_usuario, é uma CRIAÇÃO
            if (empty($password)) {
                throw new Exception("Senha é obrigatória para criar um novo usuário.");
            }
            $sql = "INSERT INTO usuarios (id_funcionario, username, matricula, senha, ativo) VALUES (:id_funcionario, :username, :matricula, :senha, :ativo)";
            $params['id_funcionario'] = $id_funcionario;
            $params['senha'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Retorna o ID do usuário que foi salvo ou atualizado
        return $id_usuario ?: $pdo->lastInsertId();
    }

    /**
     * Salva apenas os dados de endereço
     */
    private static function salvarDadosEndereco($pdo)
    {
        $id_funcionario = $_POST['id_funcionario'] ?? null;
        if (!$id_funcionario) {
            throw new Exception("ID do funcionário não informado");
        }

        $dados = [
            'id_funcionario' => $id_funcionario,
            'cep' => $_POST['cep'] ?? '',
            'logradouro' => $_POST['logradouro'] ?? '',
            'numero' => $_POST['numero'] ?? '',
            'complemento' => $_POST['complemento'] ?? '',
            'bairro' => $_POST['bairro'] ?? '',
            'id_cidade' => $_POST['id_cidade'] ?? null,
            'id_estado' => $_POST['id_estado'] ?? null
        ];

        $stmt_check = $pdo->prepare("SELECT id FROM enderecos_pessoas WHERE id_funcionario = :id_funcionario");
        $stmt_check->execute(['id_funcionario' => $id_funcionario]);
        $endereco_existente = $stmt_check->fetch();

        if ($endereco_existente) {
            $sql = "UPDATE enderecos_pessoas SET 
                cep = :cep, logradouro = :logradouro, numero = :numero, complemento = :complemento, 
                bairro = :bairro, id_cidade = :id_cidade, id_estado = :id_estado
                WHERE id_funcionario = :id_funcionario";
        } else {
            $sql = "INSERT INTO enderecos_pessoas 
                (id_funcionario, cep, logradouro, numero, complemento, bairro, id_cidade, id_estado) 
                VALUES 
                (:id_funcionario, :cep, :logradouro, :numero, :complemento, :bairro, :id_cidade, :id_estado)";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($dados);
    }

    /**
     * Processa o upload da foto de perfil
     */
    private static function processarUploadFoto()
    {
        if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../public/' . UPLOAD_DIR_USERS_PHOTOS;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['foto_perfil']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetFilePath)) {
            return UPLOAD_DIR_USERS_PHOTOS . $fileName;
        }

        return null;
    }

    /**
     * Salva os dados do usuário no banco
     */
    private static function salvarUsuario($pdo, $fotoPerfilPath)
    {
        $dados = [
            'username' => $_POST['username'] ?? '',
            'matricula' => $_POST['matricula'] ?? '',
            'senha' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'foto_perfil' => $fotoPerfilPath,
            'ativo' => isset($_POST['ativo']) ? 1 : 0
        ];

        $id_usuario = $_POST['id_usuario'] ?? null;

        if ($id_usuario) {
            // Atualiza usuário existente
            $stmt = $pdo->prepare("UPDATE usuarios SET 
                username = :username, 
                matricula = :matricula,
                senha = :senha, 
                foto_perfil = :foto_perfil, 
                ativo = :ativo 
                WHERE id = :id");

            $dados['id'] = $id_usuario;
        } else {
            // Insere novo usuário
            $stmt = $pdo->prepare("INSERT INTO usuarios 
                (username, matricula, senha, foto_perfil, ativo) 
                VALUES 
                (:username, :matricula, :senha, :foto_perfil, :ativo)");
        }

        $stmt->execute($dados);

        return $id_usuario ?: $pdo->lastInsertId();
    }

    /**
     * Salva os dados do funcionário no banco
     */
    private static function salvarFuncionario($pdo, $id_usuario)
    {
        $dados = [
            'nome' => $_POST['nome'] ?? '',
            'cpf' => $_POST['cpf'] ?? '',
            'matricula' => $_POST['matricula'] ?? '',
            'naturalidade' => $_POST['naturalidade'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
            'telefone_contato' => $_POST['telefone_contato'] ?? '',
            'email' => $_POST['email'] ?? '',
            'id_usuario' => $id_usuario
        ];

        $stmt = $pdo->prepare("INSERT INTO funcionario 
            (nome, cpf, matricula, naturalidade, data_nascimento, telefone_contato, email, id_usuario) 
            VALUES 
            (:nome, :cpf, :matricula, :naturalidade, :data_nascimento, :telefone_contato, :email, :id_usuario)");

        $stmt->execute($dados);
    }

    /**
     * Salva o perfil do usuário
     */
    private static function salvarPerfil($pdo, $id_usuario, $id_perfil)
    {
        // Remove perfis existentes para este login
        $stmt_del = $pdo->prepare("DELETE FROM loginperfil WHERE id_login = :id_login");
        $stmt_del->execute(['id_login' => $id_usuario]);

        // Insere o novo perfil selecionado
        $stmt_add = $pdo->prepare("INSERT INTO loginperfil (id_login, id_perfil) VALUES (:id_login, :id_perfil)");
        $stmt_add->execute(['id_login' => $id_usuario, 'id_perfil' => $id_perfil]);
    }

    /**
     * Salva as permissões granulares da tabela usuariopermissao
     */
    private static function salvarPermissoesGranulares($pdo, $id_usuario)
    {
        // Pega o id da tabela de junção loginperfil
        $stmt_lp = $pdo->prepare("SELECT id FROM loginperfil WHERE id_login = :id_login");
        $stmt_lp->execute(['id_login' => $id_usuario]);
        $id_loginPerfil = $stmt_lp->fetchColumn();

        if (!$id_loginPerfil) {
            // Se não houver um perfil associado, não há onde salvar as permissões.
            // Isso pode acontecer se o perfil não foi selecionado no formulário.
            // Pode-se optar por ignorar ou lançar um erro. Vamos ignorar por enquanto.
            return;
        }

        // Prepara as permissões a partir do POST. O que não for enviado, será 0.
        $permissoesPost = $_POST['permissao'] ?? [];
        $dados_perm = [
            'id_loginPerfil' => $id_loginPerfil,
            'cadastrar' => in_array('cadastrar', $permissoesPost) ? 1 : 0,
            'editar' => in_array('editar', $permissoesPost) ? 1 : 0,
            'deletar' => in_array('deletar', $permissoesPost) ? 1 : 0
        ];

        // Verifica se já existe um registro para atualizar ou se precisa inserir um novo
        $stmt_check = $pdo->prepare("SELECT id FROM usuariopermissao WHERE id_loginPerfil = :id_loginPerfil");
        $stmt_check->execute(['id_loginPerfil' => $id_loginPerfil]);

        if ($stmt_check->fetch()) {
            // UPDATE
            $sql = "UPDATE usuariopermissao SET cadastrar = :cadastrar, editar = :editar, deletar = :deletar WHERE id_loginPerfil = :id_loginPerfil";
        } else {
            // INSERT
            $sql = "INSERT INTO usuariopermissao (id_loginPerfil, cadastrar, editar, deletar) VALUES (:id_loginPerfil, :cadastrar, :editar, :deletar)";
        }

        $stmt_save = $pdo->prepare($sql);
        $stmt_save->execute($dados_perm);
    }

    /**
     * Processa a solicitação de exclusão de um funcionário via GET.
     */
    public static function handleDeleteRequest()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!PermissionManager::can('excluir', self::MODULO_ID)) {
            header('Location: ../public/funcionario.php?status=error&msg=' . urlencode('Você não tem permissão para excluir colaboradores.'));
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ../public/funcionario.php?status=error&msg=' . urlencode('ID do colaborador não fornecido.'));
            exit;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("DELETE FROM funcionario WHERE id = :id");
            $stmt->execute(['id' => $id]);
            header('Location: ../public/funcionario.php?status=deleted');
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao excluir funcionário (ID: $id): " . $e->getMessage());
            if ($e->getCode() == '23000') {
                $errorMsg = 'Este colaborador não pode ser excluído, pois está associado a um usuário do sistema.';
            } else {
                $errorMsg = 'Ocorreu um erro ao tentar excluir o colaborador.';
            }
            header('Location: ../public/funcionario.php?status=error&msg=' . urlencode($errorMsg));
            exit;
        }
    }

    public static function getFuncionarios($search = '')
    {
        $pdo = getDbConnection();
        // A base da consulta SQL
        $sql = "SELECT id, nome, cpf, naturalidade, email, telefone_contato FROM funcionario";
        $params = [];

        // Verifica se um termo de busca foi realmente fornecido
        if (!empty(trim($search))) {
            $searchTerm = '%' . trim($search) . '%';
            // Pega apenas os dígitos do termo de busca para pesquisar no CPF
            $searchDigits = preg_replace('/[^0-9]/', '', $search);

            // Adiciona a cláusula WHERE para buscar no nome
            $sql .= " WHERE nome LIKE :searchTerm";
            $params[':searchTerm'] = $searchTerm;

            // Se o termo de busca contiver números, adiciona a busca pelo CPF numérico
            if (!empty($searchDigits)) {
                $sql .= " OR cpf_numerico LIKE :searchDigits";
                $params[':searchDigits'] = '%' . $searchDigits . '%';
            }
        }

        $sql .= " ORDER BY nome ASC";

        try {
            $stmt = $pdo->prepare($sql);
            // Executa a consulta com os parâmetros (que estarão vazios se não houver busca)
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar funcionários: " . $e->getMessage() . " | SQL: $sql");
            return [];
        }
    }

    /**
     * Retorna todos os estados do banco de dados, ordenados por nome.
     */
    public static function getEstados(): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT id, nome, uf FROM estados ORDER BY nome ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar estados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retorna todas as cidades de um estado específico.
     */
    public static function getCidadesPorEstado(int $estadoId): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT id, cidade FROM cidades WHERE id_estado = ? ORDER BY cidade ASC");
            $stmt->execute([$estadoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar cidades por estado: " . $e->getMessage());
            return [];
        }
    }
}

// Inicia o processamento da requisição
//FuncionarioController::handleRequest();
