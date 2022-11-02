<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableLancamentos extends Migration
{
	public function up()
	{

		$this->forge->addField('id');
		$this->forge->addField([
			'descricao' => [
				'type' 			=> 'VARCHAR',
				'constraint'	=> 255
			],
			'chave' => [
				'type' 			=> 'VARCHAR',
				'constraint'			=> 255
			],
			'usuarios_id' => [
				'type' 			=> 'INT',
				'constraint' 	=> 9
			],
			'categorias_id' => [
				'type' 			=> 'INT',
				'constraint' 	=> 9
			],
			'valor' => [
				'type'			=> 'DECIMAL(8,2)',
				'unsigned'		=> TRUE
			],
			'data' => [
				'type'			=> 'DATE'
			],
			'notificar_por_email' => [
				'type'			=> 'TINYINT(1)',
				'default'		=> 2,
				'comment'		=> 'Indica se será enviado um email de notificação quando o lançamento vencer. 1 => SIM; 2 => NÃO'
			],
			'consolidado' => [
				'type'			=> 'TINYINT(1)',
				'default'		=> 2,
				'comment'		=> 'Indica se o lançamento entrará nos cálculos de saldo. 1 => SIM; 2 => NÃO'
			],
			'created_at' => [
				'type' 			=> 'DATETIME',
				'null'			=> TRUE,
			],
			'updated_at' => [
				'type' 			=> 'DATETIME',
				'null'			=> TRUE,
			],
			'deleted_at' => [
				'type' 			=> 'DATETIME',
				'null'			=> TRUE,
			]
		]);
		$this->forge
			->addKey('chave')
			->addForeignKey('categorias_id', 'categorias', 'id', 'NO ACTION', 'CASCADE')
			->addForeignKey('usuarios_id', 'usuarios', 'id', 'NO ACTION', 'CASCADE')
			->createTable('lancamentos');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('lancamentos');
	}
}
