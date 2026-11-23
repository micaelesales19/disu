<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rotas de autenticação do Shield
service('auth')->routes($routes);

// Página inicial
$routes->get('/', static function () {
    return redirect()->to(url_to('login'));
});

// Dashboard
$routes->get(
    'dashboard',
    'Dashboard::index',
    ['filter' => 'session']
);

$routes->get('auth/google', 'GoogleAuth::redirect');

$routes->get(
    'auth/google/link',
    'GoogleAuth::link',
    ['filter' => 'session']
);

$routes->get(
    'auth/google/callback',
    'GoogleAuth::callback'
);

/*
|--------------------------------------------------------------------------
| CLIENTES
|--------------------------------------------------------------------------
*/

$routes->group('clientes', ['filter' => 'session'], static function ($routes) {
    $routes->get('empresas', 'Clientes\Empresas::index');
    $routes->get('empresas/novo', 'Clientes\Empresas::novo');
    $routes->post('empresas/salvar', 'Clientes\Empresas::salvar');
    $routes->get('empresas/consultar-cnpj/(:segment)', 'Clientes\Empresas::consultarCnpj/$1');
    $routes->get('empresas/editar/(:num)', 'Clientes\Empresas::editar/$1');
    $routes->post('empresas/atualizar/(:num)', 'Clientes\Empresas::atualizar/$1');

    $routes->post(
        'empresas/alterar-status/(:num)',
        'Clientes\Empresas::alterarStatus/$1'
    );

    $routes->post(
        'empresas/excluir/(:num)',
        'Clientes\Empresas::excluir/$1'
    );

    $routes->get(
        'empresas/lixeira',
        'Clientes\Empresas::lixeira'
    );

    $routes->post(
        'empresas/restaurar/(:num)',
        'Clientes\Empresas::restaurar/$1'
    );
});

/*
|--------------------------------------------------------------------------
| FINANCEIRO - SERVIÇOS
|--------------------------------------------------------------------------
*/

$routes->group('financeiro/servicos', ['filter' => 'session'], static function ($routes) {
    $routes->get(
        '/',
        'Financeiro\Servicos::index'
    );

    $routes->post(
        'salvar',
        'Financeiro\Servicos::salvar'
    );

    $routes->post(
        'desativar/(:num)',
        'Financeiro\Servicos::desativar/$1'
    );
});

/*
|--------------------------------------------------------------------------
| FINANCEIRO - CONTRATOS
|--------------------------------------------------------------------------
*/

$routes->group('financeiro/contratos', ['filter' => 'session'], static function ($routes) {
    // Listagem
    $routes->get('/', 'Financeiro\Contratos::index');
    // Novo contrato
    $routes->get('novo', 'Financeiro\Contratos::novo');
    // Salvar contrato
    $routes->post('salvar', 'Financeiro\Contratos::salvar');
    // Abrir edição
    $routes->get('editar/(:num)', 'Financeiro\Contratos::editar/$1');
    // Salvar alterações
    $routes->post('atualizar/(:num)', 'Financeiro\Contratos::atualizar/$1');
    // Excluir contrato
    $routes->post('excluir/(:num)', 'Financeiro\Contratos::excluir/$1');
});


/*
|--------------------------------------------------------------------------
| FINANCEIRO - COBRANÇAS
|--------------------------------------------------------------------------
*/
$routes->group('financeiro/cobrancas', ['filter' => 'session'], static function ($routes) {
    $routes->get(
        '/',
        'Financeiro\Cobrancas::index'
    );
    $routes->get('novo', 'Financeiro\Cobrancas::novo');
    $routes->get(
        'contratos-empresa/(:num)',
        'Financeiro\Cobrancas::contratosPorEmpresa/$1'
    );
    $routes->post(
        'salvar',
        'Financeiro\Cobrancas::salvar'
    );
    $routes->get('editar/(:num)', 'Financeiro\Cobrancas::editar/$1');
    $routes->post('alterar-vencimento/(:num)', 'Financeiro\Cobrancas::alterarVencimento/$1');
    $routes->post('alterar-status/(:num)', 'Financeiro\Cobrancas::alterarStatus/$1');
});
