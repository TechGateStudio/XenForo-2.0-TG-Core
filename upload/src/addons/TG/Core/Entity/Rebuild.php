<?php

namespace TG\Core\Entity;

use XF\Mvc\Entity\Structure;

/**
 * @property mixed|null class
 */
class Rebuild extends Entity
{
    /**
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		return \XF::phrase('tgc_rebuild_title.' . $this->rebuild_id)->render();
	}
    
    /**
	 * @return \XF\Phrase|string
	 */
	public function getDescription()
	{
		return \XF::phrase('tgc_rebuild_description.' . $this->rebuild_id)->render();
	}
    
    public function getMasterTitlePhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return 'tgc_rebuild_title.' . $this->rebuild_id; });
			$phrase->language_id = 0;
		}
        $phrase->addon_id = $this->addon_id;

		return $phrase;
	}
    
    public function getMasterDescriptionPhrase()
	{
		$phrase = $this->MasterDescription;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->title = $this->_getDeferredValue(function() { return 'tgc_rebuild_description.' . $this->rebuild_id; });
			$phrase->language_id = 0;
		}
        $phrase->addon_id = $this->addon_id;

		return $phrase;
	}
    
    public function getTemplateRender()
    {
        $template = $this->template;
        
        $data = $this->app()
            ->templateCompiler()
            ->compile($this->template);
          
        $data = eval($data);

        $viewParams = [];
        $class = \XF::stringToClass($this->class, '%s\Job\%s');
        if (method_exists($class, 'rebuildViewParams'))
        {
            $viewParams = $class::rebuildViewParams();
            if (!is_array($viewParams))
            {
                $viewParams = [];
            }
        }

        return $data['code']($this->app()->templater(), array_merge($viewParams, [
            'xf' => $this->app()->getGlobalTemplateData()
        ]));
    }
    
    protected function verifyClass(&$class)
	{
        $_class = \XF::stringToClass($class, '%s\Job\%s');
		if (class_exists($_class))
		{
			return true;
		}
        
        $this->error(\XF::phrase('invalid_class_x', ['class' => $class]));
		return false;
	}
    
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tgc_rebuild';
		$structure->shortName = 'TG\Core:Rebuild';
		$structure->primaryKey = 'rebuild_id';
		$structure->columns = [
			'rebuild_id'    => ['type' => self::STR, 'maxLength' => 50],
            'class'         => ['type' => self::STR, 'required' => 'please_enter_valid_callback_class'],
            'template'      => ['type' => self::STR, 'default' => ''],
            'date'          => ['type' => self::UINT, 'nullable' => true],
			'addon_id'      => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];

        $structure->getters = [
            'template_render'   => true,
            'title'             => true,
            'description'       => true
        ];
        
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
            'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'tgc_rebuild_title.', '$rebuild_id']
				]
			],
            'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'tgc_rebuild_description.', '$rebuild_id']
				]
			]
		];
        
        $structure->behaviors = [
			'XF:DevOutputWritable' => []
		];

		return $structure;
	}
}