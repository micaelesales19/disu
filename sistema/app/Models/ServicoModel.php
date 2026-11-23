<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicoModel extends Model
{
    protected $table = 'servicos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'codigo_interno',
        'nome',
        'descricao',
        'valor_padrao',
        'unidade',
        'permite_nfse',
        'codigo_fiscal',
        'ativo',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => [
            'label' => 'Nome do serviço',
            'rules' => 'required|max_length[150]',
        ],
        'codigo_interno' => [
            'label' => 'Código interno',
            'rules' => 'permit_empty|max_length[30]',
        ],
        'descricao' => [
            'label' => 'Descrição',
            'rules' => 'permit_empty',
        ],
        'valor_padrao' => [
            'label' => 'Valor padrão',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],
        'unidade' => [
            'label' => 'Unidade de cobrança',
            'rules' => 'required|in_list[servico,mensalidade,hora,unidade]',
        ],
        'permite_nfse' => [
            'label' => 'Emissão de NFS-e',
            'rules' => 'required|in_list[0,1]',
        ],
        'codigo_fiscal' => [
            'label' => 'Código fiscal',
            'rules' => 'permit_empty|max_length[50]',
        ],
        'ativo' => [
            'label' => 'Situação',
            'rules' => 'required|in_list[0,1]',
        ],
    ];
}
