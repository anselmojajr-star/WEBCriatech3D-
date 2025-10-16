<?php
// HTDOCS/security/session_manager.php

//require_once __DIR__ . '/AuthManager.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Inicia a sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Executa a limpeza de sessões expiradas a cada requisição
AuthManager::cleanupExpiredSessions();

// Se o usuário está logado, atualiza a atividade dele
if (isset($_SESSION['user_id']) && isset($_SESSION['session_token'])) {
    AuthManager::updateUserActivity($_SESSION['user_id'], session_id());

    // Verifica se a sessão ainda é válida
    if (!AuthManager::validateSession()) {
        // Sessão inválida - força logout
        AuthManager::logout();
        session_destroy();
        //header("Location: login.php");
        //exit;
    }
}
