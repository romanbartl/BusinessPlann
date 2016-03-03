<?php

namespace App\Presenters;

use Nette,
    Nette\Security\User,
    Nette\Application\UI\Presenter,
	Nette\Application\UI\Form,
    App\Model\AppManager,
    App\Model\LabelsManager,
    App\Model\GroupsManager,
    App\Model\CommentsManager;


/**
 * 
 */
class AppPresenter extends BasePresenter
{
	private $appManager;
	private $labelsManager;
    private $groupsManager;
    private $commentsManager;

    private $viewFormat;
    private $viewFromDate;

    private $eventId;
    private $eventLabelId;


	public function __construct(AppManager $appManager, LabelsManager $labelsManager, GroupsManager $groupsManager,
                                CommentsManager $commentsManager) {
        parent::__construct();
        $this->appManager = $appManager;
        $this->labelsManager = $labelsManager;
        $this->groupsManager = $groupsManager;
        $this->commentsManager = $commentsManager;
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
                throw new Nette\Application\BadRequestException('Objekt nebyl nalezen');

        if($date != '' && !$this->appManager->checkDateCorrect($date))
            throw new Nette\Application\BadRequestException('Objekt nebyl nalezen');

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
        $this->template->groups = $this->groupsManager->getGroups();

        $this->template->viewFromDateFormated = $this->appManager->getFormatedDate($this->template->viewFromDate, $this->template->viewFormat);     
        $this->template->viewDatePlus = $this->appManager->getModifiedDate($this->template->viewFromDate, $this->template->viewFormat, '+1 ');
        $this->template->viewDateMinus = $this->appManager->getModifiedDate($this->template->viewFromDate, $this->template->viewFormat, '-2 ');       
    }


    public function getFormatedDate($date) {
        return $this->appManager->getFormatedDate($this->appManager->getNewDate($date), 'agendaLink');
    }

    protected function createComponentAddEventForm() {
        $form = new Form();
        //$form->getElementPrototype()->class('ajax');

        $form->addText('eventName')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Zadejte název události')
                 ->setAttribute('id', 'event_name_input');

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
        $labelsOptions = array();
        foreach ($labels as $label) {
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
        if(!$this->isAjax()) {
            $eventId = $this->appManager->addNewEvent($values['eventName'],
                                               $values['eventStartDate'],
                                               $values['eventEndDate'],
                                               $values['eventStartTime'],
                                               $values['eventEndTime'],
                                               $values['eventsLabels']);
            
            $this->redirect('App:event', array('id' => $eventId));
        }
    }

    public function actionEvent($id) {
        if(!$this->appManager->userHasPermition($id))
            throw new Nette\Application\BadRequestException('Tato událost nebyla nalezena');

        $this->eventId = $id;
    }

    public function handleDeleteFromGroup($groupId) {
        if($this->isAjax()) {
            $this->appManager->deleteEventFromGroup($this->eventId, $groupId);
            $this->redrawControl('groups');  
            $this->redrawControl('shareGroups');  
        }
    }

    public function handleDeleteEvent() {
        $this->appManager->deleteEvent($this->eventId);
        $this->redirect('App:default');
    }

    public function renderEvent() {
        $this->template->event = $this->appManager->getEventById($this->eventId);
        $this->eventLabelId = $this->template->event['labelId'];
        $this->template->eventInGroups = $this->appManager->eventInGroups($this->eventId);
        $this->template->groups = $this->appManager->getGroupsDifference($this->eventId);
        $this->template->comments = $this->commentsManager->getComents($this->eventId);
    }

    protected function createComponentEditEventForm() {
        $form = new Form();
        $form->getElementPrototype()->class('ajax');

        $form->addText('eventName')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Zadejte název události');

        $form->addText('eventStartDate')
                 ->setAttribute('class', 'input datepicker')
                 ->setAttribute('id', 'eventStartDate');

        $form->addText('eventEndDate')
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

        $form->addText('eventPlace')
                 ->setAttribute('placeholder', 'Místo')
                 ->setAttribute('class', 'input')
                 ->setAttribute('id', 'event_place_input');

        $form->addTextArea('eventDescription')
                ->setAttribute('id', 'textarea_description')
                ->setAttribute('class', 'input');

        $form->addSubmit('edit_event', '')
                 ->setAttribute('id', 'edit_event_button');

        $form->onSuccess[] = array($this, 'editEventFormSucceeded');

        return $form;
    }

    public function editEventFormSucceeded($form, $values) {
        if($this->isAjax()) {
            $this->appManager->editEvent($this->eventId,
                                            $values['eventName'],
                                            $values['eventStartDate'],
                                            $values['eventEndDate'],
                                            $values['eventStartTime'],
                                            $values['eventEndTime'],
                                            $values['eventPlace'],
                                            $values['eventDescription']); 

            $this->redirect('App:default');  
        }                          
    }

    protected function createComponentLabelsForm() {
        $form = new Form();
        $form->getElementPrototype()->class('ajax');

        $labels = $this->labelsManager->getLabels();
        $labelsOptions = array();
        
        foreach ($labels as $label) {
            $labelsOptions[$label['id']] = $label['name'];
        }

        $form->addSelect('editEventsLabels', '', $labelsOptions)
                 ->setAttribute('id', 'eventsLabelsChooser')
                 ->setPrompt('Žádný');

        $form->addSubmit('edit_labels_submit', 'Uložit')
                 ->setAttribute('class', '')
                 ->setAttribute('id', 'edit_event_button');

        $form['editEventsLabels']->setDefaultValue($this->eventLabelId);

        $form->onSuccess[] = array($this, 'labelsFormSucceeded');

        return $form;
    }

    public function labelsFormSucceeded($form, $values) {
        if($this->isAjax()) {
            $this->appManager->changeLabel($this->eventId, $values['editEventsLabels']);
            $this->redrawControl('header');  
        }  
    }

    protected function createComponentShareInGroupForm() {
        $form = new Form();
        $form->getElementPrototype()->class('ajax');

        $groups = $this->appManager->getGroupsDifference($this->eventId);
        foreach($groups as $group) {
            $form->addCheckbox('group_' . $group['id'], $group['name']);
        }

        $form->addSubmit('groupSubmit', 'uložit');

        $form->onSuccess[] = array($this, 'shareInGroupFormSucceeded');

        return $form;
    }

    public function shareInGroupFormSucceeded($form, $values) {
        if($this->isAjax()) {
            $shareGroupsIds = array();

            $groups = $this->appManager->getGroupsDifference($this->eventId);
            foreach($groups as $group) {
                if($values['group_' . $group['id']])
                    $shareGroupsIds[] = $group['id'];
            }

            $this->appManager->shareInGroups($this->eventId, $shareGroupsIds);
            $this->redrawControl('event');
            $this->redrawControl('shareGroups');
            $this->redrawControl('dark');
        }
    }

    protected function createComponentCommentsForm() {
        $form = new Form();
        $form->getElementPrototype()->class('ajax');

        $form->addTextArea('commentContent', 'Komentář ..')
                ->setAttribute('id', 'textarea_comment');

        $form->addSubmit('commentSubmit', '')
                ->setAttribute('id', 'comment_submit');

        $form->onSuccess[] = array($this, 'commentsFormSucceeded');

        return $form;
    }

    public function commentsFormSucceeded($form, $values) {
        if($this->isAjax()) {
            $this->commentsManager->addComment($values['commentContent'], $this->eventId);
            $this->redrawControl('comments');
        }
    }

}
