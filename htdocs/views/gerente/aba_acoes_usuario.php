<p>Selecione um usuário para definir permissões de ação (criar, editar, excluir) que sobrepõem as permissões do seu perfil.</p>
<form method="GET" action="<?= BASE_PATH ?>/gerente">
    <input type="hidden" name="tab" value="acoes_usuario">
    <div class="form-group row">
        <label for="usuario_id_acao" class="col-sm-2 col-form-label">Usuário:</label>
        <div class="col-sm-8">
            <select name="usuario_id" id="usuario_id_acao" class="form-control">
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
    <h4>Editando ações para: <strong><?= htmlspecialchars(array_column($usuarios, 'username', 'id')[$usuarioSelecionadoId]); ?></strong></h4>
    <form action="<?= BASE_PATH ?>/gerente/salvar-acoes-usuario" method="POST">
        <input type="hidden" name="action" value="salvar_acoes_usuario">
        <input type="hidden" name="usuario_id" value="<?= $usuarioSelecionadoId ?>">

        <div id="accordionAcoesUsuario" class="mt-3">
            <?php
            $menuHierarquicoAcoesUsuario = GerenteController::organizarModulosHierarquicamente($modulosGerenciaveis);

            foreach ($menuHierarquicoAcoesUsuario as $menuPrincipal):
                $permissoes = $acaoPermissoesUsuarioAtuais[$menuPrincipal['id']] ?? [];
            ?>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header" id="heading-act-user-<?= $menuPrincipal['id'] ?>">
                        <h5 class="mb-0">
                            <a href="#" class="btn btn-link text-dark font-weight-bold" data-toggle="collapse" data-target="#collapse-act-user-<?= $menuPrincipal['id'] ?>">
                                <i class="fas fa-plus mr-2 expand-icon"></i>
                                <?= htmlspecialchars($menuPrincipal['nome']) ?>
                            </a>
                        </h5>
                    </div>
                    <div id="collapse-act-user-<?= $menuPrincipal['id'] ?>" class="collapse" data-parent="#accordionAcoesUsuario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Criar</label>
                                    <select class="form-control form-control-sm" name="permissoes[<?= $menuPrincipal['id'] ?>][criar]">
                                        <option value="padrao" <?= !isset($permissoes['criar']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                        <option value="permitir" <?= isset($permissoes['criar']) && $permissoes['criar'] ? 'selected' : '' ?>>Permitir</option>
                                        <option value="negar" <?= isset($permissoes['criar']) && !$permissoes['criar'] ? 'selected' : '' ?>>Negar</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Editar</label>
                                    <select class="form-control form-control-sm" name="permissoes[<?= $menuPrincipal['id'] ?>][editar]">
                                        <option value="padrao" <?= !isset($permissoes['editar']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                        <option value="permitir" <?= isset($permissoes['editar']) && $permissoes['editar'] ? 'selected' : '' ?>>Permitir</option>
                                        <option value="negar" <?= isset($permissoes['editar']) && !$permissoes['editar'] ? 'selected' : '' ?>>Negar</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Excluir</label>
                                    <select class="form-control form-control-sm" name="permissoes[<?= $menuPrincipal['id'] ?>][excluir]">
                                        <option value="padrao" <?= !isset($permissoes['excluir']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                        <option value="permitir" <?= isset($permissoes['excluir']) && $permissoes['excluir'] ? 'selected' : '' ?>>Permitir</option>
                                        <option value="negar" <?= isset($permissoes['excluir']) && !$permissoes['excluir'] ? 'selected' : '' ?>>Negar</option>
                                    </select>
                                </div>
                            </div>

                            <?php if (!empty($menuPrincipal['submenus'])): ?>
                                <hr class="mt-4">
                                <div class="ml-4">
                                    <?php foreach ($menuPrincipal['submenus'] as $submenu):
                                        $permissoesSub = $acaoPermissoesUsuarioAtuais[$submenu['id']] ?? [];
                                    ?>
                                        <div class="card card-outline card-secondary mb-3">
                                            <div class="card-header bg-light py-2">
                                                <p class="card-title text-dark mb-0"><?= htmlspecialchars($submenu['nome']) ?></p>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="small">Criar</label>
                                                        <select class="form-control form-control-sm" name="permissoes[<?= $submenu['id'] ?>][criar]">
                                                            <option value="padrao" <?= !isset($permissoesSub['criar']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                                            <option value="permitir" <?= isset($permissoesSub['criar']) && $permissoesSub['criar'] ? 'selected' : '' ?>>Permitir</option>
                                                            <option value="negar" <?= isset($permissoesSub['criar']) && !$permissoesSub['criar'] ? 'selected' : '' ?>>Negar</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small">Editar</label>
                                                        <select class="form-control form-control-sm" name="permissoes[<?= $submenu['id'] ?>][editar]">
                                                            <option value="padrao" <?= !isset($permissoesSub['editar']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                                            <option value="permitir" <?= isset($permissoesSub['editar']) && $permissoesSub['editar'] ? 'selected' : '' ?>>Permitir</option>
                                                            <option value="negar" <?= isset($permissoesSub['editar']) && !$permissoesSub['editar'] ? 'selected' : '' ?>>Negar</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small">Excluir</label>
                                                        <select class="form-control form-control-sm" name="permissoes[<?= $submenu['id'] ?>][excluir]">
                                                            <option value="padrao" <?= !isset($permissoesSub['excluir']) ? 'selected' : '' ?>>Padrão do Perfil</option>
                                                            <option value="permitir" <?= isset($permissoesSub['excluir']) && $permissoesSub['excluir'] ? 'selected' : '' ?>>Permitir</option>
                                                            <option value="negar" <?= isset($permissoesSub['excluir']) && !$permissoesSub['excluir'] ? 'selected' : '' ?>>Negar</option>
                                                        </select>
                                                    </div>
                                                </div>
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
            <button type="submit" class="btn btn-success">Salvar Ações do Usuário</button>
        </div>
    </form>
<?php endif; ?>