<?php

namespace TG\Core\XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class PermissionDefinition extends XFCP_PermissionDefinition
{
	public function actionIndex()
	{
		$reply = parent::actionIndex();

		$currentAddOn = null;
		$addOnId = $this->filter('addon_id', 'str');
		if ($addOnId)
		{
			$currentAddOn = $this->em()->find('XF:AddOn', $addOnId);
		}

		$linkParams = [
			'addon_id' => $currentAddOn ? $currentAddOn->addon_id : null
		];

		$reply->setParams([
			'currentAddOn' => $currentAddOn,
			'addOns' => \XF::repository('XF:AddOn')
                ->findAddOnsForList()
                ->fetch(),
			'linkParams' => $linkParams
		]);

		if ($currentAddOn)
		{
			$permissionGrouped = [];
			foreach ($reply->getParam('permissionsGrouped') as $groupId => $group)
			{
				foreach ($group as $permissionId => $permission)
				{
					if ($permission->addon_id == $currentAddOn->addon_id)
					{
						$permissionGrouped[$groupId][$permissionId] = $permission;
					}
				}
			}

			$reply->setParam('permissionsGrouped', $permissionGrouped);

			$interfaceGroups = [];
			foreach($reply->getParam('interfaceGroups') as $groupId => $group)
			{
				if ($group->addon_id == $currentAddOn->addon_id)
				{
					$interfaceGroups[$groupId] = $group;
				}
			}

			$reply->setParam('interfaceGroups', \TG\Core::newArrayCollection($interfaceGroups));
		}

		return $reply;
	}

	protected function permissionAddEdit(\XF\Entity\Permission $permission)
	{
		$reply = parent::permissionAddEdit($permission);

		$reply->setParam('redirect', $this->getDynamicRedirect());

		return $reply;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['permission_group_id'] || $params['permission_id'])
		{
			$permission = $this->assertPermissionExists($params['permission_group_id'], $params['permission_id']);
		}
		else
		{
			$permission = $this->em()->create('XF:Permission');
		}

		$this->permissionSaveProcess($permission)->run();

		$dynamicRedirect = $this->getDynamicRedirect('invalid', false);
		if ($dynamicRedirect == 'invalid' || !preg_match('#(permission-definitions)/#', $dynamicRedirect))
		{
			$dynamicRedirect = null;
		}

		if ($dynamicRedirect)
		{
			$redirect = $dynamicRedirect;
		}
		else
		{
			$redirect = $this->buildLink('permission-definitions');
		}
		
		return $this->redirect(
			$redirect
			. $this->buildLinkHash("{$permission->permission_group_id}_{$permission->permission_id}")
		);
	}
}