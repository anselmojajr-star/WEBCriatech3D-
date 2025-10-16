// HTDOCS/public/dist/js/materiais-search.js

document.addEventListener('DOMContentLoaded', () => {
    // Apenas uma linha para configurar tudo!
    setupLiveSearch('search', 'materiais-table-body', '/public/api/materiais/search', 4);
});
