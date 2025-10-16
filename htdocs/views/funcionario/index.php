<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Lista de Colaboradores</h3>
        <div class="card-tools">
            <?php if (PermissionManager::can('criar', $moduloIdAtual)): ?>
                <a href="<?= BASE_PATH ?>/funcionario/novo" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Novo Colaborador
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <form id="searchForm" method="get" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por Nome ou CPF...">
            </div>
        </form>
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Naturalidade</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="funcionarios-table-body">
            </tbody>
        </table>
    </div>
</div>