// public/dist/js/estruturas-search.js
document.addEventListener('DOMContentLoaded', () => {
    // Corrigindo o ID da tabela e a rota da API para buscar ESTRUTURAS.
    setupLiveSearch(
        'search',
        'estruturas-table-body', // <-- CORREÇÃO 1: Apontar para a tabela de estruturas
        '/public/api/estruturas/search', // <-- CORREÇÃO 2: Usar a API de estruturas
        4 // <-- CORREÇÃO 3: A tabela de estruturas tem 4 colunas
    );
});