<?php

namespace App\Presenters;

use Nette,
    Nette\Security\User,
    App\Model\BusinessplannModel;


/**
 * 
 */
class BusinessplannPresenter extends BasePresenter
{
	private $plannerModel;

	public function __construct(BusinessplannModel $plannerModel) {
        parent::__construct();
        $this->plannerModel = $plannerModel;
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
