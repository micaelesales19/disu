<?php

namespace App\Models;

use CodeIgniter\Model;

class CobrancaModel extends Model
{
    protected $table            = 'cobrancas';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useAutoIncrement = true;
    protected $protectFields    = true;

    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'cliente_id',
        'contrato_id',
        'descricao',
        'valor',
        'vencimento',
        'competencia',
        'status',
        'origem',
        'pago_em',
        'valor_pago',
        'forma_pagamento',
        'origem_pagamento',
        'boleto_id',
        'boleto_linha_digitavel',
        'boleto_codigo_barras',
        'boleto_pdf_url',
        'link_pagamento',
        'pix_copia_cola',
        'observacoes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'cliente_id' => 'required|is_natural_no_zero',
        'valor'      => 'required|decimal',
        'vencimento' => 'required|valid_date[Y-m-d]',
        'status'     => 'required|in_list[em_aberto,pago,recebido,vencido,a_receber,cancelado,expirado,processando,erro]',
        'origem'     => 'required|in_list[avulsa,contrato]',
        'forma_pagamento'  => 'permit_empty|in_list[pix,transferencia,dinheiro,outro]',
        'origem_pagamento' => 'permit_empty|in_list[manual,banco_inter]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Selecione uma empresa.',
        ],
        'valor' => [
            'required' => 'Informe o valor da cobrança.',
        ],
        'vencimento' => [
            'required' => 'Informe o vencimento.',
        ],
    ];

    protected $skipValidation = false;
}
