// public/dist/js/funcionario-form.js

document.addEventListener('DOMContentLoaded', function () {

    // --- Lógica do Endereço (Estado/Cidade + Modal) --- (JÁ EXISTIA)
    const estadoSelect = document.getElementById('estado_select');
    const cidadeSelect = document.getElementById('cidade_select');

    if (estadoSelect && cidadeSelect) {
        const btnNovaCidade = document.getElementById('btnNovaCidade');

        function carregarCidades(estadoId, cidadeParaSelecionar = null) {
            const cidadeAtualId = cidadeParaSelecionar || cidadeSelect.dataset.cidadeAtualId || '';
            if (btnNovaCidade) btnNovaCidade.disabled = !estadoId;

            if (!estadoId) {
                cidadeSelect.innerHTML = '<option value="">Selecione um Estado primeiro</option>';
                return;
            }

            fetch(`/public/api/get-cidades.php?estado_id=${estadoId}`)
                .then(response => response.json())
                .then(cidades => {
                    cidadeSelect.innerHTML = '<option value="">Selecione uma Cidade</option>';
                    cidades.forEach(cidade => {
                        const option = document.createElement('option');
                        option.value = cidade.id;
                        option.textContent = cidade.cidade;
                        if (cidade.id == cidadeAtualId) {
                            option.selected = true;
                        }
                        cidadeSelect.appendChild(option);
                    });
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

    // --- Lógica do Modal de Perfil --- (JÁ EXISTIA)
    const btnSalvarNovoPerfil = document.getElementById('btnSalvarNovoPerfil');
    if (btnSalvarNovoPerfil) {
        btnSalvarNovoPerfil.addEventListener('click', function () {
            const perfilNome = document.getElementById('inputNovoPerfilNome').value.trim();
            const statusDiv = document.getElementById('novoPerfilStatus');

            if (!perfilNome) {
                statusDiv.innerHTML = '<div class="alert alert-warning">O nome do perfil é obrigatório.</div>';
                return;
            }
            statusDiv.innerHTML = 'Salvando...';

            fetch('/public/api/salva-perfil.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ perfil_nome: perfilNome })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const perfilSelect = document.getElementById('perfil_select');
                        const newOption = new Option(data.perfil, data.id, true, true);
                        perfilSelect.appendChild(newOption);
                        $('#modalNovoPerfil').modal('hide');
                        document.getElementById('inputNovoPerfilNome').value = '';
                        statusDiv.innerHTML = '';
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                });
        });
    }

    // --- Lógica para Pré-visualização da Foto de Perfil --- (JÁ EXISTIA E ESTÁ CORRETA)
    const fotoInput = document.getElementById('foto-input');
    const imgPreview = document.getElementById('img-preview');

    if (fotoInput && imgPreview) {
        fotoInput.addEventListener('change', function (event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }

    // =====================================================================
    // ===== CÓDIGO NOVO PARA CORRIGIR O BOTÃO "SALVAR ENDEREÇO" =========
    // =====================================================================
    const btnSalvarEndereco = document.getElementById('btn-salvar-endereco');

    if (btnSalvarEndereco) {
        btnSalvarEndereco.addEventListener('click', function () {
            const formEndereco = document.getElementById('form-endereco');
            if (formEndereco) {
                formEndereco.submit();
            }
        });
    }
});