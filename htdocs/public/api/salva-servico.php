<?php
// public/api/salva-servico.php (NOVO ARQUIVO)

// Carrega as dependências
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/db.php';

// Mapeia as colunas do banco para as chaves do input e salva os dados
ApiController::handleDynamicSave(
    'servicos',
    [
        'nome' => 'servico_nome',
        'prefixo_codigo' => 'servico_prefixo'
    ],
    'servico_nome' // Verifica duplicidade pelo nome do serviço
);
