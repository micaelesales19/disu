<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServicos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'codigo_interno' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],

            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],

            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'valor_padrao' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],

            'unidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'servico',
            ],

            'permite_nfse' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],

            'codigo_fiscal' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codigo_interno');
        $this->forge->addKey('nome');
        $this->forge->addKey('ativo');

        $this->forge->createTable('servicos', true);
    }

    public function down()
    {
        $this->forge->dropTable('servicos', true);
    }
}
