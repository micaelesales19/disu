<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useAutoIncrement  = true;
    protected $protectFields    = true;

    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'codigo_interno',
        'cpf_cnpj',
        'nome',
        'nome_fantasia',
        'razao_social',
        'contador_responsavel',
        'ativo',
        'vip',
        'data_entrada',
        'data_saida',

        'email',
        'email_cobranca',
        'telefone',
        'whatsapp',

        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'observacoes_internas',

        'regime_tributario',
        'inscricao_municipal',
        'inscricao_estadual',
        'capital_social',
        'plano_empresa',
        'possui_funcionarios',
        'possui_extrato_bancario',
        'importar_notas_sieg',
        'informacoes_fiscais',

        'dados_contabeis',
        'dados_dp',

        'url_contrato_social',
        'url_cartao_cnpj',
    ];

    // DATAS
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // VALIDAÇÕES
    protected $validationRules = [
        'nome' => [
            'label' => 'Nome',
            'rules' => 'required|max_length[255]',
        ],

        'cpf_cnpj' => [
            'label' => 'CPF/CNPJ',
            'rules' => 'permit_empty|regex_match[/^([0-9]{11}|[0-9]{14})$/]|is_unique[clientes.cpf_cnpj,id,{id}]',
        ],

        'codigo_interno' => [
            'label' => 'Código interno',
            'rules' => 'permit_empty|max_length[30]|is_unique[clientes.codigo_interno,id,{id}]',
        ],

        'email' => [
            'label' => 'E-mail',
            'rules' => 'permit_empty|valid_email|max_length[255]',
        ],

        'email_cobranca' => [
            'label' => 'E-mail de cobrança',
            'rules' => 'permit_empty|valid_email|max_length[255]',
        ],

        'uf' => [
            'label' => 'UF',
            'rules' => 'permit_empty|exact_length[2]|alpha',
        ],

        'capital_social' => [
            'label' => 'Capital social',
            'rules' => 'permit_empty|decimal',
        ],

        'url_contrato_social' => [
            'label' => 'URL do contrato social',
            'rules' => 'permit_empty|valid_url|max_length[2048]',
        ],

        'url_cartao_cnpj' => [
            'label' => 'URL do cartão CNPJ',
            'rules' => 'permit_empty|valid_url|max_length[2048]',
        ],
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome da empresa é obrigatório.',
        ],
        'cpf_cnpj' => [
            'regex_match' => 'Informe um CPF com 11 dígitos ou CNPJ com 14 dígitos.',
            'is_unique' => 'Este CPF/CNPJ já está cadastrado.',
        ],
        'codigo_interno' => [
            'is_unique' => 'Este código interno já está cadastrado.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // ====================================
    // CONSULTAS PARA A LISTAGEM
    // ====================================

    public function listarEmpresas(): array
    {
        return $this
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    // ====================================
    // INDICADORES
    // ====================================

    public function indicadores(): array
    {
        return [
            'total' => $this->countAllResults(),

            'ativos' => $this
                ->where('ativo', 1)
                ->countAllResults(),

            'inativos' => $this
                ->where('ativo', 0)
                ->countAllResults(),

            'vip' => $this
                ->where('vip', 1)
                ->countAllResults(),
        ];
    }

    // ====================================
    // LIXEIRA DE EMPRESAS
    // ====================================

    public function listarExcluidas(int $porPagina = 10): array
    {
        return $this
            ->onlyDeleted()
            ->orderBy('deleted_at', 'DESC')
            ->paginate($porPagina, 'lixeira');
    }

    public function contarExcluidas(): int
    {
        return $this
            ->onlyDeleted()
            ->countAllResults();
    }
}
