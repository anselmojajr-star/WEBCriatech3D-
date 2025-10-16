// public/dist/js/contrato-form.js (Versão Corrigida e Refatorada)
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar Select2, se disponível
    if (typeof $ !== 'undefined' && typeof $.fn.select2 === 'function') {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    }

    // --- LÓGICA DO MODAL DE SERVIÇO (Já estava correta) ---
    const btnSalvarServico = document.getElementById('btn-salvar-servico');
    if (btnSalvarServico) {
        btnSalvarServico.addEventListener('click', function () {
            const servicoNome = document.getElementById('novo_servico_nome').value.trim();
            const servicoPrefixo = document.getElementById('novo_servico_prefixo').value.trim();
            const statusDiv = document.getElementById('novoServicoStatus');

            if (!servicoNome || !servicoPrefixo) {
                statusDiv.innerHTML = '<div class="alert alert-warning">Nome e Prefixo são obrigatórios.</div>';
                return;
            }
            statusDiv.innerHTML = 'Salvando...';

            fetch('/public/api/salva-servico.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ servico_nome: servicoNome, servico_prefixo: servicoPrefixo })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const servicoSelect = document.getElementById('id_servico');
                        const newOption = new Option(data.nome, data.id, true, true);
                        servicoSelect.appendChild(newOption);
                        $(servicoSelect).trigger('change'); // Notifica o Select2 da mudança
                        $('#modal-servico').modal('hide');
                        document.getElementById('novo_servico_nome').value = '';
                        document.getElementById('novo_servico_prefixo').value = '';
                        statusDiv.innerHTML = '';
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                });
        });
    }

    // --- LÓGICA DO MODAL DE SETOR (CORRIGIDA) ---
    const btnSalvarSetor = document.getElementById('btn-salvar-setor');
    if (btnSalvarSetor) {
        btnSalvarSetor.addEventListener('click', function () {
            const setorNome = document.getElementById('novo_setor_nome').value.trim();
            if (!setorNome) { alert('O nome do setor é obrigatório.'); return; }

            fetch('/public/api/salva-orgao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ setor: setorNome })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const setorSelect = document.getElementById('id_setor');
                        // CORREÇÃO: Usando a chave correta 'setor' da resposta da API
                        const newOption = new Option(data.setor, data.id, true, true);
                        setorSelect.appendChild(newOption);
                        $(setorSelect).trigger('change');
                        $('#modal-setor').modal('hide');
                        document.getElementById('novo_setor_nome').value = '';
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
        });
    }

    // --- LÓGICA DO MODAL DE TIPO DE EQUIPE (CORRIGIDA) ---
    const btnSalvarTipoEquipe = document.getElementById('btn-salvar-tipo-equipe');
    if (btnSalvarTipoEquipe) {
        btnSalvarTipoEquipe.addEventListener('click', function () {
            const tipoEquipeNome = document.getElementById('novo_tipo_equipe_nome').value.trim();
            if (!tipoEquipeNome) { alert('O nome do tipo de equipe é obrigatório.'); return; }

            fetch('/public/api/salva-tipo-equipe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tipo_equipe: tipoEquipeNome })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza TODOS os selects de tipo de equipe na tabela
                        document.querySelectorAll('select[name*="[id_tipo_equipe]"]').forEach(select => {
                            const newOption = new Option(data.tipo_equipe, data.id);
                            select.appendChild(newOption);
                        });
                        // Adiciona na template para futuras linhas
                        const template = document.getElementById('equipe-options-template');
                        if (template) {
                            const newOptionTemplate = new Option(data.tipo_equipe, data.id);
                            template.appendChild(newOptionTemplate);
                        }

                        $('#modal-tipo-equipe').modal('hide');
                        document.getElementById('novo_tipo_equipe_nome').value = '';
                    } else {
                        alert('Erro: ' + data.message);
                    }
                });
        });
    }

    // --- LÓGICA DINÂMICA DE EQUIPES (Sem alterações) ---
    const addEquipeBtn = document.getElementById('btn-add-equipe');
    const equipesTableBody = document.getElementById('equipes-table-body');
    if (addEquipeBtn) {
        addEquipeBtn.addEventListener('click', () => {
            const newIndex = new Date().getTime();
            const newRow = document.createElement('tr');
            newRow.className = 'equipe-row';
            const tiposDeEquipeOptions = document.getElementById('equipe-options-template').innerHTML;

            newRow.innerHTML = `
                <td>
                    <div class="input-group">
                        <select name="equipes[${newIndex}][id_tipo_equipe]" class="form-control" required>${tiposDeEquipeOptions}</select>
                        <div class="input-group-append">
                             <button class="btn btn-outline-secondary btn-add-tipo-equipe" type="button" data-toggle="modal" data-target="#modal-tipo-equipe">+</button>
                        </div>
                    </div>
                </td>
                <td><input type="number" name="equipes[${newIndex}][quantidade]" class="form-control" value="1" required></td>
                <td><input type="text" name="equipes[${newIndex}][descricao]" class="form-control"></td>
                <td><input type="number" step="0.01" name="equipes[${newIndex}][valor]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove-equipe"><i class="fas fa-trash"></i></button></td>
            `;
            equipesTableBody.appendChild(newRow);
        });
    }

    if (equipesTableBody) {
        equipesTableBody.addEventListener('click', (e) => {
            if (e.target.closest('.btn-remove-equipe')) {
                e.target.closest('tr').remove();
            }
        });
    }
});