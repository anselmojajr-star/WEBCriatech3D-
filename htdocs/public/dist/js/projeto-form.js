// public/dist/js/projeto-form.js (Versão Final com Feedback em Tempo Real)

let map;
let markers = [];
let activeMarker = null;
let modalWasSaved = false;

// ... (referências aos elementos do modal e painel de projeção permanecem as mesmas)
const componenteModal = $('#modal-config-componente');
const modalTitleId = document.getElementById('modal-componente-id');
const estruturaSearchInput = document.getElementById('estrutura-search-input');
const estruturaSearchResults = document.getElementById('estrutura-search-results');
const componenteEstruturasTable = document.getElementById('componente-estruturas-table');
const materialSearchInput = document.getElementById('material-search-input');
const materialSearchResults = document.getElementById('material-search-results');
const componenteMateriaisTable = document.getElementById('componente-materiais-table');
const btnSalvarComponenteModal = document.getElementById('btn-salvar-componente-modal');
const btnRemoverComponente = document.getElementById('btn-remover-componente');
const btnProjetarPonto = document.getElementById('btn-projetar-ponto');

async function initMap() {
    // ... (função initMap não muda)
    const mapDiv = document.getElementById('map');
    if (!mapDiv) return;

    const componentesData = JSON.parse(mapDiv.dataset.componentes || '[]');
    const initialCoords = componentesData.length > 0
        ? { lat: parseFloat(componentesData[0].latitude), lng: parseFloat(componentesData[0].longitude) }
        : { lat: -14.235004, lng: -51.92528 };

    map = new google.maps.Map(mapDiv, {
        center: initialCoords,
        zoom: componentesData.length > 0 ? 15 : 4
    });

    map.addListener('click', (event) => {
        addMarker(event.latLng);
    });

    componentesData.forEach(componente => {
        const position = { lat: parseFloat(componente.latitude), lng: parseFloat(componente.longitude) };
        addMarker(position, componente, false);
    });

    redrawAllPolylines();
}

function addMarker(position, componente = null, openModal = true) {
    const isNew = !componente;
    const marker = new google.maps.Marker({
        position: position,
        map: map,
        draggable: true
    });

    marker.componentData = componente || {
        id: `new_${new Date().getTime()}`,
        latitude: position.lat(),
        longitude: position.lng(),
        status: 'planejado',
        estruturas: [],
        materiais_avulsos: [],
        conecta_com_anterior: 1
    };

    marker.polylines = [];
    markers.push(marker);
    createOrUpdateHiddenInputs(marker);

    marker.addListener('click', () => openComponenteModal(marker));
    marker.addListener('drag', (event) => { updateDraggedLineInfo(marker); });
    marker.addListener('dragend', (event) => {
        marker.componentData.latitude = event.latLng.lat();
        marker.componentData.longitude = event.latLng.lng();
        createOrUpdateHiddenInputs(marker);
        redrawAllPolylines();
    });

    redrawAllPolylines();
    if (openModal) openComponenteModal(marker);
    return marker;
}

