<?php
// public/index.php (Versão Corrigida)

require_once __DIR__ . '/../config/constants.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Router.php';

$router = new Router();

// Rotas de Autenticação e Dashboard
$router->add('GET', '/login', [AuthController::class, 'showLoginForm']);
$router->add('POST', '/login', [AuthController::class, 'processLogin']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);
$router->add('GET', '/dashboard', [DashboardController::class, 'index']);

// Rotas do Módulo de Serviços
$router->add('GET', '/servicos', [ServicoController::class, 'index']);
$router->add('GET', '/servicos/novo', [ServicoController::class, 'create']);
$router->add('GET', '/servicos/editar/{id}', [ServicoController::class, 'edit']);
$router->add('POST', '/servicos/salvar', [ServicoController::class, 'store']);
$router->add('GET', '/servicos/excluir/{id}', [ServicoController::class, 'delete']);

// Rotas do Módulo de Estruturas
$router->add('GET', '/estruturas', [EstruturaController::class, 'index']);
$router->add('GET', '/estruturas/novo', [EstruturaController::class, 'create']);
$router->add('GET', '/estruturas/editar/{id}', [EstruturaController::class, 'edit']);
$router->add('POST', '/estruturas/salvar', [EstruturaController::class, 'store']);
$router->add('GET', '/estruturas/excluir/{id}', [EstruturaController::class, 'delete']);
$router->add('POST', '/estruturas/salvar-composicao-materiais', [EstruturaController::class, 'storeComposicaoMateriais']);
$router->add('POST', '/estruturas/salvar-composicao-subestruturas', [EstruturaController::class, 'storeComposicaoSubestruturas']);

// ADICIONE ESTA NOVA ROTA PARA O GERENCIADOR
$router->add('GET', '/gerente', [GerenteController::class, 'index']);
$router->add('POST', '/gerente/salvar-visibilidade-perfil', [GerenteController::class, 'storeVisibilidadePerfil']);
$router->add('POST', '/gerente/salvar-acoes-perfil', [GerenteController::class, 'storeAcoesPerfil']);
$router->add('POST', '/gerente/salvar-visibilidade-usuario', [GerenteController::class, 'storeVisibilidadeUsuario']);
$router->add('POST', '/gerente/salvar-acoes-usuario', [GerenteController::class, 'storeAcoesUsuario']);

// ADICIONE ESTAS NOVAS ROTAS PARA O CONTROLE DE ACESSO
$router->add('GET', '/controle-acesso', [ControleAcessoController::class, 'index']);
$router->add('POST', '/controle-acesso/salvar-regras-perfil', [ControleAcessoController::class, 'storeRegrasPerfil']);
$router->add('POST', '/controle-acesso/salvar-regras-usuario', [ControleAcessoController::class, 'storeRegrasUsuario']);


// ADICIONE ESTAS NOVAS ROTAS PARA A LIBERAÇÃO DE ACESSO
$router->add('GET', '/liberacao_acesso', [LiberacaoAcessoController::class, 'index']);
$router->add('GET', '/liberacao-acesso/liberar/{id}', [LiberacaoAcessoController::class, 'release']);

// ADICIONE ESTAS NOVAS ROTAS PARA PERFIS TEMPORÁRIOS
$router->add('GET', '/perfis_temporarios', [PerfilTemporarioController::class, 'index']);
$router->add('POST', '/perfis_temporarios/salvar', [PerfilTemporarioController::class, 'store']);
$router->add('GET', '/perfis_temporarios/excluir/{id}', [PerfilTemporarioController::class, 'delete']);

// ADICIONE ESTAS NOVAS ROTAS PARA ESPELHO DE PERMISSÕES
$router->add('GET', '/espelhar_permissoes', [GerenteController::class, 'showEspelharPermissoesForm']);
$router->add('POST', '/espelhar_permissoes/salvar', [GerenteController::class, 'storeEspelhamento']);

// --- ROTAS DA API ---
$router->add('GET', '/api/servicos/search', [ServicoController::class, 'search']);

// >>> ADICIONE ESTA ROTA FALTANTE AQUI <<<
$router->add('GET', '/api/estruturas/search', [EstruturaController::class, 'search']);

