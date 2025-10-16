<?php
// public/api/salva-cnae.php (NOVA VERSÃO)
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia 'cnae' e 'descricao' para as chaves do input e verifica duplicidade pelo código
ApiController::handleDynamicSave(
    'cnae',
    ['cnae' => 'cnae_codigo', 'descricao' => 'cnae_descricao'],
    'cnae_codigo'
);
