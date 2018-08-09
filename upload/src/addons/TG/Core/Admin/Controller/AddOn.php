<?php

namespace TG\Core\Admin\Controller;

use XF\Mvc\ParameterBag;

use XF\Admin\Controller\AbstractController;

class AddOn extends AbstractController
{
	public function actionIndex()
	{
		$addOns = [];

		foreach ($this->getAddOnManager()->getAllAddOns() as $id => $addOn)
		{
			if (substr($id, 0, 3) == 'TG/')
			{
				$addOns[$id] = $addOn;
			}
		}

		return $this->view('TG\Core:AddOn\Listing', 'tgcore_addon_list', [
			'addOns' => $addOns
		]);
	}

	public function actionUpgrade(ParameterBag $params)
	{
		$addOn = $this->assertAddOnAvailable($params->addon_id_url);
		if (!$addOn->canUpgrade())
		{
			return $this->error(\XF::phrase('this_add_on_cannot_be_upgraded'));
		}

		$addOnRepo = $this->getAddOnRepo();

		if (!$addOnRepo->checkAddonUpgrade())
		{
			return $this->error(\XF::phrase('this_add_on_cannot_be_upgraded'));
		}

		if (!$this->request->exists('install'))
		{
			$addOnInfo = $addOnRepo->getLastVersionInfo($addOn);
			return $this->view('TG\Core:Upgrade', 'tgc_addon_upgrade', [
				'info' => $addOnInfo
			]);
		}

		// $addOn->active = false;
		// $addOn->save();
	}

	/**
	 * @param string $id
	 *
	 * @return \XF\AddOn\AddOn
	 */
	protected function assertAddOnAvailable($id)
	{
		$id = $this->getAddOnRepo()->convertAddOnIdUrlVersionToBase($id);

		$addOn = $this->getAddOnManager()->getById($id);
		if (!$addOn)
		{
			throw $this->exception($this->error(\XF::phrase('requested_page_not_found'), 404));
		}

		return $addOn;
	}

	/**
	 * @return \XF\AddOn\Manager
	 */
	protected function getAddOnManager()
	{
		return $this->app->addOnManager();
	}

	/**
	 * @return \TG\Core\Repository\AddOn
	 */
	protected function getAddOnRepo()
	{
		return $this->repository('TG\Core:AddOn');
	}
}