<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> <?= $isEdit ? 'Editar Colaborador' : 'Novo Colaborador' ?></h3>
        <div class="card-tools">
            <a href="<?= BASE_PATH ?>/funcionario" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Voltar à Lista</a>
        </div>
    </div>
    <div class="card-body">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="funcionarioTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pessoais-tab" data-toggle="pill" href="#pessoais" role="tab">Dados Pessoais</a>
                    </li>
                    <?php if ($isEdit): ?>
                        <li class="nav-item">
                            <a class="nav-link" id="acesso-tab" data-toggle="pill" href="#acesso" role="tab">Acesso ao Sistema</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="endereco-tab" data-toggle="pill" href="#endereco" role="tab">Endereço</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="tab-content mt-4">
                <!--Dados Pessoais-->
                <div class="tab-pane fade show active" id="pessoais" role="tabpanel">
                    <form action="<?= BASE_PATH ?>/funcionario/salvar" method="post">
                        <input type="hidden" name="action" value="salvarPessoais">
                        <input type="hidden" name="id" value="<?= $funcionario['id'] ?? '' ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nome Completo</label>
                                    <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($funcionario['nome'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>CPF</label>
                                    <input type="text" class="form-control" name="cpf" value="<?= htmlspecialchars($funcionario['cpf'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($funcionario['email'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="tel" class="form-control" name="telefone_contato" value="<?= htmlspecialchars($funcionario['telefone_contato'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Naturalidade</label>
                                    <input type="text" class="form-control" name="naturalidade" value="<?= htmlspecialchars($funcionario['naturalidade'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Data de Nascimento</label>
                                    <input type="date" class="form-control" name="data_nascimento" value="<?= htmlspecialchars($funcionario['data_nascimento'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="col-md-4 text-center">
                                <div class="form-group">
                                    <label>Foto de Perfil</label>
                                    <div>
                                        <img src="<?= BASE_PATH . '/' . htmlspecialchars($usuario['foto_perfil'] ?? 'dist/img/avatar.png') ?>"
                                            id="img-preview"
                                            alt="Foto de Perfil"
                                            class="img-thumbnail rounded-circle mb-3"
                                            style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                            onclick="document.getElementById('foto-input').click();">
                                    </div>
                                    <label for="foto-input" class="btn btn-sm btn-info">Alterar Foto</label>
                                    <input type="file" class="d-none" id="foto-input" name="foto_perfil" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white pl-0">
                            <button type="submit" class="btn btn-primary">Salvar Dados Pessoais</button>
                        </div>
                    </form>
                </div>
                <!--Acesso-->
                <div class="tab-pane fade" id="acesso" role="tabpanel">
                    <form action="<?= BASE_PATH ?>/funcionario/salvar" method="post">
                        <input type="hidden" name="action" value="salvarAcesso">
                        <input type="hidden" name="id_funcionario" value="<?= $funcionario['id'] ?? '' ?>">
                        <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?? '' ?>">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Nome de Usuário</label><input type="text" class="form-control" name="username" value="<?= htmlspecialchars($usuario['username'] ?? '') ?>" required></div>
                            <div class="col-md-6 form-group"><label>Matrícula</label><input type="text" class="form-control" name="matricula" value="<?= htmlspecialchars($usuario['matricula'] ?? '') ?>" required></div>
                            <div class="col-md-6 form-group"><label>Senha</label><input type="password" class="form-control" id="password" name="password" placeholder="<?= !empty($usuario['id']) ? 'Deixe em branco para não alterar' : 'Senha obrigatória' ?>"></div>

                            <div class="col-md-6 form-group">
                                <label>Perfil</label>
                                <div class="input-group">
                                    <select class="form-control" name="id_perfil" id="perfil_select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($perfis as $perfil): ?>
                                            <option value="<?= $perfil['id'] ?>" <?= ($perfilUsuario == $perfil['id']) ? 'selected' : '' ?>><?= htmlspecialchars($perfil['perfil']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#modalNovoPerfil">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group align-self-center">
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="ativo" value="1" id="ativo" <?= (isset($usuario['ativo']) && $usuario['ativo'] == 1) || empty($usuario['id']) ? 'checked' : '' ?>><label class="form-check-label" for="ativo">Usuário Ativo</label></div>
                            </div>
                        </div>
                        <div class="card-footer bg-white pl-0">
                            <button type="submit" class="btn btn-primary">Salvar Acesso</button>
                        </div>
                    </form>

                    <div class="modal fade" id="modalNovoPerfil" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Cadastrar Novo Perfil</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nome do Perfil</label>
                                        <input type="text" id="inputNovoPerfilNome" class="form-control" placeholder="Ex: Vendedor">
                                    </div>
                                    <div id="novoPerfilStatus"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnSalvarNovoPerfil">Salvar Perfil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Endereço-->
                <div class="tab-pane fade" id="endereco" role="tabpanel">
                    <form action="<?= BASE_PATH ?>/funcionario/salvar" method="post" id="form-endereco">
                        <input type="hidden" name="action" value="salvarEndereco">
                        <input type="hidden" name="id_funcionario" value="<?= $funcionario['id'] ?? '' ?>">
                        <div class="row">
                            <div class="col-md-4 form-group"><label>CEP</label><input type="text" class="form-control" name="cep" value="<?= htmlspecialchars($endereco['cep'] ?? '') ?>"></div>
                            <div class="col-md-8 form-group"><label>Logradouro</label><input type="text" class="form-control" name="logradouro" value="<?= htmlspecialchars($endereco['logradouro'] ?? '') ?>"></div>
                            <div class="col-md-4 form-group"><label>Número</label><input type="text" class="form-control" name="numero" value="<?= htmlspecialchars($endereco['numero'] ?? '') ?>"></div>
                            <div class="col-md-8 form-group"><label>Complemento</label><input type="text" class="form-control" name="complemento" value="<?= htmlspecialchars($endereco['complemento'] ?? '') ?>"></div>
                            <div class="col-md-4 form-group"><label>Bairro</label><input type="text" class="form-control" name="bairro" value="<?= htmlspecialchars($endereco['bairro'] ?? '') ?>"></div>
                            <div class="col-md-4 form-group">
                                <label>Estado</label>
                                <select class="form-control" name="id_estado" id="estado_select">
                                    <option value="">Selecione um Estado</option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id'] ?>" <?= (isset($endereco['id_estado']) && $endereco['id_estado'] == $estado['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Cidade</label>
                                <div class="input-group">
                                    <select class="form-control" name="id_cidade" id="cidade_select"
                                        data-cidade-atual-id="<?= $endereco['id_cidade'] ?? '' ?>">
                                        <option value="">Selecione um Estado primeiro</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="button" id="btnNovaCidade" data-toggle="modal" data-target="#modalNovaCidade" disabled>+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="modalNovaCidade" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cadastrar Nova Cidade</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formNovaCidade">
                                                <div class="form-group">
                                                    <label>Nome da Cidade</label>
                                                    <input type="text" id="inputNomeNovaCidade" class="form-control">
                                                </div>
                                                <div id="novaCidadeStatus"></div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn btn-primary" id="btnSalvarNovaCidade">Salvar Cidade</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white pl-0">
                            <button type="button" id="btn-salvar-endereco" class="btn btn-primary">Salvar Endereço</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>