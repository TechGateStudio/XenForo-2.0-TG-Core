<?php

namespace TG\Core\XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Route extends XFCP_Route
{
	public function actionSave(ParameterBag $params)
	{
        if ($this->request->exists('exit'))
        {
            return parent::actionSave($params);
        }
        
		$this->assertPostOnly();

		if ($params['route_id'])
		{
			$route = $this->assertRouteExists($params['route_id']);
		}
		else
		{
			$route = $this->em()->create('XF:Route');
		}

		$this->routeSaveProcess($route)->run();
        
        return $this->redirect($this->buildLink('routes/edit', $route));
	}
}