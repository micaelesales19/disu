<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleAccounts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'google_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'google_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 254,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('google_id');
        $this->forge->addUniqueKey('user_id');

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('google_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('google_accounts');
    }
}
