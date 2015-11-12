<?php

namespace App\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	public function actionOut() {
		if(isset($_GET['logout']))
	    	$this->getUser()->logout();
	   	$this->redirect("Home:default");
	}

}
