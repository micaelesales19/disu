<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoModel extends Model
{
    protected $table = 'contratos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'cliente_id',
        'titulo',
        'tipo',
        'valor',
        'dia_vencimento',
        'forma_pagamento',
        'data_inicio',
        'data_termino',
        'prazo_indeterminado',
        'decimo_terceiro',
        'meses_decimo_terceiro',
        'mover_fim_semana',
        'emitir_nfse',
        'momento_nfse',
        'reajuste_automatico',
        'tipo_reajuste',
        'periodicidade_reajuste',
        'aplicacao_reajuste',
        'percentual_reajuste',
        'data_proximo_reajuste',
        'desconto_antecipacao',
        'tipo_desconto',
        'percentual_desconto',
        'valor_desconto_fixo',
        'dias_antecipacao',
        'observacoes',
        'situacao',
    ];

    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'titulo' => 'required|max_length[180]',
        'tipo' => 'required|in_list[mensal,pontual]',
        'valor' => 'required|decimal|greater_than_equal_to[0]',
        'dia_vencimento' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[31]',
        'forma_pagamento' => 'required|in_list[boleto,pix,transferencia,outro]',
        'data_inicio' => 'required|valid_date[Y-m-d]',
        'data_termino' => 'permit_empty|valid_date[Y-m-d]',
        'situacao' => 'required|in_list[ativo,encerrado]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Selecione uma empresa.',
        ],
        'titulo' => [
            'required' => 'Informe o título do contrato.',
        ],
        'tipo' => [
            'in_list' => 'Selecione um tipo de contrato válido.',
        ],
    ];

    public function listarComEmpresa(): array
    {
        return $this
            ->select('contratos.*, clientes.nome AS empresa_nome')
            ->join('clientes', 'clientes.id = contratos.cliente_id')
            ->orderBy('contratos.id', 'DESC')
            ->findAll();
    }
}
