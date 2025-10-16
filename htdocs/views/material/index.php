<div class="card card-primary card-outline">
    <?php $pageScripts[] = 'dist/js/materiais-search.js'; ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-box"></i> Lista de Materiais</h3>
            <div class="card-tools">
                <?php if (PermissionManager::can('criar', $moduloId)): ?>
                    <a href="<?= BASE_PATH ?>/material/novo" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Material</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control" placeholder="Buscar por nome ou código...">
                </div>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome do Material</th>
                        <th>Medida</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody id="materiais-table-body">
                </tbody>
            </table>
        </div>
    </div>
</div>