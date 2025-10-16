<?php

// public/api/salva-tipo-equipe.php (NOVA VERSÃO)
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia a coluna 'tipo_equipe' e verifica duplicidade
ApiController::handleDynamicSave('tipo_equipes', ['tipo_equipe' => 'tipo_equipe'], 'tipo_equipe');
