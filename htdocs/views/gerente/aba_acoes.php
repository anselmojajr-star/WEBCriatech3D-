<p>Selecione um perfil para editar as ações (criar, editar, excluir) que ele pode realizar em cada módulo.</p>
<!-- <form method="GET" action="gerente.php"> -->
<form method="GET" action="<?= BASE_PATH ?>/gerente">
    <div class="form-group row">
        <label for="perfil_id_acao" class="col-sm-2 col-form-label">Perfil:</label>
        <div class="col-sm-8">
            <select name="perfil_id" id="perfil_id_acao" class="form-control">
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
    <h4>Editando ações para: <strong><?= htmlspecialchars(array_column($perfis, 'perfil', 'id')[$perfilSelecionadoId]); ?></strong></h4>
    <form action="<?= BASE_PATH ?>/gerente/salvar-acoes-perfil" method="POST">
        <input type="hidden" name="action" value="salvar_acoes">
        <input type="hidden" name="perfil_id" value="<?= $perfilSelecionadoId ?>">

        <div class="form-check mb-2 mt-3">
            <input class="form-check-input" type="checkbox" id="marcar-todas-acoes">
            <label class="form-check-label font-weight-bold" for="marcar-todas-acoes">
                Marcar Todas as Ações
            </label>
        </div>

        <div id="accordionAcoes" class="mt-3">
            <?php
            $menuHierarquicoAcoes = GerenteController::organizarModulosHierarquicamente($modulosGerenciaveis);

            foreach ($menuHierarquicoAcoes as $menuPrincipal):
                $permissoesDoModulo = $acaoPermissoesAtuais[$menuPrincipal['id']] ?? [];
            ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header" id="heading-<?= $menuPrincipal['id'] ?>">
                        <h5 class="mb-0">
                            <a href="#" class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapse-<?= $menuPrincipal['id'] ?>">
                                <i class="fas fa-plus mr-2 expand-icon"></i>
                                <?= htmlspecialchars($menuPrincipal['nome']) ?>
                            </a>
                        </h5>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-check"><input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $menuPrincipal['id'] ?>][criar]" <?= in_array('criar', $permissoesDoModulo) ? 'checked' : '' ?>><label class="form-check-label">Criar</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $menuPrincipal['id'] ?>][editar]" <?= in_array('editar', $permissoesDoModulo) ? 'checked' : '' ?>><label class="form-check-label">Editar</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $menuPrincipal['id'] ?>][excluir]" <?= in_array('excluir', $permissoesDoModulo) ? 'checked' : '' ?>><label class="form-check-label">Excluir</label></div>
                            </div>
                        </div>
                    </div>

                    <div id="collapse-<?= $menuPrincipal['id'] ?>" class="collapse" data-parent="#accordionAcoes">
                        <div class="card-body">
                            <?php if (!empty($menuPrincipal['submenus'])): ?>
                                <div class="ml-4">
                                    <?php foreach ($menuPrincipal['submenus'] as $submenu):
                                        $permissoesSubmenu = $acaoPermissoesAtuais[$submenu['id']] ?? [];
                                    ?>
                                        <div class="card card-outline card-secondary mb-3">
                                            <div class="card-header bg-light py-2">
                                                <p class="card-title text-dark mb-0"><?= htmlspecialchars($submenu['nome']) ?></p>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $submenu['id'] ?>][criar]" <?= in_array('criar', $permissoesSubmenu) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Criar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $submenu['id'] ?>][editar]" <?= in_array('editar', $permissoesSubmenu) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Editar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input acao-checkbox" type="checkbox" name="permissoes[<?= $submenu['id'] ?>][excluir]" <?= in_array('excluir', $permissoesSubmenu) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Excluir</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
            <button type="submit" class="btn btn-success">Salvar Ações</button>
        </div>
    </form>
<?php endif; ?>