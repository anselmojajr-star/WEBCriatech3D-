<?php

require_once __DIR__ . '/../../config/db.php';
//require_once __DIR__ . '/../../controllers/ApiController.php'; // Adicione esta linha
require_once __DIR__ . '/../../vendor/autoload.php';

// Validação de nomes reservados (adicionar antes de chamar handleDynamicSave)
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$perfilNome = trim($data['perfil_nome'] ?? '');

if (in_array(strtolower($perfilNome), ['adm', 'gerente de permissões'])) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Este nome de perfil é reservado e não pode ser criado.']);
    exit;
}

// Corrija o mapeamento: [coluna_db => chave_input]
ApiController::handleDynamicSave('perfil', ['perfil' => 'perfil_nome'], 'perfil_nome');
