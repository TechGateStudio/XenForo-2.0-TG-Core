<?php

namespace TG\Core\Admin\Controller;

use XF\Mvc\ParameterBag;

class Rebuild extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		if ($params->rebuild_id)
        {
            return $this->reroute(__CLASS__, 'edit', $params);
        }
        
        $rebuilds = $this->finder('TG\Core:Rebuild')
            ->fetch();
            
        return $this->view('TG\Core:Rebuild\Listing', 'tgc_rebuild_list', [
            'rebuilds' => $rebuilds
        ]);
	}
    
    protected function rebuildAddEdit(\TG\Core\Entity\Rebuild $rebuild)
    {
        return $this->view('TG\Core:Rebuild\Edit', 'tgc_rebuild_edit', [
            'rebuild' => $rebuild
        ]);
    }
    
    public function actionEdit(ParameterBag $params)
    {
        $rebuild = $this->assertRebuildExists($params->rebuild_id);
        return $this->rebuildAddEdit($rebuild);
    }
    
    
    public function actionAdd()
    {
        $rebuild = $this->em()->create('TG\Core:Rebuild');
        return $this->rebuildAddEdit($rebuild);
    }
    
    protected function rebuildSaveProcess(\TG\Core\Entity\Rebuild $rebuild)
    {
        $form = $this->formAction();
        
        $input = $this->filter([
            'rebuild_id'    => 'str',
            'class'         => 'str',
            'template'      => 'str',
            'addon_id'      => 'str'
        ]);
        
        $form->basicEntitySave($rebuild, $input);
        
        $extraInput = $this->filter([
			'title'         => 'str',
            'description'   => 'str'
		]);
        $form->apply(function() use ($extraInput, $rebuild)
		{
			$title = $rebuild->getMasterTitlePhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();
            
            $description = $rebuild->getMasterDescriptionPhrase();
			$description->phrase_text = $extraInput['description'];
			$description->save();
		});
        
        return $form;
    }
    
    public function actionSave(ParameterBag $params)
    {
        if ($params->rebuild_id)
        {
            $rebuild = $this->assertRebuildExists($params->rebuild_id);
        }
        else
        {
            $rebuild = $this->em()->create('TG\Core:Rebuild');
        }
        
        $this->rebuildSaveProcess($rebuild)->run();
        
        return $this->redirectSaveOrExit('tgc-rebuilds', $rebuild);
    }
    
    public function actionDelete(ParameterBag $params)
    {
        $rebuild = $this->assertRebuildExists($params->rebuild_id);
        if ($this->isPost())
        {
            $rebuild->delete();
            return $this->redirect($this->buildLink('tgc-rebuilds'));
        }
        
        return $this->queryConfirmDelete($rebuild, 'tgc-rebuilds');
    }
    
    protected function assertRebuildExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('TG\Core:Rebuild', $id, $with, $phraseKey);
	}
}