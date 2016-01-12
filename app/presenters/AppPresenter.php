<?php

namespace App\Presenters;

use Nette,
    Nette\Security\User,
    Nette\Application\UI\Presenter,
	Nette\Application\UI\Form,
    App\Model\AppManager,
    App\Model\LabelsManager;


/**
 * 
 */
class AppPresenter extends BasePresenter
{
	private $appManager;
	private $labelsManager;

    private $viewFormat;
    private $viewFromDate;

	public function __construct(AppManager $appManager, LabelsManager $labelsManager) {
        parent::__construct();
        $this->appManager = $appManager;
        $this->labelsManager = $labelsManager;
    }

    public function actionDefault($view, $date) {
        if (!$this->getUser()->isLoggedIn()) 
            $this->redirect('Home:default');
        if($view == 'out'){
            $this->user->logout();
            $this->redirect('Home:default');
        }    
        if(!$this->appManager->checkViewCorrect($view))
            if($view == '')
                $this->redirect('default', 'day');
            else
                throw new Nette\Application\BadRequestException('Špatný pohled');

        $this->template->viewFromDate = $this->appManager->getNewDate($date);
        $this->template->viewFormat = $view;
    }

    public function handleChangeView($view, $date){
        if($this->isAjax()) {
            $this->template->viewFormat = $view;
            $this->redrawControl('eventsView');
        }
    }
    
    public function handleChangeViewDate($date){
        if($this->isAjax()) {
            $this->template->viewFromDate = $this->appManager->getNewDate($date);
            $this->redrawControl('eventsView');
        }
    }

    public function handleEditEvent($id){
        if($this->isAjax()) {
            $this->redrawControl('eventsView');
        }
    }

    public function renderDefault() {
    	$this->template->events = $this->appManager->getEvents($this->template->viewFormat, $this->template->viewFromDate); 
        $this->template->labels = $this->labelsManager->getLabels();

        $this->template->viewFromDateFormated = $this->appManager->getFormatedDate($this->template->viewFromDate, $this->template->viewFormat);     
        $this->template->viewDatePlus = $this->appManager->getModifiedDate($this->template->viewFromDate, $this->template->viewFormat, '+1 ');
        $this->template->viewDateMinus = $this->appManager->getModifiedDate($this->template->viewFromDate, $this->template->viewFormat, '-2 ');       
    }

    public function getFormatedDate($date) {
        return $this->appManager->getFormatedDate($this->appManager->getNewDate($date), 'agendaLink');
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
            $this->appManager->addNewEvent($values['eventName'],
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
