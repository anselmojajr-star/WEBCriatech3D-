document.addEventListener('DOMContentLoaded', () => {
    // A tabela de projetos tem 6 colunas
    setupLiveSearch('search', 'projetos-table-body', '/public/api/projetos/search', 6);
});
