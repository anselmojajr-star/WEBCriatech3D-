<?php

// public/api/salva-setor.php (NOVA VERSÃO)
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia a coluna 'setor' na tabela 'setor_economico' e verifica duplicidade
ApiController::handleDynamicSave('setor_economico', ['setor' => 'setor_nome'], 'setor_nome');
