<?php

namespace App\Presenters;

use Nette,
    Nette\Security\User,
    App\Model\BusinessplannManager;


/**
 * 
 */
class BusinessplannPresenter extends BasePresenter
{
	private $plannerManager;

	public function __construct(BusinessplannManager $plannerManager) {
        parent::__construct();
        $this->plannerManager = $plannerManager;
    }

	public function actionDefault() {
	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}
}
