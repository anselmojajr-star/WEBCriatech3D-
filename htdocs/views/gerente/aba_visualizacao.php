<p>Selecione um perfil para editar os módulos que ele pode ver no menu lateral.</p>
<form method="GET" action="<?= BASE_PATH ?>/gerente">
    <div class="form-group row">
        <label for="perfil_id_vis" class="col-sm-2 col-form-label">Perfil:</label>
        <div class="col-sm-8">
            <select name="perfil_id" id="perfil_id_vis" class="form-control">
                <option value="">-- Selecione um perfil --</option>
                <?php foreach ($perfis as $perfil): ?>
                    <option value="<?= $perfil['id'] ?>" <?= ($perfilSelecionadoId == $perfil['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($perfil['perfil']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>

<?php if ($perfilSelecionadoId): ?>
    <hr>
    <h4>Editando visibilidade para: <strong><?= htmlspecialchars(array_column($perfis, 'perfil', 'id')[$perfilSelecionadoId]); ?></strong></h4>
    <form action="<?= BASE_PATH ?>/gerente/salvar-visibilidade-perfil" method="POST">
        <input type="hidden" name="action" value="salvar_visualizacao">
        <input type="hidden" name="perfil_id" value="<?= $perfilSelecionadoId ?>">

        <div id="accordionVisualizacao" class="mt-3">
            <?php
            $menuHierarquico = GerenteController::organizarModulosHierarquicamente($modulosGerenciaveis);

            foreach ($menuHierarquico as $menuPrincipal): ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header" id="heading-vis-<?= $menuPrincipal['id'] ?>">
                        <h5 class="mb-0">
                            <a href="#" class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapse-vis-<?= $menuPrincipal['id'] ?>">
                                <i class="fas fa-plus mr-2 expand-icon"></i>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input permission-parent" type="checkbox" name="modulos_ids[]" value="<?= $menuPrincipal['id'] ?>" id="modulo_vis_<?= $menuPrincipal['id'] ?>"
                                        <?= in_array($menuPrincipal['id'], $permissoesAtuais) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="modulo_vis_<?= $menuPrincipal['id'] ?>">
                                        <?= htmlspecialchars($menuPrincipal['nome']) ?>
                                    </label>
                                </div>
                            </a>
                        </h5>
                    </div>

                    <div id="collapse-vis-<?= $menuPrincipal['id'] ?>" class="collapse" data-parent="#accordionVisualizacao">
                        <div class="card-body">
                            <?php if (!empty($menuPrincipal['submenus'])): ?>
                                <div class="ml-4">
                                    <?php foreach ($menuPrincipal['submenus'] as $submenu): ?>
                                        <div class="form-check">
                                            <input class="form-check-input permission-child" type="checkbox" name="modulos_ids[]" value="<?= $submenu['id'] ?>" id="modulo_vis_<?= $submenu['id'] ?>"
                                                data-parent-id="modulo_vis_<?= $menuPrincipal['id'] ?>"
                                                <?= in_array($submenu['id'], $permissoesAtuais) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="modulo_vis_<?= $submenu['id'] ?>">
                                                <?= htmlspecialchars($submenu['nome']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Este módulo não possui submódulos.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer bg-white mt-3 pl-0">
            <button type="submit" class="btn btn-success">Salvar Visibilidade</button>
        </div>
    </form>
<?php endif; ?>