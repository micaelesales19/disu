<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConfiguracoesReajusteToContratos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contratos', [
            'tipo_reajuste' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'reajuste_automatico',
            ],
            'periodicidade_reajuste' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tipo_reajuste',
            ],
            'aplicacao_reajuste' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'periodicidade_reajuste',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('contratos', [
            'tipo_reajuste',
            'periodicidade_reajuste',
            'aplicacao_reajuste',
        ]);
    }
}
