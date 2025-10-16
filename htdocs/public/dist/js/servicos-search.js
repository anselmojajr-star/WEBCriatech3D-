document.addEventListener('DOMContentLoaded', () => {
    // A tabela de serviços tem 4 colunas
    setupLiveSearch('search', 'servicos-table-body', '/public/api/servicos/search', 4);
});
