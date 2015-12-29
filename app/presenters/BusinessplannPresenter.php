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

    private $includeView;

	public function __construct(BusinessplannManager $plannerManager, LabelsManager $labelsManager) {
        parent::__construct();
        $this->plannerManager = $plannerManager;
        $this->labelsManager = $labelsManager;
    }

    public function renderDefault() {
    	$this->template->events = $this->plannerManager->getEvents(); 
        if($this->includeView === NULL)
            $this->includeView = 'agenda'; 
        $this->template->includeView = $this->includeView;
    }

    public function handleChangeView($view){
        $this->includeView = $view;
        
        if($this->isAjax()) {
            $this->redrawControl('eventsView');
        }
    }

	public function actionDefault() {
	    if (!$this->getUser()->isLoggedIn()) 
	        $this->redirect('Home:default');
	    
	    $this->template->labels = $this->labelsManager->getLabels();
	}

    protected function createComponentAddEventForm() {
        $form = new Form();
        $form->getElementPrototype()->class('ajax');

        $form->addText('eventName')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Zadejte název událsti');

        $form->addText('eventStartDate')
                 ->setAttribute('placeholder', 'Od')
                 ->setAttribute('class', 'input datepicker')
                 ->setAttribute('id', 'eventStartDate');

        $form->addText('eventEndDate')
                 ->setAttribute('placeholder', 'Do')
                 ->setAttribute('class', 'input datepicker')
                 ->setAttribute('id', 'eventEndDate');

        $form->addText('eventStartTime')
                 ->setAttribute('placeholder', 'Od')
                 ->setAttribute('class', 'input timepicker')
                 ->setAttribute('id', 'eventStartTime');

        $form->addText('eventEndTime')
                 ->setAttribute('placeholder', 'Do')
                 ->setAttribute('class', 'input timepicker')
                 ->setAttribute('id', 'eventEndTime');

        $labels = $this->labelsManager->getLabels();

        foreach ($labels as $key => $label) {
            $labelsOptions[$label['id']] = $label['name'];
        }
        
        $form->addSelect('eventsLabels', '', $labelsOptions)
                 ->setAttribute('id', 'eventsLabelsChooser')
                 ->setPrompt('Žádný');

        $form->addSubmit('save_new_event', 'Uložit')
                 ->setAttribute('class', 'submit_button')
                 ->setAttribute('id', 'save_new_event_button');

        $form->onSuccess[] = array($this, 'addEventFormSucceeded');
        return $form;
    }

    public function addEventFormSucceeded($form, $values) {
        if($this->isAjax()) {
            $this->plannerManager->addNewEvent($values['eventName'],
                                               $values['eventStartDate'],
                                               $values['eventEndDate'],
                                               $values['eventStartTime'],
                                               $values['eventEndTime'],
                                               $values['eventsLabels']);
            $this->redrawControl('eventsView');
            $this->redrawControl('addEvent');
            $this->redrawControl('dark');
        }
    }
}