$router->add('GET', '/api/estruturas/search-materiais', [EstruturaController::class, 'searchMateriaisAjax']);
$router->add('GET', '/api/estruturas/search-estruturas', [EstruturaController::class, 'searchEstruturasAjax']);

// Roteamento para o Módulo de Colaboradores (Funcionário)
$router->add('GET', '/funcionario', [FuncionarioController::class, 'index']);
$router->add('GET', '/funcionario/novo', [FuncionarioController::class, 'create']);
$router->add('GET', '/funcionario/editar/{id}', [FuncionarioController::class, 'edit']);
$router->add('POST', '/funcionario/salvar', [FuncionarioController::class, 'store']);
$router->add('GET', '/funcionario/excluir/{id}', [FuncionarioController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/funcionarios/search', [FuncionarioController::class, 'search']);

// Roteamento para o Módulo de Material
$router->add('GET', '/material', [MaterialController::class, 'index']);
$router->add('GET', '/material/novo', [MaterialController::class, 'create']);
$router->add('GET', '/material/editar/{id}', [MaterialController::class, 'edit']);
$router->add('POST', '/material/salvar', [MaterialController::class, 'store']);
$router->add('GET', '/material/excluir/{id}', [MaterialController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/materiais/search', [MaterialController::class, 'search']);

// Roteamento para o Módulo de Empresa
$router->add('GET', '/empresa', [EmpresaController::class, 'index']);
$router->add('GET', '/empresa/novo', [EmpresaController::class, 'create']);
$router->add('GET', '/empresa/editar/{id}', [EmpresaController::class, 'edit']);
$router->add('POST', '/empresa/salvar', [EmpresaController::class, 'store']);
$router->add('GET', '/empresa/excluir/{id}', [EmpresaController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/empresas/search', [EmpresaController::class, 'search']);

// Roteamento para o Módulo de Contratos
$router->add('GET', '/contratos', [ContratoController::class, 'index']);
$router->add('GET', '/contratos/novo', [ContratoController::class, 'create']);
$router->add('GET', '/contratos/editar/{id}', [ContratoController::class, 'edit']);
$router->add('POST', '/contratos/salvar', [ContratoController::class, 'store']);
$router->add('GET', '/contratos/excluir/{id}', [ContratoController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/contratos/search', [ContratoController::class, 'search']);

// Roteamento para o Módulo de Projetos
$router->add('GET', '/projetos', [ProjetoController::class, 'index']);
$router->add('GET', '/projetos/novo', [ProjetoController::class, 'create']);
$router->add('GET', '/projetos/editar/{id}', [ProjetoController::class, 'edit']);
$router->add('POST', '/projetos/salvar', [ProjetoController::class, 'store']);
$router->add('GET', '/projetos/excluir/{id}', [ProjetoController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/projetos/search', [ProjetoController::class, 'search']);

// Roteamento para o Módulo de Módulos
$router->add('GET', '/modulos', [ModuloController::class, 'index']);
$router->add('GET', '/modulos/novo', [ModuloController::class, 'create']);
$router->add('GET', '/modulos/editar/{id}', [ModuloController::class, 'edit']);
$router->add('POST', '/modulos/salvar', [ModuloController::class, 'store']);
$router->add('GET', '/modulos/excluir/{id}', [ModuloController::class, 'delete']);

// Rota para a API de busca em tempo real
$router->add('GET', '/api/modulos/search', [ModuloController::class, 'search']);

// Rotas para o Módulo de Equipes
$router->add('GET', '/equipes', [EquipeController::class, 'index']);
$router->add('GET', '/equipes/novo', [EquipeController::class, 'create']);
$router->add('GET', '/equipes/editar/{id}', [EquipeController::class, 'edit']);
$router->add('POST', '/equipes/salvar', [EquipeController::class, 'store']);
$router->add('GET', '/equipes/excluir/{id}', [EquipeController::class, 'delete']);
$router->add('GET', '/api/equipes/search', [EquipeController::class, 'search']);

// Captura e processa a URL
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$uri = str_replace(BASE_PATH, '', $uri);
if (empty($uri)) {
    $uri = '/login';
}

$router->dispatch($uri, $_SERVER['REQUEST_METHOD']);
