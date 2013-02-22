<?php

namespace Fuel\Migrations;

class Create_users
{
	public function up()
	{
		$table = \Config::get('simpleauth.table_name', 'users');

		\DBUtil::create_table($table, array(
			'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
			'username' => array('type' => 'varchar', 'constraint' => 50),
			'password' => array('type' => 'varchar', 'constraint' => 255),
			'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
			'email' => array('type' => 'varchar', 'constraint' => 255),
			'last_login' => array('type' => 'varchar', 'constraint' => 25),
			'login_hash' => array('type' => 'varchar', 'constraint' => 255),
			'profile_fields' => array('type' => 'text'),
			'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
		), array('id'));

		\DBUtil::create_index('users', array('username', 'email'), 'username', 'UNIQUE');
	}

	public function down()
	{
		$table = \Config::get('simpleauth.table_name', 'users');

		\DBUtil::drop_table($table);
	}
}