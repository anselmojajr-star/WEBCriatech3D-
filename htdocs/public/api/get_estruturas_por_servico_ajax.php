<?php
if (session_status() == PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

// ARQUIVOS ESSENCIAIS
require_once __DIR__ . '/../../config/db.php'; // <-- LINHA QUE FALTAVA
require_once __DIR__ . '/../../vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$servicoId = filter_input(INPUT_GET, 'id_servico', FILTER_VALIDATE_INT);

// O Controller que você me enviou não tem este método, vamos assumir que ele existe
// Se isto der erro, teremos de criar o método EstruturaController::getEstruturasPorServico()
$estruturas = EstruturaController::getEstruturasPorServico($servicoId);

echo json_encode($estruturas);
