<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoServicoModel extends Model
{
    protected $table = 'contrato_servicos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $useTimestamps = false;
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'contrato_id',
        'servico_id',
        'quantidade',
        'valor_unitario',
        'subtotal',
    ];

    protected $validationRules = [
        'contrato_id' => 'required|is_natural_no_zero',
        'servico_id' => 'required|is_natural_no_zero',
        'quantidade' => 'required|integer|greater_than[0]',
        'valor_unitario' => 'required|decimal|greater_than_equal_to[0]',
        'subtotal' => 'required|decimal|greater_than_equal_to[0]',
    ];

    public function listarPorContrato(int $contratoId): array
    {
        return $this
            ->select('contrato_servicos.*, servicos.nome AS servico_nome')
            ->join('servicos', 'servicos.id = contrato_servicos.servico_id')
            ->where('contrato_servicos.contrato_id', $contratoId)
            ->orderBy('contrato_servicos.id', 'ASC')
            ->findAll();
    }
}
