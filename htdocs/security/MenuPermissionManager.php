<?php
class MenuPermissionManager
{
    /**
     * Busca os itens de menu visíveis para um usuário, aplicando a nova hierarquia de permissões.
     * Esta versão utiliza uma única consulta SQL para determinar o resultado final.
     *
     * @param int $userId O ID do usuário logado.
     * @return array Um array hierárquico com os itens de menu permitidos.
     */
    public static function getVisibleMenuItems(int $userId): array
    {
        try {
            $pdo = getDbConnection();

            // Busca os perfis do usuário para a verificação de Administrador
            $stmt_perfis = $pdo->prepare("SELECT id_perfil FROM loginperfil WHERE id_login = :userId");
            $stmt_perfis->execute([':userId' => $userId]);
            $perfis = $stmt_perfis->fetchAll(PDO::FETCH_COLUMN);

            if (empty($perfis)) {
                return self::getDefaultMenu();
            }

            // REGRA 1: Administrador (perfil 4) vê tudo. Esta regra não muda e é a de maior prioridade.
            if (in_array(4, $perfis)) {
                $sql = "SELECT * FROM modulos WHERE status <> 'desativado' AND (tipo_menu = 'menu_principal' OR tipo_menu = 'submenu_item') ORDER BY ordem ASC, nome ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            } else {
                // --- LÓGICA ATUALIZADA PARA USUÁRIOS NORMAIS ---
                // Esta consulta SQL única resolve toda a hierarquia de permissões.
                // A explicação detalhada da consulta está nos comentários abaixo do arquivo.
                $sql = "
                    SELECT DISTINCT
                        m.*
                    FROM
                        modulos m
                    LEFT JOIN (
                        SELECT pmp.id_modulo
                        FROM perfil_modulo_permissao pmp
                        JOIN loginperfil lp ON pmp.id_perfil = lp.id_perfil
                        WHERE lp.id_login = :userId1 AND pmp.visualizar = 1
                    ) AS profile_perms ON m.id = profile_perms.id_modulo
                    LEFT JOIN (
                        SELECT ump.id_modulo, ump.permitido
                        FROM usuario_modulo_permissao ump
                        WHERE ump.id_usuario = :userId2
                          AND (ump.data_inicio_validade IS NULL OR CURDATE() >= ump.data_inicio_validade)
                          AND (ump.data_fim_validade IS NULL OR CURDATE() <= ump.data_fim_validade)
                    ) AS user_perms ON m.id = user_perms.id_modulo
                    WHERE
                        m.status = 'liberado'
                        AND (m.tipo_menu = 'menu_principal' OR m.tipo_menu = 'submenu_item')
                        AND (
                            user_perms.permitido = 1
                            OR
                            (user_perms.permitido IS NULL AND profile_perms.id_modulo IS NOT NULL)
                        )
                    ORDER BY
                        m.ordem ASC, m.nome ASC;
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([':userId1' => $userId, ':userId2' => $userId]);
            }

            $allowedModules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($allowedModules)) {
                return self::getDefaultMenu();
            }

            return self::buildMenuHierarchy($allowedModules);
        } catch (PDOException $e) {
            error_log("Erro ao buscar itens de menu: " . $e->getMessage());
            return self::getDefaultMenu();
        }
    }

    /**
     * Retorna apenas o menu Dashboard para usuários sem permissões.
     */
    private static function getDefaultMenu(): array
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->query("SELECT * FROM modulos WHERE nome = 'Dashboard' AND status = 'liberado'");
            $dashboard = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dashboard) {
                $dashboard['submenus'] = [];
                return [$dashboard['id'] => $dashboard];
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar menu padrão: " . $e->getMessage());
        }
        return [];
    }

    /**
     * Organiza uma lista plana de módulos em uma estrutura hierárquica.
     */
    private static function buildMenuHierarchy(array $modules): array
    {
        $menu = [];
        // Primeiro, adiciona todos os menus principais
        foreach ($modules as $module) {
            if ($module['tipo_menu'] === 'menu_principal') {
                $menu[$module['id']] = $module;
                $menu[$module['id']]['submenus'] = [];
            }
        }
        // Depois, aninha os submenus
        foreach ($modules as $module) {
            if ($module['tipo_menu'] === 'submenu_item' && isset($menu[$module['id_modulo_pai']])) {
                $menu[$module['id_modulo_pai']]['submenus'][] = $module;
            }
        }
        return $menu;
    }
}
