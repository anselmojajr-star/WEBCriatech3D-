<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users"></i> Lista de Equipes</h3>
        <div class="card-tools">
            <?php if (PermissionManager::can('criar', $moduloId)): ?>
                <a href="<?= BASE_PATH ?>/equipes/novo" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nova Equipe
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Buscar por nome da equipe ou líder...">
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome da Equipe</th>
                    <th>Líder</th>
                    <th>Ativo</th>
                    <th style="width: 100px;">Ações</th>
                </tr>
            </thead>
            <tbody id="equipes-table-body">
            </tbody>
        </table>
    </div>
</div>