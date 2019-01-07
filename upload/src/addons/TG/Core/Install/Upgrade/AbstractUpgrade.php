<?php

namespace TG\Core\Install\Upgrade;

use TG\Core\Install\Manager;
use XF\Db\Schema\Alter;

abstract class AbstractUpgrade
{
	protected $manager;
	protected $app;
	
	public function __construct(Manager $manager, \XF\App $app)
	{
		$this->manager = $manager;
		$this->app = $app;
	}
	
	protected function getManager()
	{
		return $this->manager;
	}
	
	protected function importTable($dataKey, $tableName = null, $query = true)
	{
		$this->manager->importTable($dataKey, $tableName, $query);
	}
    
	protected function dropTables($tables)
	{
		if (is_array($tables))
        {
            foreach ($tables as $table)
            {
                $this->dropTables($table);
            }
            
            return;
        }
        
        $this->schemaManager()->dropTable($tables);
	}
    
    public function dropColumns($table, array $columns)
    {
        $dropColumns = [];
        array_map(function($column) use ($table, &$dropColumns)
        {
            if ($this->columnExists($table, $column))
            {
                $dropColumns[] = $column;
            }
        }, $columns);
        
        $this->schemaManager()->alterTable($table, function(Alter $table) use ($dropColumns)
        {
            $table->dropColumns($dropColumns);
        });
    }
    
    public function columnExists($tableName, $column, &$definition = null)
    {
        return $this->schemaManager()->columnExists($tableName, $column, $definition);
    }
	
	protected function query($query)
	{
		$this->db()->query($query);
	}
    
    public function alterTable($tableId)
    {
        $this->manager->alterTable($tableId);
    }
	
	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	protected function db()
	{
		return $this->app->db();
	}

	/**
	 * @return \XF\Db\SchemaManager
	 */
	protected function schemaManager()
	{
		return $this->db()->getSchemaManager();
	}
}