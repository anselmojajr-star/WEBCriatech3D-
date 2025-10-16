// public/dist/js/estrutura-form.js (Versão Final e Corrigida)

document.addEventListener('DOMContentLoaded', () => {
    // --- LÓGICA PARA MATERIAIS ---
    const materialSearchInput = document.getElementById('material-search');
    const matSearchResults = document.getElementById('material-search-results');
    const matTableBody = document.getElementById('composicao-materiais-table-body');

    // URL base para a busca de materiais
    const materiaisApiUrl = '/public/api/estruturas/search-materiais'; // << LINHA CORRIGIDA
    setupSearch(materialSearchInput, matSearchResults, materiaisApiUrl, addMaterialToTable);

    function addMaterialToTable(material) {
        if (document.querySelector(`input[name="materiais[${material.id}][id]"]`)) {
            alert('Este material já foi adicionado.'); return;
        }
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="hidden" name="materiais[${material.id}][id]" value="${material.id}">${material.codigoMaterial}</td>
            <td>${material.material}</td>
            <td><input type="number" name="materiais[${material.id}][qtd]" class="form-control" value="1" step="any" required></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash"></i></button></td>
        `;
        matTableBody.appendChild(newRow);
    }

    // --- LÓGICA PARA SUBESTRUTURAS ---
    const subestruturaSearchInput = document.getElementById('subestrutura-search');
    const subSearchResults = document.getElementById('subestrutura-search-results');
    const subTableBody = document.getElementById('composicao-subestruturas-table-body');
    const estruturaIdField = document.querySelector('input[name="id"]');
    const currentEstruturaId = estruturaIdField ? estruturaIdField.value : 0;

    // URL base para a busca de subestruturas
    const estruturasApiUrl = `/public/api/estruturas/search-estruturas?exclude_id=${currentEstruturaId}`; // << LINHA CORRIGIDA
    setupSearch(subestruturaSearchInput, subSearchResults, estruturasApiUrl, addSubestruturaToTable);

    function addSubestruturaToTable(estrutura) {
        if (document.querySelector(`input[name="subestruturas[${estrutura.id}][id]"]`)) {
            alert('Esta subestrutura já foi adicionada.'); return;
        }
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="hidden" name="subestruturas[${estrutura.id}][id]" value="${estrutura.id}">${estrutura.codigo}</td>
            <td>${estrutura.nome}</td>
            <td><input type="number" name="subestruturas[${estrutura.id}][qtd]" class="form-control" value="1" step="1" required></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash"></i></button></td>
        `;
        subTableBody.appendChild(newRow);
    }

    // --- FUNÇÃO GENÉRICA DE BUSCA (sem alterações) ---
    function setupSearch(input, resultsContainer, apiUrl, addCallback) {
        let timer;
        if (!input) return;
        input.addEventListener('input', () => {
            const term = input.value.trim();
            clearTimeout(timer);
            if (term.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }
            timer = setTimeout(() => {
                const separator = apiUrl.includes('?') ? '&' : '?';
                const finalUrl = `${apiUrl}${separator}term=${encodeURIComponent(term)}`;
                fetch(finalUrl)
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const resultItem = document.createElement('a');
                                resultItem.href = '#';
                                resultItem.className = 'list-group-item list-group-item-action';
                                resultItem.textContent = `[${item.codigo || item.codigoMaterial}] ${item.nome || item.material}`;
                                resultItem.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    addCallback(item);
                                    input.value = '';
                                    resultsContainer.innerHTML = '';
                                });
                                resultsContainer.appendChild(resultItem);
                            });
                        } else {
                            resultsContainer.innerHTML = '<span class="list-group-item">Nenhum item encontrado.</span>';
                        }
                    }).catch(err => {
                        console.error("Erro na busca:", err);
                        resultsContainer.innerHTML = '<span class="list-group-item text-danger">Erro na busca.</span>';
                    });
            }, 300);
        });
    }

    // Listener genérico para botões de remover
    document.getElementById('estrutura-tabs-content').addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-item')) {
            e.target.closest('tr').remove();
        }
    });
});