<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cubes"></i> Módulos do Sistema</h3>
        <div class="card-tools">
            <?php
            // ID do módulo "Módulos" é 24 no seu banco de dados
            $moduloIdAtual = 24;

            // AQUI ESTÁ A VERIFICAÇÃO:
            // "Se o usuário logado PODE ('can') 'criar' neste módulo..."
            if (PermissionManager::can('criar', $moduloIdAtual)):
            ?>
                <a href="<?= BASE_PATH ?>/modulos/novo" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Novo Módulo</a>

            <?php endif; // Fim da verificação 
            ?>
        </div>
    </div>
    <div class="card-body">
        <form id="searchForm" method="get" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" id="search" placeholder="Buscar por nome, rota, status ou tipo...">
            </div>
        </form>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Rota</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="modulos-table-body">
            </tbody>
        </table>
    </div>
</div>