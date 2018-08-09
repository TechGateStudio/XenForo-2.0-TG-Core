<?php

namespace TG\Core\Install;

abstract class AbstractSetup extends \XF\AddOn\AbstractSetup
{
	protected $manager;

	public function __construct(\XF\AddOn\AddOn $addOn, \XF\App $app)
	{
		parent::__construct($addOn, $app);

		$this->manager = new Manager($addOn, $app);
	}

	protected function _preInstall()
	{
		
	}
	
	protected function _postInstall()
	{
		
	}
	
	protected function _preUpgrade()
	{
		
	}
	
	protected function _postUpgrade()
	{
		
	}
	
	protected function _preUninstall()
	{
		
	}
	
	protected function _postUninstall()
	{
		
	}

	public function install(array $stepParams = [])
	{
		$this->_preInstall();

		$mySQLData = $this->getMySQLData();

		foreach($mySQLData as $tableId => $data)
		{
			if (!isset($data['import']) || $data['import'])
			{
				if (isset($data['create']))
				{
					$this->importTable($tableId, $tableId, true);
				}
				else
				{
					$this->alterTable($tableId);
				}
			}
		}

		$this->_postInstall();
	}

	public function upgrade(array $stepParams = [])
	{
		$this->_preUpgrade();

		$currentVersion = $this->manager->getCurrentVersion();
		$nextVersionIds = $this->manager->getRemainingUpgradeVersionIds($currentVersion);

		foreach ($nextVersionIds AS $versionId => $file)
		{
			$upgrade = $this->manager->getUpgrade($versionId);
			
			for ($i = 1; true; $i++)
			{
				$step = 'step' . $i;
				
				if (method_exists($upgrade, $step))
			    {
					$upgrade->$step();
					
					continue;
				}
				
				break;
			}
		}

		$this->_postUpgrade();
	}

	public function uninstall(array $stepParams = [])
	{
		$this->_preUninstall();

		$mySQLData = $this->getMySQLData();
		$sm = $this->db()->getSchemaManager();
		foreach($mySQLData as $tableId => $data)
		{
			if (!isset($data['drop']) || $data['drop'])
			{
				if (isset($data['create']))
				{
					$sm->dropTable($tableId);
				}
				elseif (isset($data['alter_drop']))
				{
					$sm->alterTable($tableId, $data['alter_drop']);
				}
			}
		}

		$this->_postUninstall();
	}

	public function getMySQLData()
	{
		return $this->manager->getMySQLData();
	}

	public function importTable($tableId, $tableName = null, $query = true)
	{
		$this->manager->importTable($tableId, $tableName, $query);
	}

	public function alterTable($tableId)
	{
		$this->manager->alterTable($tableId);
	}
}