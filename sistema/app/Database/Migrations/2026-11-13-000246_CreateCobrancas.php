<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCobrancas extends Migration
{
    public function up()
    {
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
            'contrato_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'vencimento' => [
                'type' => 'DATE',
            ],
            'competencia' => [
                'type' => 'CHAR',
                'constraint' => 7,
                'null' => true,
                'comment' => 'Formato YYYY-MM',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'em_aberto',
            ],
            'origem' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'avulsa',
            ],
            'pago_em' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'valor_pago' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'boleto_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'boleto_linha_digitavel' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'boleto_codigo_barras' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'boleto_pdf_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'link_pagamento' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pix_copia_cola' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addPrimaryKey('id');

        $this->forge->addKey('cliente_id');
        $this->forge->addKey('contrato_id');
        $this->forge->addKey('vencimento');
        $this->forge->addKey('status');

        // Impede gerar duas cobranças da mesma competência
        // para o mesmo contrato.
        // Cobranças avulsas podem ter contrato_id NULL.
        $this->forge->addUniqueKey(
            ['contrato_id', 'competencia'],
            'uq_cobranca_contrato_competencia'
        );

        $this->forge->createTable('cobrancas');
    }

    public function down()
    {
        $this->forge->dropTable('cobrancas');
    }
}
