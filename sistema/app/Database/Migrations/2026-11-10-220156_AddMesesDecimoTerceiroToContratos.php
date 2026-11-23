<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMesesDecimoTerceiroToContratos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('contratos', [
            'meses_decimo_terceiro' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'decimo_terceiro',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(
            'contratos',
            'meses_decimo_terceiro'
        );
    }
}
