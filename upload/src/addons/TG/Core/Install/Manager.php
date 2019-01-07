<?php

namespace TG\Core\Install;

class Manager
{
	protected $addOn;
	protected $app;
	protected $db;

	protected $mySQLData;

    protected $currentVersion;
    
	public function __construct(\XF\AddOn\AddOn $addOn, \XF\App $app)
	{
		$this->addOn = $addOn;
		$this->app = $app;
		$this->db = $app->db();

		$this->getMySQLData();
	}

	public function getMySQLData()
	{
		if ($this->mySQLData === null)
		{
			$class = '/' . $this->addOn->addon_id . '/Install/Data/MySQL';
			$class = str_replace('/', '\\', $class);
			$class = \XF::extendClass($class);
			$class = new $class;
			$class->getData($this->mySQLData);
		}

		return $this->mySQLData;
	}

	public function importTable($tableId, $tableName = null, $query = true)
	{
		if (!isset($this->mySQLData[$tableId]))
		{
			return;
		}
		
		$data = $this->mySQLData[$tableId];

		if ($tableName === null)
		{
			$tableName = $tableId;
		}

		if (!isset($data['create']))
		{
			return;
		}

		$sm = $this->db->getSchemaManager();
		$sm->createTable($tableName, $data['create']);

		if ($query && isset($data['query']))
		{
			$query = str_ireplace('[table]', $tableName, $data['query']);
			$this->db->query($query);
		}
	}

	public function alterTable($tableId)
	{
		if (!isset($this->mySQLData[$tableId]))
		{
			return;
		}
		
		$data = $this->mySQLData[$tableId];

		if (isset($data['alter']))
		{
			$sm = $this->db->getSchemaManager();
			$sm->alterTable($tableId, $data['alter']);
		}
	}

	public function getPossibleUpgradeFileNames()
	{
		$searchDir = \XF::getAddOnDirectory() . '/' . $this->addOn->addon_id . '/Install/Upgrade';

		$upgrades = [];
		foreach (glob($searchDir . '/*.php') AS $file)
		{
			$file = basename($file);

			$versionId = intval($file);
			if (!$versionId)
			{
				continue;
			}

			$upgrades[$versionId] = $searchDir . '/' . $file;
		}

		ksort($upgrades, SORT_NUMERIC);

		return $upgrades;
	}

	public function getRemainingUpgradeVersionIds($lastCompletedVersion)
	{
		$upgrades = $this->getPossibleUpgradeFileNames();
		$offset = 0;

		foreach ($upgrades AS $upgrade => $file)
		{
			if ($upgrade > $lastCompletedVersion)
			{
				return array_slice($upgrades, $offset, null, true);
			}

			$offset++;
		}

		return [];
	}

	public function getNextUpgradeVersionId($lastCompletedVersion)
	{
		$upgrades = $this->getRemainingUpgradeVersionIds($lastCompletedVersion);
		reset($upgrades);
		return key($upgrades);
	}

	public function getNewestUpgradeVersionId()
	{
		$upgrades = $this->getRemainingUpgradeVersionIds(0);
		end($upgrades);
		return key($upgrades);
	}

	/**
	 * @param integer $versionId
	 * @param App $app
	 *
	 * @return AbstractUpgrade
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getUpgrade($versionId)
	{
		$versionId = intval($versionId);
		if (!$versionId)
		{
			throw new \InvalidArgumentException('No upgrade version ID specified.');
		}

		$upgrades = $this->getPossibleUpgradeFileNames();
		if (isset($upgrades[$versionId]))
		{
			require_once($upgrades[$versionId]);
			$class = '\\' . $this->addOn->addon_id . '\\Install\\Upgrade\\Upgrade' . $versionId;
            $class = str_ireplace('/', '\\', $class);
			return new $class($this, $this->app);
		}

		throw new \InvalidArgumentException('Could not find the specified upgrade.');
	}

	public function getCurrentVersion()
	{
		if ($this->currentVersion === null)
		{
			$existingVersion = $this->db->fetchOne("
				SELECT version_id
				FROM xf_addon
				WHERE addon_id = '{$this->addOn->addon_id}'
			");

			$this->currentVersion = $existingVersion ? $existingVersion : 0;
		}

		return $this->currentVersion;
	}
}