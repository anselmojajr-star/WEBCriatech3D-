<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-unlock-alt"></i> Sessões de Usuários Ativas</h3>
            </div>
            <div class="card-body">
                <p>Esta página lista todos os usuários que estão atualmente logados no sistema. Utilize a busca para encontrar um usuário específico e liberar seu acesso, se necessário.</p>
                <div class="form-group row">
                    <div class="col-md-4">
                        <input type="text" id="search-session" class="form-control" placeholder="Buscar por nome de usuário...">
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Endereço IP</th>
                            <th>Horário do Login</th>
                            <th width="150px" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="sessoes-table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>