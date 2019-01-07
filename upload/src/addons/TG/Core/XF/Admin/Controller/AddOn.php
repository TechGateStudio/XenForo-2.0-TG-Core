<?php

namespace TG\Core\XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class AddOn extends XFCP_AddOn
{
    public function actionIndex()
	{
        $options = \XF::options();
        if ($options->visableTGAddOns)
        {
            return parent::actionIndex();
        }
        
		$upgradeable = [];
		$installed = [];
		$installable = [];
		$legacy = [];
		$skippable = [];

		foreach ($this->getAddOnManager()->getAllAddOns() AS $id => $addOn)
		{
            if (\TG\Core::canTGAddOn($addOn))
            {
                continue;
            }
            
			if (isset($skippable[$id]))
			{
				continue;
			}

			if ($addOn->canUpgrade())
			{
				$skip = $addOn->legacy_addon_id;
				if ($skip)
				{
					$skippable[$skip] = $skip;
					unset($legacy[$skip]);
				}
				$upgradeable[$id] = $addOn;
			}
			else if ($addOn->isLegacy())
			{
				$legacy[$id] = $addOn;
			}
			else if ($addOn->isInstalled())
			{
				$installed[$id] = $addOn;
			}
			else if ($addOn->canInstall())
			{
				$installable[$id] = $addOn;
			}
		}

		$addOnRepo = $this->getAddOnRepo();

		$viewParams = [
			'upgradeable' => $upgradeable,
			'installed' => $installed,
			'installable' => $installable,
			'legacy' => $legacy,
			'total' => count($upgradeable) + count($installed) + count($installable) + count($legacy),
			'disabled' => $addOnRepo->getDisabledAddOnsCache(),
			'hasProcessing' => $addOnRepo->hasAddOnsBeingProcessed()
		];
		return $this->view('XF:AddOn\Listing', 'addon_list', $viewParams);
	}
    
    public function actionToggle(ParameterBag $params)
    {
        $reply = parent::actionToggle($params);
        
        $addOn = $this->assertAddOnAvailable($params->addon_id_url);
        if ($reply instanceof \XF\Mvc\Reply\Redirect && substr($addOn->addon_id, 0, 3) == 'TG/')
        {
            return $this->redirect($this->buildLink('tgc-addons'));
        }
        
        return $reply;
    }
    
    public function actionFinalize(ParameterBag $params)
    {
        $reply = parent::actionFinalize($params);
        
        $addOn = $this->assertAddOnAvailable($params->addon_id_url);
        if ($reply instanceof \XF\Mvc\Reply\Redirect && substr($addOn->addon_id, 0, 3) == 'TG/')
        {
            return $this->redirect($this->buildLink('tgc-addons'));
        }
        
        return $reply;
    }
}