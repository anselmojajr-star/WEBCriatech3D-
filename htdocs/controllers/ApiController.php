<?php
// controllers/ApiController.php (VERSÃO CORRETA E ATUALIZADA)

class ApiController
{
    /**
     * Lida com requisições de salvamento para entidades, de forma dinâmica.
     *
     * @param string $tableName O nome da tabela do banco de dados (ex: 'perfil').
     * @param array $fieldMapping Um array associativo que mapeia [coluna_db => chave_input] (ex: ['perfil' => 'perfil_nome']).
     * @param string|null $uniqueCheckInputKey A chave do input a ser usada para verificar duplicidade (geralmente o campo de nome/texto).
     */
    public static function handleDynamicSave(string $tableName, array $fieldMapping, ?string $uniqueCheckInputKey = null)
    {
        // Define o cabeçalho da resposta como JSON
        header('Content-Type: application/json');

        // 1. Validação de Sessão
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            http_response_code(401); // Não autorizado
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
            exit;
        }

        // 2. Pega o input (funciona para JSON e POST)
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $params = [];
        $columns = [];
        $placeholders = [];

        // 3. Valida e prepara os dados com base no mapeamento
        foreach ($fieldMapping as $dbColumn => $inputKey) {
            $value = trim($data[$inputKey] ?? '');
            if (empty($value)) {
                http_response_code(400); // Requisição inválida
                echo json_encode(['success' => false, 'message' => "O campo {$inputKey} é obrigatório."]);
                exit;
            }
            $params[$dbColumn] = $value;
            $columns[] = $dbColumn;
            $placeholders[] = ":{$dbColumn}";
        }

        try {
            $pdo = getDbConnection();

            // 4. Verifica se já existe (se uma chave de verificação foi fornecida)
            if ($uniqueCheckInputKey && isset($data[$uniqueCheckInputKey])) {
                // Descobre a coluna correta do DB para a verificação de duplicidade
                $uniqueCheckDbColumn = array_search($uniqueCheckInputKey, $fieldMapping);
                if ($uniqueCheckDbColumn) {
                    $stmtCheck = $pdo->prepare("SELECT id FROM {$tableName} WHERE {$uniqueCheckDbColumn} LIKE ?");
                    $stmtCheck->execute([$data[$uniqueCheckInputKey]]);
                    if ($stmtCheck->fetch()) {
                        http_response_code(409); // Conflito
                        echo json_encode(['success' => false, 'message' => 'Este item já está cadastrado.']);
                        exit;
                    }
                }
            }

            // 5. Insere no banco
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $tableName,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );

            $stmtInsert = $pdo->prepare($sql);
            $stmtInsert->execute($params);
            $newId = $pdo->lastInsertId();

            // 6. Retorna sucesso
            echo json_encode(['success' => true, 'id' => $newId] + $params);
        } catch (PDOException $e) {
            http_response_code(500); // Erro interno do servidor
            error_log("Erro em handleDynamicSave para a tabela {$tableName}: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro interno do servidor.']);
        }
    }
}