// >>> NOVA FUNÇÃO: Atualiza a linha e o rótulo durante o arrasto
function updateDraggedLineInfo(draggedMarker) {
    const markerIndex = markers.findIndex(m => m === draggedMarker);

    // Atualiza a linha que vem ANTES do marcador arrastado
    if (markerIndex > 0) {
        const prevMarker = markers[markerIndex - 1];
        const lineInfo = draggedMarker.polylines.find(p => p.type === 'incoming');
        if (lineInfo) {
            lineInfo.polyline.setPath([prevMarker.getPosition(), draggedMarker.getPosition()]);

            const distance = google.maps.geometry.spherical.computeDistanceBetween(prevMarker.getPosition(), draggedMarker.getPosition());
            const heading = google.maps.geometry.spherical.computeHeading(prevMarker.getPosition(), draggedMarker.getPosition());
            const headingPositive = heading < 0 ? heading + 360 : heading;

            const newLabelText = `${distance.toFixed(1)} m / ${headingPositive.toFixed(1)}°`;
            const midPoint = google.maps.geometry.spherical.interpolate(prevMarker.getPosition(), draggedMarker.getPosition(), 0.5);

            lineInfo.labelMarker.setPosition(midPoint);
            lineInfo.labelMarker.setLabel({ text: newLabelText, color: '#FFFFFF', fontSize: '12px', fontWeight: 'bold' });
        }
    }

    // Atualiza a linha que vai DEPOIS do marcador arrastado
    if (markerIndex < markers.length - 1) {
        const nextMarker = markers[markerIndex + 1];
        const lineInfo = draggedMarker.polylines.find(p => p.type === 'outgoing');
        if (lineInfo) {
            lineInfo.polyline.setPath([draggedMarker.getPosition(), nextMarker.getPosition()]);

            const distance = google.maps.geometry.spherical.computeDistanceBetween(draggedMarker.getPosition(), nextMarker.getPosition());
            const heading = google.maps.geometry.spherical.computeHeading(draggedMarker.getPosition(), nextMarker.getPosition());
            const headingPositive = heading < 0 ? heading + 360 : heading;

            const newLabelText = `${distance.toFixed(1)} m / ${headingPositive.toFixed(1)}°`;
            const midPoint = google.maps.geometry.spherical.interpolate(draggedMarker.getPosition(), nextMarker.getPosition(), 0.5);

            lineInfo.labelMarker.setPosition(midPoint);
            lineInfo.labelMarker.setLabel({ text: newLabelText, color: '#FFFFFF', fontSize: '12px', fontWeight: 'bold' });
        }
    }
}


function removeMarker(markerId) {
    // ... (função não muda)
    const markerIndex = markers.findIndex(m => m.componentData.id == markerId);
    if (markerIndex === -1) return;
    const markerToRemove = markers[markerIndex];
    markerToRemove.setMap(null);
    const oldDiv = document.getElementById(`marker-inputs-${markerId}`);
    if (oldDiv) oldDiv.remove();
    markers.splice(markerIndex, 1);
    redrawAllPolylines();
    componenteModal.modal('hide');
}

function drawPolyline(startMarker, endMarker) {
    // ... (função não muda, mas vamos adicionar o ângulo ao texto final)
    const polyline = new google.maps.Polyline({
        path: [startMarker.getPosition(), endMarker.getPosition()],
        geodesic: true,
        strokeColor: '#0000FF',
        strokeOpacity: 0.8,
        strokeWeight: 4,
        map: map
    });

    // >>> ALTERAÇÃO PRINCIPAL AQUI: Adicionando o listener de clique na linha <<<
    polyline.addListener('click', () => {
        if (confirm('Deseja remover esta conexão? (Os pontos permanecerão no mapa)')) {
            // Encontra o marcador final desta linha e define que ele não se conecta mais
            endMarker.componentData.conecta_com_anterior = 0;
            // Atualiza o input oculto para que a alteração seja salva
            createOrUpdateHiddenInputs(endMarker);
            // Redesenha todas as linhas para que esta desapareça
            redrawAllPolylines();
        }
    });
    const distance = google.maps.geometry.spherical.computeDistanceBetween(startMarker.getPosition(), endMarker.getPosition());
    const heading = google.maps.geometry.spherical.computeHeading(startMarker.getPosition(), endMarker.getPosition());
    const headingPositive = heading < 0 ? heading + 360 : heading; // Garante que o ângulo seja sempre positivo

    const distanceText = `${distance.toFixed(1)} m / ${headingPositive.toFixed(1)}°`;

    const midPoint = google.maps.geometry.spherical.interpolate(startMarker.getPosition(), endMarker.getPosition(), 0.5);
    const labelMarker = new google.maps.Marker({
        position: midPoint,
        map: map,
        label: { text: distanceText, color: '#FFFFFF', fontSize: '12px', fontWeight: 'bold' },
        icon: { path: 'M -10,0 a 10,10 0 1,0 20,0 a 10,10 0 1,0 -20,0', fillColor: '#0000FF', fillOpacity: 1, strokeWeight: 0, scale: 1.5 }
    });

    // Adicionamos um 'tipo' para saber se a linha está "a sair" ou "a entrar" no marcador
    startMarker.polylines.push({ polyline, labelMarker, type: 'outgoing' });
    endMarker.polylines.push({ polyline, labelMarker, type: 'incoming' });
}

