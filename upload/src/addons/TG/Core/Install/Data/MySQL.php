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
				$table->dropColumns(['tgc_gender']);
			}
		];

        $data['xf_tgc_rebuild'] = [
			'import' => true,
			'drop' => true,
			'create' => function(Create $table)
			{
				$table->addColumn('rebuild_id', 'varchar', 50);
                $table->addColumn('class', 'varchar', 50);
                $table->addColumn('template', 'mediumtext')->comment('User-editable HTML and template syntax');;
                $table->addColumn('date', 'int')->nullable();
                $table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			},
            'query' => 'INSERT INTO `xf_tgc_rebuild` (`rebuild_id`, `class`, `template`, `date`, `addon_id`) VALUES
(\'tgc_test\', \'TG\\Core:ExampleRebuild\', \'<div class=\"block-row\">\n	Test custom variables from example ExampleRebuild -> $test: {$test}\n</div>\n<div class=\"block-row\">\n	Warning: This rebuild not jobing!\n</div>\', 1542938525, 0x54472f436f7265);'
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