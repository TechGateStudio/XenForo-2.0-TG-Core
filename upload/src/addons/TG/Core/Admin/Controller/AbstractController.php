<?php

namespace TG\Core\Admin\Controller;

class AbstractController extends \XF\Admin\Controller\AbstractController
{
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
    
    protected function queryConfirmDelete(\XF\Mvc\Entity\Entity $entity, $route, $title = 'title')
    {
        return $this->view('TG\Core:Delete\View', 'tgc___delete', [
            'entity'        => $entity,
            'delete_url'    => $this->buildLink($route . '/delete', $entity),
            'edit_url'      => $this->buildLink($route . '/edit', $entity),
            'title'         => $entity->get($title)
        ]);
    }
}