<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoDescontoToContratos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contratos', [
            'tipo_desconto' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'desconto_antecipacao',
            ],
            'valor_desconto_fixo' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'after'      => 'percentual_desconto',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('contratos', [
            'tipo_desconto',
            'valor_desconto_fixo',
        ]);
    }
}
