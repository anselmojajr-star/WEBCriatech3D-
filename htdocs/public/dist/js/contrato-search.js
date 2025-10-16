document.addEventListener('DOMContentLoaded', () => {
    // A tabela de contratos tem 7 colunas
    setupLiveSearch('search', 'contratos-table-body', '/public/api/contratos/search', 7);
});
