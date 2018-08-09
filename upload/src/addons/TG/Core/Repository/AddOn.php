<?php

namespace TG\Core\Repository;

class AddOn extends \XF\Repository\AddOn
{
	public function findAddOnsForList()
	{
		return parent::findAddOnsForList()->where('addon_id', 'like', '%TG/%');
	}

	public function checkUpgrade(\XF\AddOn $addOn)
	{
		$version = $addOn->getLastVersionInfo();
		$lastVersionInfo = $this->getLastVersionInfo($addOn);

		return $lastVersionInfo['version_id'] > $version['version_id'];
	}

	public function isValidUpgrade($addOn, &$errors = [])
	{
		$lastVersionInfo = $this->getLastVersionInfo();
		$addOnManagerRepo = $this->getAddOnManager();

		foreach($lastVersionInfo['dependencies'] as $dependence)
		{
			$id = $this->convertAddOnIdUrlVersionToBase($dependence[0]);
			$addOn = $addOnManagerRepo->getById($id);

			if (!$addOn)
			{
				$errors[] = \XF::phrase('tgc_addon_dependence_not_found', [
					'title' => $dependence[0],
					'version' => $dependence[1]
				]);

				continue;
			}

			if ($addOn->getLastVersionInfo()['version_id'] < $dependence[0])
			{
				$errors[] = \XF::phrase('tgc_addon_dependence_version_invalid', [
					'title' => $addOn->title,
					'version' => $addOn->version_string
				]);
			}
		}

		return count($errors) == 0;
	}

	public function getLastVersionInfo(\XF\AddOn $addOn)
	{
		return [
			'version_id' => 1000011,
			'version_string' => '1.0.0 Alpha 1',
			'download_url' => '',
			'title' => '[TG] Core',
			'image_url' => '',
			'description' => '',
			'upgrade_description' => '',
			'dependencies' => []
		];
	}

		/**
	 * @return \XF\AddOn\Manager
	 */
	protected function getAddOnManager()
	{
		return $this->app->addOnManager();
	}
}