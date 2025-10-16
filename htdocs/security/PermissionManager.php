<?php
class PermissionManager
{
    private static $permissoesCache = null;

    /**
     * Verifica se o usuário logado tem permissão para uma ação específica em um módulo.
     *
     * @param string $acao A ação a ser verificada (ex: 'editar', 'excluir', 'criar').
     * @param int $moduleId O ID do módulo em questão.
     * @return bool
     */
    public static function can(string $acao, int $moduleId): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (isset($_SESSION['perfis']) && in_array(4, $_SESSION['perfis'])) {
            return true;
        }

        if (self::$permissoesCache === null) {
            self::loadPermissionsForUser($_SESSION['user_id']);
        }

        return isset(self::$permissoesCache[$moduleId]) && in_array($acao, self::$permissoesCache[$moduleId]);
    }

    /**
     * Carrega todas as permissões de ação de um usuário e as armazena em cache estático.
     */
    private static function loadPermissionsForUser(int $userId)
    {
        self::$permissoesCache = [];
        try {
            $pdo = getDbConnection();

            // PASSO A: Carrega as permissões base do perfil principal
            $sql_perfil = "
                SELECT DISTINCT pap.id_modulo, pap.acao
                FROM perfil_acao_permissao pap
                JOIN loginperfil lp ON pap.id_perfil = lp.id_perfil
                WHERE lp.id_login = :userId
            ";
            $stmt_perfil = $pdo->prepare($sql_perfil);
            $stmt_perfil->execute([':userId' => $userId]);

            foreach ($stmt_perfil->fetchAll(PDO::FETCH_ASSOC) as $row) {
                self::$permissoesCache[$row['id_modulo']][] = $row['acao'];
            }

            // PASSO B: Carrega e aplica sobreposições (overrides) do usuário
            $sql_usuario = "
                SELECT id_modulo, acao, permitido
                FROM usuario_acao_permissao
                WHERE id_usuario = :userId
                  AND (data_inicio_validade IS NULL OR CURDATE() >= data_inicio_validade)
                  AND (data_fim_validade IS NULL OR CURDATE() <= data_fim_validade)
            ";
            $stmt_usuario = $pdo->prepare($sql_usuario);
            $stmt_usuario->execute([':userId' => $userId]);

            foreach ($stmt_usuario->fetchAll(PDO::FETCH_ASSOC) as $override) {
                $moduleId = $override['id_modulo'];
                $action = $override['acao'];

                // Garante que o array para o módulo exista
                if (!isset(self::$permissoesCache[$moduleId])) {
                    self::$permissoesCache[$moduleId] = [];
                }

                // Remove a permissão antiga para evitar duplicatas antes de adicionar/remover
                self::$permissoesCache[$moduleId] = array_diff(self::$permissoesCache[$moduleId], [$action]);

                if ($override['permitido'] == 1) { // Regra "Permitir"
                    self::$permissoesCache[$moduleId][] = $action;
                }
                // Se for 'negar' (permitido=0), a ação já foi removida pelo array_diff e não é adicionada novamente.
            }
        } catch (PDOException $e) {
            error_log("Erro ao carregar permissões de ação para o usuário {$userId}: " . $e->getMessage());
        }
    }
}
