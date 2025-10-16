document.addEventListener('DOMContentLoaded', () => {
    // A tabela de sessões tem 4 colunas
    setupLiveSearch('search-session', 'sessoes-table-body', 'get_sessoes_ajax.php', 4);
});
