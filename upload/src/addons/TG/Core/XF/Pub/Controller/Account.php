<?php

namespace TG\Core\XF\Pub\Controller;

class Account extends XFCP_Account
{
	protected function accountDetailsSaveProcess(\XF\Entity\User $user)
	{
		$form = parent::accountDetailsSaveProcess($user);
		
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