<?php

// public/api/salva-orgao.php (NOVA VERSÃO)
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia a coluna 'setor' para a chave 'setor' do input e verifica duplicidade
ApiController::handleDynamicSave('setor', ['setor' => 'setor'], 'setor');
