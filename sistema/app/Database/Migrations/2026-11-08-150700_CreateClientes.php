<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientes extends Migration
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

            // IDENTIFICAÇÃO
            'codigo_interno' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'cpf_cnpj' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'nome_fantasia' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'razao_social' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'contador_responsavel' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ativo' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'vip' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'data_entrada' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'data_saida' => [
                'type' => 'DATE',
                'null' => true,
            ],

            // CONTATO
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'email_cobranca' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'whatsapp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],

            // ENDEREÇO
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'null'       => true,
            ],
            'logradouro' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'complemento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'uf' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
            ],
            'observacoes_internas' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // DADOS FISCAIS
            'regime_tributario' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'inscricao_municipal' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'inscricao_estadual' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'capital_social' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'plano_empresa' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'possui_funcionarios' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'possui_extrato_bancario' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'importar_notas_sieg' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'informacoes_fiscais' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // DADOS CONTÁBEIS E DP
            'dados_contabeis' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dados_dp' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // DOCUMENTOS
            'url_contrato_social' => [
                'type'       => 'VARCHAR',
                'constraint' => 2048,
                'null'       => true,
            ],
            'url_cartao_cnpj' => [
                'type'       => 'VARCHAR',
                'constraint' => 2048,
                'null'       => true,
            ],

            // CONTROLE
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

        $this->forge->addUniqueKey('cpf_cnpj');
        $this->forge->addUniqueKey('codigo_interno');

        $this->forge->addKey('ativo');
        $this->forge->addKey('vip');
        $this->forge->addKey('regime_tributario');
        $this->forge->addKey('cidade');

        $this->forge->createTable('clientes', true);
    }

    public function down()
    {
        $this->forge->dropTable('clientes', true);
    }
}
