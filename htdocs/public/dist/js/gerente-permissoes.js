// public/dist/js/gerente-permissoes.js (Versão Corrigida e Completa)

document.addEventListener('DOMContentLoaded', () => {
    // --- LÓGICA PARA A ABA DE AÇÕES (MARCAR TODAS) ---
    const marcarTodasAcoesCheckbox = document.getElementById('marcar-todas-acoes');
    if (marcarTodasAcoesCheckbox) {
        marcarTodasAcoesCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            document.querySelectorAll('.acao-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

    // --- LÓGICA PARA A ABA DE VISIBILIDADE (DEPENDÊNCIA PAI/FILHO) ---
    const parentCheckboxes = document.querySelectorAll('.permission-parent');
    const childCheckboxes = document.querySelectorAll('.permission-child');

    childCheckboxes.forEach(child => {
        child.addEventListener('change', function () {
            if (this.checked) {
                const parentId = this.dataset.parentId;
                const parentCheckbox = document.getElementById(parentId);
                if (parentCheckbox) {
                    parentCheckbox.checked = true;
                }
            }
        });
    });

    parentCheckboxes.forEach(parent => {
        parent.addEventListener('change', function () {
            if (!this.checked) {
                const parentId = this.id;
                document.querySelectorAll(`.permission-child[data-parent-id="${parentId}"]`).forEach(child => {
                    child.checked = false;
                });
            }
        });
    });

    // --- LÓGICA PARA O EFEITO ACORDEÃO (ÍCONES +/-) ---
    $('#accordionAcoes, #accordionVisualizacao, #accordionVisualizacaoUsuario, #accordionAcoesUsuario').on('show.bs.collapse hide.bs.collapse', function (e) {
        $(e.target).prev('.card-header').find('.expand-icon')
            .toggleClass('fa-plus fa-minus');
    });

    // --- LÓGICA PARA SELEÇÃO DINÂMICA (A PARTE QUE VAMOS COMPLETAR) ---
    const perfilSelectVis = document.getElementById('perfil_id_vis');
    const perfilSelectAcoes = document.getElementById('perfil_id_acao');
    const usuarioSelectVis = document.getElementById('usuario_id_vis');
    const usuarioSelectAcoes = document.getElementById('usuario_id_acao');

    function handleSelectChange(selectElement, paramName) {
        if (selectElement) {
            selectElement.addEventListener('change', function () {
                const selectedValue = this.value;
                const currentUrl = new URL(window.location);

                currentUrl.searchParams.delete('perfil_id');
                currentUrl.searchParams.delete('usuario_id');

                if (selectedValue) {
                    currentUrl.searchParams.set(paramName, selectedValue);
                }

                window.location.href = currentUrl.toString();
            });
        }
    }

    // >>>>> AS 4 LINHAS FALTANTES FORAM ADICIONADAS AQUI <<<<<
    handleSelectChange(perfilSelectVis, 'perfil_id');
    handleSelectChange(perfilSelectAcoes, 'perfil_id');
    handleSelectChange(usuarioSelectVis, 'usuario_id');
    handleSelectChange(usuarioSelectAcoes, 'usuario_id');
});