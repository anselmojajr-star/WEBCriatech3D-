<?php
// public/api/salva-cargo.php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Apenas uma linha para executar toda a lógica!
ApiController::handleDynamicSave('cargo', ['cargo' => 'cargo_nome'], 'cargo_nome');
