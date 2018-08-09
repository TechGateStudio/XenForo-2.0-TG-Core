<?php

namespace TG\Core\XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Route extends XFCP_Route
{
	public function actionSave(ParameterBag $params)
	{
		$action = parent::actionSave($params);

		$route = $this->assertRouteExists($params['route_id']);

		return $this->redirectSaveOrExit('routes', $route);
	}

	protected function redirectSaveOrExit($route, $entity)
	{
		if ($this->request->exists('exit'))
		{
			$redirect = $this->buildLink($route) . $this->buildLinkHash($entity->getEntityId());
		}
		else
		{
			$redirect = $this->buildLink($route . '/edit', $entity);
		}

		return $this->redirect($redirect);
	}
}