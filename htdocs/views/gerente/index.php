<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#visualizacao_perfil" data-toggle="tab">Visibilidade por Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="#acoes_perfil" data-toggle="tab">Ações por Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="#visualizacao_usuario" data-toggle="tab">Visibilidade por Usuário</a></li>
                        <li class="nav-item"><a class="nav-link" href="#acoes_usuario" data-toggle="tab">Ações por Usuário</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="visualizacao_perfil">
                            <?php include __DIR__ . '/aba_visualizacao.php'; ?>
                        </div>
                        <div class="tab-pane" id="acoes_perfil">
                            <?php include __DIR__ . '/aba_acoes.php'; ?>
                        </div>
                        <div class="tab-pane" id="visualizacao_usuario">
                            <?php include __DIR__ . '/aba_visualizacao_usuario.php'; ?>
                        </div>
                        <div class="tab-pane" id="acoes_usuario">
                            <?php include __DIR__ . '/aba_acoes_usuario.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>