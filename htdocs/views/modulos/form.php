<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> <?= $moduleId ? 'Editar Módulo' : 'Novo Módulo' ?></h3>
        <div class="card-tools">
            <a href="<?= BASE_PATH ?>/modulos" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <form action="<?= BASE_PATH ?>/modulos/salvar" method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars($modulo['id'] ?? '') ?>">

            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($modulo['nome'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Rota</label>
                <input type="text" name="rota" class="form-control" value="<?= htmlspecialchars($modulo['rota'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Ícone (ex: fas fa-tachometer-alt)</label>
                <input type="text" name="icone" class="form-control" value="<?= htmlspecialchars($modulo['icone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Módulo Pai</label>
                <select name="id_modulo_pai" class="form-control">
                    <option value="">Nenhum (Menu Principal)</option>
                    <?php
                    $parents = ModuloController::listParentModules();
                    foreach ($parents as $parent):
                        $selected = (isset($modulo['id_modulo_pai']) && $modulo['id_modulo_pai'] == $parent['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $parent['id'] ?>" <?= $selected ?>><?= htmlspecialchars($parent['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ordem</label>
                <input type="number" name="ordem" class="form-control" value="<?= htmlspecialchars($modulo['ordem'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>Tipo de Menu</label>
                <select name="tipo_menu" class="form-control" required>
                    <option value="menu_principal" <?= (isset($modulo['tipo_menu']) && $modulo['tipo_menu'] === 'menu_principal') ? 'selected' : '' ?>>Menu Principal</option>
                    <option value="submenu_item" <?= (isset($modulo['tipo_menu']) && $modulo['tipo_menu'] === 'submenu_item') ? 'selected' : '' ?>>Submenu</option>
                    <option value="funcionalidade" <?= (isset($modulo['tipo_menu']) && $modulo['tipo_menu'] === 'funcionalidade') ? 'selected' : '' ?>>Funcionalidade</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="rascunho" <?= (isset($modulo['status']) && $modulo['status'] === 'rascunho') ? 'selected' : '' ?>>Rascunho</option>
                    <option value="pendente_aprovacao" <?= (isset($modulo['status']) && $modulo['status'] === 'pendente_aprovacao') ? 'selected' : '' ?>>Pendente de Aprovação</option>
                    <option value="aprovado" <?= (isset($modulo['status']) && $modulo['status'] === 'aprovado') ? 'selected' : '' ?>>Aprovado</option>
                    <option value="liberado" <?= (isset($modulo['status']) && $modulo['status'] === 'liberado') ? 'selected' : '' ?>>Liberado</option>
                    <option value="desativado" <?= (isset($modulo['status']) && $modulo['status'] === 'desativado') ? 'selected' : '' ?>>Desativado</option>
                </select>
            </div>

            <hr>
            <h4>Permissões de Perfil</h4>
            <p>Marque os perfis que terão acesso a este módulo quando ele estiver "liberado".</p>
            <?php foreach ($allProfiles as $profile): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="perfil_ids[]" value="<?= $profile['id'] ?>">
                    <label class="form-check-label">
                        <?= htmlspecialchars($profile['perfil']) ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <div class="card-footer bg-white pl-0 mt-4">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>