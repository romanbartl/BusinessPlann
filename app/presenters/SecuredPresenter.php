<?php 

namespace App\Presenters;

abstract class SecuredPresenter extends BasePresenter
{
	public function startup()
	{
		parent::startup();

		if (!$this->user->isLoggedIn()) {
			$this->redirect('Home:default');
		}
	}
}