function redrawAllPolylines() {
    // ... (função não muda)
    markers.forEach(marker => {
        marker.polylines.forEach(line => {
            line.polyline.setMap(null);
            line.labelMarker.setMap(null);
        });
        marker.polylines = [];
    });
    for (let i = 0; i < markers.length - 1; i++) {
        const startMarker = markers[i];
        const endMarker = markers[i + 1];

        // A linha só é desenhada se o marcador final tiver a flag "conecta_com_anterior" como 1
        if (endMarker.componentData.conecta_com_anterior == 1) {
            drawPolyline(startMarker, endMarker);
        }
    }
}

// ... (todas as outras funções como openComponenteModal, saveModalChanges, setupAutocomplete, etc., não precisam de ser alteradas e podem ser mantidas como estão no seu ficheiro atual)
function openComponenteModal(marker) {
    activeMarker = marker;
    modalWasSaved = false;
    modalTitleId.textContent = marker.componentData.id;
    renderModalContent(marker.componentData);
    componenteModal.modal('show');
}
function renderModalContent(data) {
    componenteEstruturasTable.innerHTML = '';
    componenteMateriaisTable.innerHTML = '';
    if (data.estruturas) data.estruturas.forEach(est => addEstruturaToModalTable({ ...est, id: est.id_estrutura }));
    if (data.materiais_avulsos) data.materiais_avulsos.forEach(mat => addMaterialToModalTable({ ...mat, id: mat.id_material }));
}
function addEstruturaToModalTable(estrutura) {
    const row = componenteEstruturasTable.insertRow();
    row.dataset.id = estrutura.id;
    row.innerHTML = `<td>${estrutura.codigo || ''}</td><td>${estrutura.nome || ''}</td><td><input type="number" class="form-control form-control-sm" value="${estrutura.quantidade || 1}" min="1"></td><td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash"></i></button></td>`;
}
function addMaterialToModalTable(material) {
    const row = componenteMateriaisTable.insertRow();
    row.dataset.id = material.id;
    row.innerHTML = `<td>${material.codigoMaterial || 'N/A'}</td><td>${material.material || material.descricao_material}</td><td><input type="number" class="form-control form-control-sm" value="${material.quantidade || 1.00}" step="any" min="0.01"></td><td><button type="button" class="btn btn-danger btn-sm btn-remove-item"><i class="fas fa-trash"></i></button></td>`;
}
function saveModalChanges() {
    if (!activeMarker) return;
    modalWasSaved = true;
    activeMarker.componentData.estruturas = [];
    activeMarker.componentData.materiais_avulsos = [];
    for (const row of componenteEstruturasTable.rows) activeMarker.componentData.estruturas.push({ id_estrutura: row.dataset.id, quantidade: row.querySelector('input[type="number"]').value, codigo: row.cells[0].textContent, nome: row.cells[1].textContent });
    for (const row of componenteMateriaisTable.rows) activeMarker.componentData.materiais_avulsos.push({ id_material: row.dataset.id, quantidade: row.querySelector('input[type="number"]').value, codigoMaterial: row.cells[0].textContent, material: row.cells[1].textContent });
    createOrUpdateHiddenInputs(activeMarker);
    componenteModal.modal('hide');
}
function createOrUpdateHiddenInputs(marker) {
    const container = document.getElementById('componentes-hidden-inputs');
    const id = marker.componentData.id;
    const oldDiv = document.getElementById(`marker-inputs-${id}`);
    if (oldDiv) oldDiv.remove();

    const markerDiv = document.createElement('div');
    markerDiv.id = `marker-inputs-${id}`;

    // >>> ALTERAÇÃO AQUI: Adicionando o novo campo ao formulário oculto <<<
    let inputsHTML = `
        <input type="hidden" name="componentes[${id}][latitude]" value="${marker.componentData.latitude}">
        <input type="hidden" name="componentes[${id}][longitude]" value="${marker.componentData.longitude}">
        <input type="hidden" name="componentes[${id}][status]" value="${marker.componentData.status}">
        <input type="hidden" name="componentes[${id}][conecta_com_anterior]" value="${marker.componentData.conecta_com_anterior}">
    `;

    marker.componentData.estruturas.forEach(item => { inputsHTML += `<input type="hidden" name="componentes[${id}][estruturas][${item.id_estrutura}]" value="${item.quantidade}">`; });
    marker.componentData.materiais_avulsos.forEach(item => { inputsHTML += `<input type="hidden" name="componentes[${id}][materiais_avulsos][${item.id_material}]" value="${item.quantidade}">`; });

    markerDiv.innerHTML = inputsHTML;
    container.appendChild(markerDiv);
}

