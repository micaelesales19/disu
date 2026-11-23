<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContratos extends Migration
{
    public function up()
    {
        // CONTRATOS
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => [
                    'mensal',
                    'trimestral',
                    'anual',
                    'pontual',
                ],
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'dia_vencimento' => [
                'type' => 'TINYINT',
                'unsigned' => true,
            ],
            'forma_pagamento' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'data_inicio' => [
                'type' => 'DATE',
            ],
            'data_termino' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'prazo_indeterminado' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'decimo_terceiro' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'mover_fim_semana' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
            'emitir_nfse' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'momento_nfse' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'reajuste_automatico' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'percentual_reajuste' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'data_proximo_reajuste' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'desconto_antecipacao' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'percentual_desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'dias_antecipacao' => [
                'type' => 'INT',
                'null' => true,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'situacao' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'ativo',
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
        $this->forge->addKey('cliente_id');

        $this->forge->addForeignKey(
            'cliente_id',
            'clientes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('contratos', true);

        // SERVIÇOS VINCULADOS AO CONTRATO
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'contrato_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'servico_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'quantidade' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 1,
            ],
            'valor_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('contrato_id');
        $this->forge->addKey('servico_id');

        $this->forge->addForeignKey(
            'contrato_id',
            'contratos',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->addForeignKey(
            'servico_id',
            'servicos',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('contrato_servicos', true);
    }

    public function down()
    {
        $this->forge->dropTable('contrato_servicos', true);
        $this->forge->dropTable('contratos', true);
    }
}
