<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-copy"></i> Espelhar Permissões</h3>
            </div>
            <form action="<?= BASE_PATH ?>/espelhar_permissoes/salvar" method="POST"
                onsubmit="return confirm('Atenção! Todas as permissões personalizadas do usuário de DESTINO serão substituídas pelas do usuário de ORIGEM. Deseja continuar?');">

                <input type="hidden" name="action" value="espelhar_permissoes">

                <div class="card-body">
                    <p class="text-muted">Esta ferramenta copia todas as permissões personalizadas (visibilidade e ações) de um usuário para outro. É ideal para configurar rapidamente novos usuários com as mesmas permissões de um já existente.</p>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="source_user_id">Copiar Permissões De:</label>
                            <select name="source_user_id" id="source_user_id" class="form-control" required>
                                <option value="">-- Selecione o usuário de ORIGEM --</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="target_user_id">Para:</label>
                            <select name="target_user_id" id="target_user_id" class="form-control" required>
                                <option value="">-- Selecione o usuário de DESTINO --</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-copy"></i> Espelhar Permissões</button>
                </div>
            </form>
        </div>
    </div>
</div>