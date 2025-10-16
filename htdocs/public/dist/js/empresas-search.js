document.addEventListener('DOMContentLoaded', () => {
    // O terceiro parâmetro foi atualizado para a nova rota da API
    setupLiveSearch(
        'search',
        'empresas-table-body',
        '/public/api/empresas/search', // <-- CORREÇÃO ESTÁ AQUI
        5 // Número de colunas na tabela
    );
});