// public/dist/js/app-utils.js

/**
 * Configura uma busca em tempo real (live search) para uma tabela.
 * @param {string} searchInputId - O ID do campo de input da busca.
 * @param {string} tableBodyId - O ID do <tbody> da tabela a ser atualizado.
 * @param {string} endpointUrl - A URL da API AJAX que retorna o HTML das linhas.
 * @param {number} colspan - O número de colunas da tabela para a mensagem de "Carregando...".
 */
function setupLiveSearch(searchInputId, tableBodyId, endpointUrl, colspan) {
    const searchInput = document.getElementById(searchInputId);
    const tableBody = document.getElementById(tableBodyId);
    let debounceTimer;

    // Se os elementos não existirem na página, não faz nada.
    if (!searchInput || !tableBody) {
        return;
    }

    const loadResults = (searchTerm = '') => {
        // Mensagem de carregamento
        tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center">Carregando...</td></tr>`;

        // Busca os dados na API
        fetch(`${endpointUrl}?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.ok ? response.text() : Promise.reject('Erro de rede'))
            .then(html => {
                tableBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Erro ao buscar dados:', error);
                tableBody.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-danger">Erro ao carregar dados.</td></tr>`;
            });
    };

    // Adiciona o "escutador" de eventos no campo de busca
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadResults(searchInput.value.trim());
        }, 300); // Atraso de 300ms (debounce)
    });

    // Carga inicial dos dados
    loadResults();
}