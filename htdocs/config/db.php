<?php
// HTDOCS/config/db.php

// Define as constantes de conexão com o banco de dados.
// É altamente recomendado carregar essas variáveis de ambiente
// em um ambiente de produção (usando um arquivo .env, por exemplo)
// para maior segurança e flexibilidade.

define('DB_HOST', 'XXXXXXXXXXXXXXxx');
define('DB_NAME', 'xxxxxxxxxxxx'); // Nome do seu banco de dados
define('DB_USER', 'xxxxxxxxxxxxxxxxx');       // Usuário do banco de dados
define('DB_PASS', 'XXXXXXXXXXXX');         // Senha do banco de dados
define('DB_CHARSET', 'utf8mb4');

/**
 * Retorna uma instância de PDO para conexão com o banco de dados.
 * @return PDO
 * @throws PDOException Se houver um erro na conexão.
 */
function getDbConnection(): PDO
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Em um ambiente de produção, logar o erro e não exibi-lo ao usuário.
        // Por exemplo: error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
        die("Erro de conexão com o banco de dados. Tente novamente mais tarde.");
    }
}
