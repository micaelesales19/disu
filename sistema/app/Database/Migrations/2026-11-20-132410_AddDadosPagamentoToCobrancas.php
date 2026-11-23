<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDadosPagamentoToCobrancas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cobrancas', [
            'forma_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'valor_pago',
            ],
            'origem_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'forma_pagamento',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cobrancas', [
            'forma_pagamento',
            'origem_pagamento',
        ]);
    }
}
