// public/dist/js/equipe-form.js (Versão com filtro corrigido e formato de nome)

document.addEventListener('DOMContentLoaded', function () {

    // 1. Armazena as opções originais dos membros para restaurar o filtro
    const membrosSelect = $('#membros_ids');
    const originalOptions = membrosSelect.html();

    // 2. Inicializa a biblioteca Select2
    $('#id_lider, #filtro_perfil').select2({
        theme: 'bootstrap4'
    });

    // 3. Lógica para filtrar a lista de membros por perfil
    const filtroPerfilSelect = $('#filtro_perfil');

    filtroPerfilSelect.on('change', function () {
        const perfilIdSelecionado = $(this).val();

        // Limpa as opções atuais (exceto as já selecionadas)
        const selecionados = membrosSelect.val();
        membrosSelect.html(originalOptions); // Restaura todas as opções
        membrosSelect.val(selecionados); // Mantém os que já estavam selecionados

        if (perfilIdSelecionado !== 'todos') {
            // Remove as opções que não correspondem ao perfil selecionado
            membrosSelect.find('option').each(function () {
                const $option = $(this);
                // Não remove as que já estão selecionadas
                if (!$option.is(':selected')) {
                    const perfisDoUsuario = $option.data('perfis')?.toString().split(',') || [];
                    if (!perfisDoUsuario.includes(perfilIdSelecionado)) {
                        $option.remove();
                    }
                }
            });
        }

        // Força o Select2 a re-renderizar a lista com as opções atualizadas
        membrosSelect.trigger('change.select2');
    });

    // 4. Lógica para impedir dupla atribuição (nomes a vermelho)
    const dadosJS = JSON.parse(document.getElementById('dados-para-js').textContent);
    const usuariosJaEmEquipe = dadosJS.usuariosJaEmEquipe.map(String);

    function formatarOpcaoComCor(option) {
        if (!option.id) {
            return option.text;
        }
        const usuarioId = option.id;

        if (usuariosJaEmEquipe.includes(usuarioId)) {
            // Usa jQuery para criar um elemento span com a cor
            return $('<span>').css('color', '#dc3545').text(option.text);
        }
        return option.text;
    }

    function colorirMembrosJaEmEquipe() {
        $('#membros_ids').next('.select2-container').find('.select2-selection__choice').each(function () {
            const $choice = $(this);
            const choiceTitle = $choice.attr('title');

            const originalOption = $('#membros_ids option').filter(function () {
                return $(this).text() === choiceTitle;
            });

            const usuarioId = originalOption.val();
            if (usuariosJaEmEquipe.includes(usuarioId)) {
                $choice.css('background-color', '#dc3545').css('border-color', '#dc3545').css('color', 'white');
            }
        });
    }

    membrosSelect.select2({
        theme: 'bootstrap4',
        templateResult: formatarOpcaoComCor, // Formata as opções na lista suspensa
        templateSelection: formatarOpcaoComCor // Formata as opções já selecionadas
    }).on('change', colorirMembrosJaEmEquipe);

    // Dispara a coloração inicial e o filtro
    colorirMembrosJaEmEquipe();
    filtroPerfilSelect.trigger('change');
});