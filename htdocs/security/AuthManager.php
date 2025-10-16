<?php
// HTDOCS/security/AuthManager.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/session_manager.php';

class AuthManager
{
    /**
     * Tenta autenticar um usuário e retorna um array com o status e a mensagem.
     * @param string $username
     * @param string $password
     * @return array
     */
    public static function login(string $username, string $password): array
    {
        try {
            $pdo = getDbConnection();

            // 1. Busque o usuário pelo username.
            $stmt = $pdo->prepare("SELECT id, senha, tentativas, bloqueado, ativo FROM usuarios WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['status' => 'error', 'message' => 'Usuário ou senha incorretos.'];
            }

            // 2. Verifique o status da conta.
            if (!$user['ativo']) {
                return ['status' => 'error', 'message' => 'Sua conta está inativa. Por favor, entre em contato com o administrador.'];
            }
            if ($user['bloqueado']) {
                return ['status' => 'error', 'message' => 'Sua conta está bloqueada. Por favor, entre em contato com o administrador.'];
            }

            if (password_verify($password, $user['senha'])) {
                // VERIFICAÇÃO ADICIONADA: Se já houver uma sessão ativa, impede o login.
                if (self::hasActiveSession($pdo, $user['id'])) {
                    return ['status' => 'error', 'message' => 'Este usuário já está logado em outra máquina. Caso contrario em contato com o ADM ou retorne em 20 min.'];
                }

                self::resetLoginAttempts($pdo, $user['id']);

                // Busca os perfis do usuário e guarda na sessão.
                $stmt_perfis = $pdo->prepare("SELECT id_perfil FROM loginperfil WHERE id_login = :userId");
                $stmt_perfis->execute(['userId' => $user['id']]);
                $_SESSION['perfis'] = $stmt_perfis->fetchAll(PDO::FETCH_COLUMN);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['session_token'] = bin2hex(random_bytes(32));

                self::logUserAccess($pdo, $user['id'], 'login');
                self::activateSession($pdo, $user['id'], session_id());

                return ['status' => 'success'];
            } else {
                self::incrementLoginAttempts($pdo, $user['id']);

                $stmt = $pdo->prepare("SELECT tentativas FROM usuarios WHERE id = :id");
                $stmt->execute(['id' => $user['id']]);
                $attempts = $stmt->fetchColumn();

                $message = "Usuário ou senha incorretos.";
                if ($attempts >= MAX_LOGIN_ATTEMPTS) {
                    $message = "Sua conta foi bloqueada após várias tentativas falhas.";
                } else {
                    $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
                    $message .= " Tentativas restantes: {$remaining}";
                }

                return ['status' => 'error', 'message' => $message, 'attempts' => $attempts];
            }
        } catch (PDOException $e) {
            error_log("Erro de login: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro no sistema. Tente novamente.'];
        }
    }

    /**
     * Encerra a sessão atual e remove o registro do banco de dados.
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])) {
            try {
                $pdo = getDbConnection();
                $userId = $_SESSION['user_id'];
                $sessionId = session_id();

                // Remove a sessão ativa
                $stmt = $pdo->prepare("DELETE FROM sessoes_ativas WHERE id_usuario = :user_id AND session_id = :session_id");
                $stmt->execute([
                    'user_id' => $userId,
                    'session_id' => $sessionId
                ]);

                // Registra o logout
                self::logUserAccess($pdo, $userId, 'logout');
            } catch (PDOException $e) {
                error_log("Erro ao encerrar sessão: " . $e->getMessage());
            }
        }
    }

    public static function cleanupExpiredSessions()
    {
        try {
            $pdo = getDbConnection();

            // Limpa sessões expiradas (mais antigas que o tempo limite)
            $stmt = $pdo->prepare("DELETE FROM sessoes_ativas WHERE data_login < DATE_SUB(NOW(), INTERVAL :timeout MINUTE)");
            $stmt->execute(['timeout' => SESSION_IDLE_TIMEOUT_MINUTES_DEFAULT]);

            // Adiciona log das sessões removidas
            $removed = $stmt->rowCount();
            if ($removed > 0) {
                error_log("Limpeza de sessões: $removed sessões expiradas removidas");
            }
        } catch (PDOException $e) {
            error_log("Erro na limpeza de sessões: " . $e->getMessage());
        }
    }

    /**
     * Valida se a sessão atual é uma sessão ativa e legítima.
     * @return bool
     */
    public static function validateSession(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            return false;
        }

        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sessoes_ativas WHERE id_usuario = :user_id AND session_id = :session_id");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'session_id' => session_id()
            ]);

            // Se a sessão não for encontrada no banco, é inválida.
            if (!$stmt->fetchColumn()) {
                return false;
            }

            // ---- LÓGICA DE HORÁRIO INTEGRADA AQUI ----
            if (!self::isAccessTimeAllowed($_SESSION['user_id'])) {
                self::logout(); // Faz o logout completo
                session_destroy(); // Destrói a sessão
                return false; // Retorna falso para redirecionar
            }
            // ---- FIM DA LÓGICA DE HORÁRIO ----

            // Se chegou até aqui, a sessão é válida e o horário é permitido.
            return true;
        } catch (PDOException $e) {
            error_log("Erro na validação da sessão: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Nova função para verificar se o acesso é permitido no horário atual.
     * VERSÃO CORRIGIDA
     *
     * @param int $userId
     * @return bool
     */
    private static function isAccessTimeAllowed(int $userId): bool
    {
        try {
            $pdo = getDbConnection();
            $dias = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            $diaAtual = $dias[date('w')];
            $horaAtual = date('H:i:s');

            // Passo 1: Busca a regra (primeiro do usuário, depois do perfil)
            $stmt = $pdo->prepare("SELECT * FROM acesso_usuario_horario WHERE id_usuario = :userId AND dia_semana = :dia");
            $stmt->execute(['userId' => $userId, 'dia' => $diaAtual]);
            $regra = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$regra) {
                $stmt = $pdo->prepare("
                    SELECT aph.* FROM acesso_perfil_horario aph
                    JOIN loginperfil lp ON aph.id_perfil = lp.id_perfil
                    WHERE lp.id_login = :userId AND aph.dia_semana = :dia
                    LIMIT 1
                ");
                $stmt->execute(['userId' => $userId, 'dia' => $diaAtual]);
                $regra = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Se não encontrou NENHUMA regra para o dia, o acesso é permitido por padrão.
            if (!$regra) {
                return true;
            }

            // --- LÓGICA DE DECISÃO CORRIGIDA ---

            // Verifica se está dentro de algum dos turnos definidos
            $noIntervalo1 = ($regra['hora_inicio_1'] && $regra['hora_fim_1'] && $horaAtual >= $regra['hora_inicio_1'] && $horaAtual <= $regra['hora_fim_1']);
            $noIntervalo2 = ($regra['hora_inicio_2'] && $regra['hora_fim_2'] && $horaAtual >= $regra['hora_inicio_2'] && $horaAtual <= $regra['hora_fim_2']);

            // Se a hora atual está em qualquer um dos intervalos, permite o acesso.
            if ($noIntervalo1 || $noIntervalo2) {
                return true;
            }

            // Se NÃO está nos intervalos, verifica a regra do checkbox "Acesso Liberado"
            // Se a caixa está marcada E NENHUM horário foi definido, significa acesso o dia todo.
            if ($regra['acesso_liberado'] && !$regra['hora_inicio_1'] && !$regra['hora_inicio_2']) {
                return true;
            }

            // Se nenhuma das condições acima foi atendida, o acesso é negado.
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar horário de acesso: " . $e->getMessage());
            return false; // Em caso de erro, bloqueia por segurança.
        }
    }

    /**
     * Adicionada para verificar se já existe uma sessão ativa para o usuário.
     * @param PDO $pdo
     * @param int $userId
     * @return bool
     */
    private static function hasActiveSession(PDO $pdo, int $userId): bool
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sessoes_ativas WHERE id_usuario = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Incrementa o contador de tentativas de login falhas.
     * @param PDO $pdo
     * @param int $userId
     */
    private static function incrementLoginAttempts(PDO $pdo, int $userId)
    {
        $stmt = $pdo->prepare("UPDATE usuarios SET tentativas = tentativas + 1 WHERE id = :id");
        $stmt->execute(['id' => $userId]);

        $attempts = self::getLoginAttempts($pdo, $userId);

        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $stmt = $pdo->prepare("UPDATE usuarios SET bloqueado = 1 WHERE id = :id");
            $stmt->execute(['id' => $userId]);
        }
    }

    /**
     * Reseta o contador de tentativas de login.
     * @param PDO $pdo
     * @param int $userId
     */
    private static function resetLoginAttempts(PDO $pdo, int $userId)
    {
        $stmt = $pdo->prepare("UPDATE usuarios SET tentativas = 0 WHERE id = :id");
        $stmt->execute(['id' => $userId]);
    }

    /**
     * Registra o evento de login no banco de dados.
     * @param PDO $pdo
     * @param int $userId
     * @param string $eventType
     */
    private static function logUserAccess(PDO $pdo, int $userId, string $eventType)
    {
        $stmt = $pdo->prepare("INSERT INTO log_acesso_usuarios (id_usuario, tipo_evento, session_id_php, ip_acesso) VALUES (:id_usuario, :tipo_evento, :session_id_php, :ip_acesso)");
        $stmt->execute([
            'id_usuario' => $userId,
            'tipo_evento' => $eventType,
            'session_id_php' => session_id(),
            'ip_acesso' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    /**
     * Obtém o número de tentativas de login de um usuário.
     * @param PDO $pdo
     * @param int $userId
     * @return int
     */
    private static function getLoginAttempts(PDO $pdo, int $userId): int
    {
        $stmt = $pdo->prepare("SELECT tentativas FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Deleta qualquer sessão antiga do usuário na tabela.
     * @param PDO $pdo
     * @param int $userId
     */
    private static function deactivateOldSession(PDO $pdo, int $userId)
    {
        $stmt = $pdo->prepare("DELETE FROM sessoes_ativas WHERE id_usuario = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Registra a nova sessão na tabela de sessões ativas.
     * @param PDO $pdo
     * @param int $userId
     * @param string $sessionId
     */
    private static function activateSession(PDO $pdo, int $userId, string $sessionId)
    {
        $stmt = $pdo->prepare("INSERT INTO sessoes_ativas (id_usuario, session_id, ip, user_agent) VALUES (:id_usuario, :session_id, :ip, :user_agent)");
        $stmt->execute([
            'id_usuario' => $userId,
            'session_id' => $sessionId,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);
    }

    /**
     * Atualiza o timestamp da sessão ativa do usuário
     */
    public static function updateUserActivity(int $userId, string $sessionId): bool
    {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("UPDATE sessoes_ativas SET data_login = NOW() 
                              WHERE id_usuario = :user_id AND session_id = :session_id");
            return $stmt->execute([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar atividade do usuário: " . $e->getMessage());
            return false;
        }
    }
}
