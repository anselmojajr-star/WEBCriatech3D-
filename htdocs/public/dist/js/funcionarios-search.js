document.addEventListener('DOMContentLoaded', () => {
    // O terceiro parâmetro DEVE ser a rota completa que definimos no index.php
    setupLiveSearch(
        'search',
        'funcionarios-table-body',
        '/public/api/funcionarios/search', // <-- Garanta que este é o caminho
        6
    );
});