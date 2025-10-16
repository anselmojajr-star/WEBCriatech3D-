// public/dist/js/equipes-search.js
document.addEventListener('DOMContentLoaded', () => {
    setupLiveSearch(
        'search',
        'equipes-table-body',
        '/public/api/equipes/search', // <<<---- CORREÇÃO AQUI
        4
    );
});