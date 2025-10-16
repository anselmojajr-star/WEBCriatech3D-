<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?= $isEditing ? 'Editar Material' : 'Novo Material' ?></h3>
    </div>

    <form action="<?= BASE_PATH ?>/material/salvar" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($material['id'] ?? '') ?>">
        <div class="card-body">
            <div class="form-group">
                <label for="material">Nome do Material</label>
                <input type="text" class="form-control" id="material" name="material" value="<?= htmlspecialchars($material['material'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($material['descricao'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="codigoMaterial">Código</label>
                        <input type="text" class="form-control" id="codigoMaterial" name="codigoMaterial" value="<?= htmlspecialchars($material['codigoMaterial'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_medida">Unidade de Medida</label>
                        <select class="form-control" id="id_medida" name="id_medida" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($medidas as $medida): ?>
                                <option value="<?= $medida['id'] ?>" <?= (isset($material['id_medida']) && $material['id_medida'] == $medida['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($medida['unidade']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" <?= ($material['ativo'] ?? 's') == 's' ? 'checked' : '' ?>>
                    <label class="custom-control-label" for="ativo">Ativo</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="<?= BASE_PATH ?>/material" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>