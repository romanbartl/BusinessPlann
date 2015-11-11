<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    Nette\Security\User;

/**
 * 
 */

class SettingsPresenter extends BasePresenter
{
	public function renderUser() {
		
	}

	public function actionDefault() {
		if(isset($_GET['logout']))
	    	$this->getUser()->logout();

	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}

}