function setupAutocomplete(input, resultsContainer, apiUrl, addCallback) {
    let timer;
    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(timer);
        resultsContainer.innerHTML = '';
        if (term.length < 2) return;
        timer = setTimeout(() => {
            const finalUrl = `${apiUrl}&term=${encodeURIComponent(term)}`;
            fetch(finalUrl).then(response => response.json()).then(data => {
                resultsContainer.innerHTML = '';
                data.slice(0, 5).forEach(item => {
                    const div = document.createElement('a');
                    div.href = '#';
                    div.className = 'list-group-item list-group-item-action';
                    div.textContent = `[${item.codigo || item.codigoMaterial}] ${item.nome || item.material}`;
                    div.onclick = (e) => { e.preventDefault(); addCallback(item); input.value = ''; resultsContainer.innerHTML = ''; };
                    resultsContainer.appendChild(div);
                });
            }).catch(error => console.error('Autocomplete fetch error:', error));
        }, 300);
    });
}
document.addEventListener('DOMContentLoaded', () => {
    btnSalvarComponenteModal.addEventListener('click', saveModalChanges);
    componenteModal.on('hide.bs.modal', function () {
        if (!modalWasSaved && activeMarker && activeMarker.componentData.id.startsWith('new_')) {
            removeMarker(activeMarker.componentData.id);
        }
        activeMarker = null;
    });
    if (btnRemoverComponente) {
        btnRemoverComponente.addEventListener('click', function () {
            if (activeMarker && confirm('Tem a certeza que deseja remover este marcador?')) {
                removeMarker(activeMarker.componentData.id);
            }
        });
    }
    if (btnProjetarPonto) {
        btnProjetarPonto.addEventListener('click', function () {
            const distancia = parseFloat(document.getElementById('proj-distancia').value);
            const direcaoInput = document.getElementById('proj-direcao').value;
            if (!distancia || markers.length === 0) {
                alert('Por favor, insira uma distância e certifique-se de que existe pelo menos um ponto no mapa.');
                return;
            }
            const ultimoMarcador = markers[markers.length - 1];
            let direcao = direcaoInput ? parseFloat(direcaoInput) : null;
            if (direcao === null && markers.length > 1) {
                const penultimoMarcador = markers[markers.length - 2];
                direcao = google.maps.geometry.spherical.computeHeading(penultimoMarcador.getPosition(), ultimoMarcador.getPosition());
            } else if (direcao === null) {
                alert('Para o primeiro ponto projetado, é necessário fornecer uma direção (ângulo).');
                return;
            }
            const novaPosicao = google.maps.geometry.spherical.computeOffset(ultimoMarcador.getPosition(), distancia, direcao);
            addMarker(novaPosicao);
        });
    }
    const mapDiv = document.getElementById('map');
    const servicoId = mapDiv ? mapDiv.dataset.servicoId : 0;
    const estruturasApiUrl = `/public/api/estruturas/search-estruturas?exclude_id=0`;
    setupAutocomplete(estruturaSearchInput, estruturaSearchResults, estruturasApiUrl, addEstruturaToModalTable);
    const materiaisApiUrl = `/public/api/estruturas/search-materiais?id_servico=${servicoId}`;
    setupAutocomplete(materialSearchInput, materialSearchResults, materiaisApiUrl, addMaterialToModalTable);
    componenteModal.on('click', '.btn-remove-item', function () { $(this).closest('tr').remove(); });
});

window.initMap = initMap;