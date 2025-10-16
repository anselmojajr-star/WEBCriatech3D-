<p>Selecione um usuário para editar ou sobrepor as permissões de visibilidade de módulos que ele herda do perfil.</p>
<!-- <form method="GET" action="gerente.php"> -->
<form method="GET" action="<?= BASE_PATH ?>/gerente">
    <input type="hidden" name="tab" value="visualizacao_usuario">
    <div class="form-group row">
        <label for="usuario_id_vis" class="col-sm-2 col-form-label">Usuário:</label>
        <div class="col-sm-8">
            <select name="usuario_id" id="usuario_id_vis" class="form-control">
                <option value="">-- Selecione um usuário --</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id'] ?>" <?= ($usuarioSelecionadoId == $usuario['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($usuario['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>

<?php if ($usuarioSelecionadoId): ?>
    <hr>
    <h4>Editando visibilidade para: <strong><?= htmlspecialchars(array_column($usuarios, 'username', 'id')[$usuarioSelecionadoId]); ?></strong></h4>
    <p class="text-muted small">Defina regras específicas para este usuário. Regras com data de validade são ideais para permissões temporárias.</p>

    <form action="<?= BASE_PATH ?>/gerente/salvar-visibilidade-usuario" method="POST">
        <input type="hidden" name="action" value="salvar_visualizacao_usuario">
        <input type="hidden" name="usuario_id" value="<?= $usuarioSelecionadoId ?>">

        <div id="accordionVisualizacaoUsuario" class="mt-3">
            <?php
            $menuHierarquicoVisUsuario = GerenteController::organizarModulosHierarquicamente($modulosGerenciaveis);

            foreach ($menuHierarquicoVisUsuario as $menuPrincipal):
                $permissao = $permissoesUsuarioAtuais[$menuPrincipal['id']] ?? null;
            ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header" id="heading-vis-user-<?= $menuPrincipal['id'] ?>">
                        <h5 class="mb-0">
                            <a href="#" class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapse-vis-user-<?= $menuPrincipal['id'] ?>">
                                <i class="fas fa-plus mr-2 expand-icon"></i>
                                <?= htmlspecialchars($menuPrincipal['nome']) ?>
                            </a>
                        </h5>
                    </div>

                    <div id="collapse-vis-user-<?= $menuPrincipal['id'] ?>" class="collapse" data-parent="#accordionVisualizacaoUsuario">
                        <div class="card-body">
                            <div class="p-2">
                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $menuPrincipal['id'] ?>][status]" value="padrao" id="vis_user_padrao_<?= $menuPrincipal['id'] ?>" <?= !$permissao ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_padrao_<?= $menuPrincipal['id'] ?>">Padrão do Perfil</label></div>
                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $menuPrincipal['id'] ?>][status]" value="permitir" id="vis_user_permitir_<?= $menuPrincipal['id'] ?>" <?= ($permissao['permitido'] ?? 0) == 1 ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_permitir_<?= $menuPrincipal['id'] ?>">Sempre Permitir</label></div>
                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $menuPrincipal['id'] ?>][status]" value="negar" id="vis_user_negar_<?= $menuPrincipal['id'] ?>" <?= isset($permissao['permitido']) && $permissao['permitido'] == 0 ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_negar_<?= $menuPrincipal['id'] ?>">Sempre Negar</label></div>
                                <hr>
                                <div class="form-group"><label class="small">Início da Validade (opcional)</label><input type="date" class="form-control form-control-sm" name="permissoes[<?= $menuPrincipal['id'] ?>][data_inicio]" value="<?= $permissao['data_inicio_validade'] ?? '' ?>"></div>
                                <div class="form-group"><label class="small">Fim da Validade (opcional)</label><input type="date" class="form-control form-control-sm" name="permissoes[<?= $menuPrincipal['id'] ?>][data_fim]" value="<?= $permissao['data_fim_validade'] ?? '' ?>"></div>
                            </div>

                            <?php if (!empty($menuPrincipal['submenus'])): ?>
                                <hr class="mt-4">
                                <div class="ml-4">
                                    <?php foreach ($menuPrincipal['submenus'] as $submenu):
                                        $permissaoSub = $permissoesUsuarioAtuais[$submenu['id']] ?? null;
                                    ?>
                                        <div class="card card-outline card-secondary mb-3">
                                            <div class="card-header bg-light py-2">
                                                <p class="card-title text-dark mb-0"><?= htmlspecialchars($submenu['nome']) ?></p>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $submenu['id'] ?>][status]" value="padrao" id="vis_user_padrao_<?= $submenu['id'] ?>" <?= !$permissaoSub ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_padrao_<?= $submenu['id'] ?>">Padrão do Perfil</label></div>
                                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $submenu['id'] ?>][status]" value="permitir" id="vis_user_permitir_<?= $submenu['id'] ?>" <?= ($permissaoSub['permitido'] ?? 0) == 1 ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_permitir_<?= $submenu['id'] ?>">Sempre Permitir</label></div>
                                                <div class="form-check"><input class="form-check-input" type="radio" name="permissoes[<?= $submenu['id'] ?>][status]" value="negar" id="vis_user_negar_<?= $submenu['id'] ?>" <?= isset($permissaoSub['permitido']) && $permissaoSub['permitido'] == 0 ? 'checked' : '' ?>><label class="form-check-label" for="vis_user_negar_<?= $submenu['id'] ?>">Sempre Negar</label></div>
                                                <hr>
                                                <div class="form-group"><label class="small">Início da Validade (opcional)</label><input type="date" class="form-control form-control-sm" name="permissoes[<?= $submenu['id'] ?>][data_inicio]" value="<?= $permissaoSub['data_inicio_validade'] ?? '' ?>"></div>
                                                <div class="form-group"><label class="small">Fim da Validade (opcional)</label><input type="date" class="form-control form-control-sm" name="permissoes[<?= $submenu['id'] ?>][data_fim]" value="<?= $permissaoSub['data_fim_validade'] ?? '' ?>"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card-footer bg-white mt-3 pl-0">
            <button type="submit" class="btn btn-success">Salvar Visibilidade do Usuário</button>
        </div>
    </form>
<?php endif; ?>