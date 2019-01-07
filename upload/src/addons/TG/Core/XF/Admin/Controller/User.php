<?php

namespace TG\Core\XF\Admin\Controller;

class User extends XFCP_User
{
	protected function userSaveProcess(\XF\Entity\User $user)
	{
		$form = parent::userSaveProcess($user);
		
		$input = $this->filter([
			'tgc_gender' => 'str'
		]);
			
		if (!$input['tgc_gender'])
		{
			$input['tgc_gender'] = 'none';
		}
		
		$form->basicEntitySave($user, $input);
		
		return $form;
	}
}