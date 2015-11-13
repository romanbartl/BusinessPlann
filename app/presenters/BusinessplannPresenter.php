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

	public function renderDefault() {
		$this->template->name = 
			$this->getUser()->identity->data['name'] . " " . $this->getUser()->identity->data['surname'];
		$this->template->email = $this->getUser()->identity->data['email'];
	}

	public function actionDefault() {
	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}
}
