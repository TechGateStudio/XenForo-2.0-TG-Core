<?php

namespace TG\Core\Install\Data;

use XF\Db\Schema\Create;
use XF\Db\Schema\Alter;

class MySQL
{
	public function getData(&$data = [])
	{
		$data['xf_user'] = [
			'import' => true,
			'drop' => true,
			'alter' => function(Alter $table)
			{
				$table->addColumn('tgc_gender', 'enum')->values(['none', 'male', 'female'])->setDefault('none');
			},
			'alter_drop' => function(Alter $table)
			{
				$table->dropColumn('tgc_gender');
			}
		];

		$data['table_name'] = [
			'import' => false, // Автоматический импорт при установке.
			'drop' => false, // Автоматическое удаление при удаление.
			'create' => function(Create $table)
			{
				$table->addColumn('id', 'int')->autoIncrement();
			},
			'query' => "",
			'alter' => function(Alter $table)
			{
				$table->addColumn('useraname', 'varchar', '100');
			},
			'alter_drop' => function(Alter $table)
			{
				$table->dropColumns(['username']);
			}
		];
	}
}