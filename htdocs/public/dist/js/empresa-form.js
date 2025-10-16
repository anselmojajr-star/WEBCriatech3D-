document.addEventListener('DOMContentLoaded', function () {
    // --- Lógica para manter a aba ativa após salvar ---
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        $('#empresaTabs a[href="#' + activeTab + '"]').tab('show');
    }

    // --- Lógica do Endereço (Estado/Cidade + Modal) ---
    const estadoSelect = document.getElementById('estado_select');
    const cidadeSelect = document.getElementById('cidade_select');
    const btnNovaCidade = document.getElementById('btnNovaCidade'); // Garante que o botão seja encontrado

    if (estadoSelect && cidadeSelect) {
        function carregarCidades(estadoId, cidadeParaSelecionar = null) {
            const cidadeAtualId = cidadeParaSelecionar || cidadeSelect.dataset.cidadeAtualId || '';

            // --- CORREÇÃO PRINCIPAL ESTÁ AQUI ---
            // Esta linha garante que o botão '+' seja ativado se um estado for selecionado (estadoId != null/vazio)
            // e desativado caso contrário.
            if (btnNovaCidade) {
                btnNovaCidade.disabled = !estadoId;
            }

            if (!estadoId) {
                cidadeSelect.innerHTML = '<option value="">Selecione um Estado primeiro</option>';
                return;
            }

            fetch(`/public/api/get-cidades.php?estado_id=${estadoId}`)
                .then(response => response.json())
                .then(cidades => {
                    cidadeSelect.innerHTML = '<option value="">Selecione uma Cidade</option>';
                    cidades.forEach(cidade => {
                        const option = new Option(cidade.cidade, cidade.id);
                        if (cidade.id == cidadeAtualId) {
                            option.selected = true;
                        }
                        cidadeSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Falha ao buscar cidades:", error);
                    cidadeSelect.innerHTML = '<option value="">Erro ao carregar cidades</option>';
                });
        }

        estadoSelect.addEventListener('change', function () {
            cidadeSelect.dataset.cidadeAtualId = '';
            carregarCidades(this.value);
        });

        if (estadoSelect.value) {
            carregarCidades(estadoSelect.value);
        }

        const btnSalvarNovaCidade = document.getElementById('btnSalvarNovaCidade');
        if (btnSalvarNovaCidade) {
            btnSalvarNovaCidade.addEventListener('click', function () {
                const nomeCidade = document.getElementById('inputNomeNovaCidade').value.trim();
                const estadoId = estadoSelect.value;
                const statusDiv = document.getElementById('novaCidadeStatus');

                if (!nomeCidade) {
                    statusDiv.innerHTML = '<div class="alert alert-warning">Por favor, insira um nome.</div>';
                    return;
                }
                statusDiv.innerHTML = 'Salvando...';

                fetch('/public/api/salva-cidade.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ estado_id: estadoId, nome_cidade: nomeCidade })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#modalNovaCidade').modal('hide');
                            document.getElementById('inputNomeNovaCidade').value = '';
                            statusDiv.innerHTML = '';
                            carregarCidades(estadoId, data.id);
                        } else {
                            statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    });
            });
        }
    }

    // --- Lógica do Modal de CNAE ---
    const btnSalvarNovoCnae = document.getElementById('btnSalvarNovoCnae');
    if (btnSalvarNovoCnae) {
        btnSalvarNovoCnae.addEventListener('click', function () {
            const cnaeCodigo = document.getElementById('inputNovoCnaeCodigo').value.trim();
            const cnaeDescricao = document.getElementById('inputNovoCnaeDescricao').value.trim();
            const statusDiv = document.getElementById('novoCnaeStatus');

            if (!cnaeCodigo || !cnaeDescricao) {
                statusDiv.innerHTML = '<div class="alert alert-warning">Código e Descrição são obrigatórios.</div>';
                return;
            }
            statusDiv.innerHTML = 'Salvando...';

            fetch('/public/api/salva-cnae.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cnae_codigo: cnaeCodigo, cnae_descricao: cnaeDescricao })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cnaeSelect = document.getElementById('cnae_select');
                        const newOption = new Option(`${data.cnae} - ${data.descricao}`, data.id, true, true);
                        cnaeSelect.appendChild(newOption);
                        $('#modalNovoCnae').modal('hide');
                        document.getElementById('inputNovoCnaeCodigo').value = '';
                        document.getElementById('inputNovoCnaeDescricao').value = '';
                        statusDiv.innerHTML = '';
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                });
        });
    }

    // --- Lógica do Modal de Setor Econômico ---
    const btnSalvarNovoSetor = document.getElementById('btnSalvarNovoSetor');
    if (btnSalvarNovoSetor) {
        btnSalvarNovoSetor.addEventListener('click', function () {
            const setorNome = document.getElementById('inputNovoSetorNome').value.trim();
            const statusDiv = document.getElementById('novoSetorStatus');

            if (!setorNome) {
                statusDiv.innerHTML = '<div class="alert alert-warning">O nome do setor é obrigatório.</div>';
                return;
            }
            statusDiv.innerHTML = 'Salvando...';

            fetch('/public/api/salva-setor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ setor_nome: setorNome })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const setorSelect = document.getElementById('setor_select');
                        const newOption = new Option(data.setor, data.id, true, true);
                        setorSelect.appendChild(newOption);
                        $('#modalNovoSetor').modal('hide');
                        document.getElementById('inputNovoSetorNome').value = '';
                        statusDiv.innerHTML = '';
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                });
        });
    }

    // --- Lógica do Modal de Cargo --- (NOVO CÓDIGO)
    const btnSalvarNovoCargo = document.getElementById('btnSalvarNovoCargo');
    if (btnSalvarNovoCargo) {
        btnSalvarNovoCargo.addEventListener('click', function () {
            const cargoNome = document.getElementById('inputNovoCargoNome').value.trim();
            const statusDiv = document.getElementById('novoCargoStatus');

            if (!cargoNome) {
                statusDiv.innerHTML = '<div class="alert alert-warning">O nome do cargo é obrigatório.</div>';
                return;
            }
            statusDiv.innerHTML = 'Salvando...';

            fetch('/public/api/salva-cargo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cargo_nome: cargoNome })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cargoSelect = document.getElementById('cargo_select');
                        // Cria a nova opção, seleciona e a adiciona na lista
                        const newOption = new Option(data.cargo, data.id, true, true);
                        cargoSelect.appendChild(newOption);

                        // Fecha o modal e limpa os campos
                        $('#modalNovoCargo').modal('hide');
                        document.getElementById('inputNovoCargoNome').value = '';
                        statusDiv.innerHTML = '';
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error("Falha ao salvar cargo:", error);
                    statusDiv.innerHTML = '<div class="alert alert-danger">Ocorreu um erro de comunicação.</div>';
                });
        });
    }
});