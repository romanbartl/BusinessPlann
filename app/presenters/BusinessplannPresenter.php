<?php

namespace App\Presenters;

use Nette,
    Nette\Security\User,
    Nette\Application\UI\Presenter,
	Nette\Application\UI\Form,
    App\Model\BusinessplannManager,
    App\Model\LabelsManager;


/**
 * 
 */
class BusinessplannPresenter extends BasePresenter
{
	private $plannerManager;
	private $labelsManager;

	public function __construct(BusinessplannManager $plannerManager, LabelsManager $labelsManager) {
        parent::__construct();
        $this->plannerManager = $plannerManager;
        $this->labelsManager = $labelsManager;
    }

	public function actionDefault() {
	    if (!$this->getUser()->isLoggedIn()) 
	        $this->redirect('Home:default');
	    
	    $this->template->labels = $this->labelsManager->getLabels();
	}
}
