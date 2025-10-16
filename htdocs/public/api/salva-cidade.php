<?php

// public/api/salva-cidade.php (NOVA VERSÃO)
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia as colunas 'id_estado' e 'cidade' para as chaves 'estado_id' e 'nome_cidade' do input
ApiController::handleDynamicSave('cidades', ['id_estado' => 'estado_id', 'cidade' => 'nome_cidade']);
