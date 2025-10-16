<?php
// public/api/get-cidades.php
header('Content-Type: application/json');
//require_once __DIR__ . '/../../controllers/FuncionarioController.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

$estadoId = $_GET['estado_id'] ?? 0;

if (!$estadoId) {
    echo json_encode([]);
    exit;
}

$cidades = FuncionarioController::getCidadesPorEstado((int)$estadoId);
echo json_encode($cidades);
