<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-edit"></i>
            <?= $isEditing ? 'Editar Equipe' : 'Nova Equipe' ?>
        </h3>

        <div class="card-tools">
            <a href="<?= BASE_PATH ?>/equipes" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>
    <form action="<?= BASE_PATH ?>/equipes/salvar" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($equipe['id'] ?? '') ?>">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nome_equipe">Nome da Equipe <span class="text-danger">*</span></label>
                    <input type="text" name="nome_equipe" id="nome_equipe" class="form-control" value="<?= htmlspecialchars($equipe['nome_equipe'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="id_lider">Líder da Equipe</label>
                    <select name="id_lider" id="id_lider" class="form-control select2">
                        <option value="">-- Nenhum --</option>
                        <?php foreach ($lideres as $lider): ?>
                            <option value="<?= $lider['id'] ?>" <?= (($equipe['id_lider'] ?? '') == $lider['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lider['nome_com_cargo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="filtro_perfil">Filtrar Membros por Perfil</label>
                    <select id="filtro_perfil" class="form-control select2">
                        <option value="todos">-- Todos os Perfis --</option>
                        <?php foreach ($perfis as $perfil): ?>
                            <option value="<?= $perfil['id'] ?>"><?= htmlspecialchars($perfil['perfil']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="membros_ids">Membros da Equipe</label>
                <select name="membros_ids[]" id="membros_ids" class="form-control select2" multiple="multiple" data-placeholder="Selecione os membros">
                    <?php foreach ($todosOsUsuarios as $usuario): ?>
                        <option value="<?= $usuario['id'] ?>"
                            data-perfis="<?= htmlspecialchars($usuario['perfis_ids'] ?? '') ?>"
                            <?= in_array($usuario['id'], $membrosDaEquipe) ? 'selected' : '' ?>>
                            <?= htmlspecialchars('[' . $usuario['matricula'] . '] ' . $usuario['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-danger">Nomes a vermelho já pertencem a outra equipe.</small>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3"><?= htmlspecialchars($equipe['descricao'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" <?= !isset($equipe['ativo']) || $equipe['ativo'] ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="ativo">Equipe Ativa</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= BASE_PATH ?>/equipes" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script id="dados-para-js" type="application/json">
    {
        "usuariosJaEmEquipe": <?= json_encode($usuariosJaEmEquipe) ?>
    }
</script>