<?php
// HTDOCS/config/constants.php

define('BASE_PATH', '/public');
// --- Configurações de Segurança ---

// Número máximo de tentativas de login falhas antes de bloquear a conta
define('MAX_LOGIN_ATTEMPTS', 3);

// Tempo de bloqueio da conta em minutos após exceder as tentativas
define('BLOCK_TIME_MINUTES', 5);

// Tempo de ociosidade da sessão em minutos (antes de deslogar)
// O valor do banco de dados (tabela config_ociosidade) terá precedência se existir.
define('SESSION_IDLE_TIMEOUT_MINUTES_DEFAULT', 20);

// --- Outras Constantes (Exemplos) ---

// Nome do sistema para uso em títulos, e-mails, etc.
define('SYSTEM_NAME', 'Criatech3D System');

// Endereço de e-mail do administrador para contatos ou logs
define('ADMIN_EMAIL', 'admin@criatech3d.com.br');

// Caminho base para uploads de fotos de perfil de usuário
// DEVE ser relativo à pasta public (seu DocumentRoot)
define('UPLOAD_DIR_USERS_PHOTOS', 'uploads/users_photos/'); // Mantenha APENAS esta linha

// HTDOCS/config/constants.php

// ... (todas as suas outras constantes) ...

// --- Caminhos e URLs ---

// Obtém o protocolo (http ou https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";

// Obtém o nome do host (ex: localhost)
$host = $_SERVER['HTTP_HOST'];

// Define o caminho do diretório base
// Se o seu site estiver na raiz do htdocs, use '/'
// Se estiver em um subdiretório (ex: http://localhost/meu_projeto/public/), defina o caminho.
// Vamos assumir que sua pasta public é a raiz acessível.
define('BASE_URL', $protocol . '://' . $host . '/public/');
