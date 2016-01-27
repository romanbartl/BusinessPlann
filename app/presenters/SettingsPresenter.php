<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    Nette\Security\User,
    App\Model\UserManager,
    App\Model\LabelsManager,
    App\Model\GroupsManager;

/**
 * 
 */
class SettingsPresenter extends BasePresenter
{
	private $userManager;
	private $labelsManager;
	private $groupsManager;

	private $groupsView;
	private $usersInGroup;
	private $groupColor;
	private $group;

	public function __construct(UserManager $userManager, LabelsManager $labelsManager, GroupsManager $groupsManager) {
        $this->userManager = $userManager;
        $this->labelsManager = $labelsManager;
        $this->groupsManager = $groupsManager;

        $this->usersInGroup = array();
    }

    public function actionDefault() {
		$this->redirect('Settings:user');
	}

	public function actionUser() {
		$this->chceckUserLoggedIn();
	}

	public function actionLabels() {
		$this->chceckUserLoggedIn();
	}

	public function actionGroups($view, $id) {
		$this->chceckUserLoggedIn();		
        $this->groupsView = 'show';
        
        if($view != '') {
           	if($this->groupsManager->checkViewCorrect($view))
               	$this->groupsView = $view;
        	else
            	throw new Nette\Application\BadRequestException('Špatný pohled');
        }

        if($id != ''){
        	if($group = $this->groupsManager->getGroups($id)) {
        		$this->group = $group;
        	} else {
        		$this->template->groupError = 'Zdá se, že skupina neexistuje, nebo nemáte práva na upravování';
        	}
        }
	}

	public function handleChangeGroupsView($view, $id = null) {
		if($this->isAjax()) {
			$this->groupsView = $view;
			$this->redrawControl('groupsView');
		}
	}

    public function renderUser() {
    	$this->template->colors = $this->userManager->getColors();
    }

    public function renderLabels() {
    	$this->template->labels = $this->labelsManager->getLabels();
    	$this->template->colors = $this->userManager->getColors();
	}

	public function renderGroups() {
		if($this->group)
			$this->template->groups = $this->group;
		else
			$this->template->groups = $this->groupsManager->getGroups();
		if(!$this->groupColor)
    		$this->template->groupColor = 1;
    	else
    		$this->template->groupColor = $this->groupColor;

    	$this->template->colors = $this->userManager->getColors();
    	$this->template->groupsView = $this->groupsView;

    	$this->template->usersInGroup = $this->usersInGroup;
	}

	public function handleRemoveLabel($id) {
		if($this->isAjax()) { 
			$this->labelsManager->removeLabel($id);
			$this->redrawControl('labels');
		}
	}

	public function handleEditLabelColor($labelId, $colorId) {
		if($this->isAjax()) {
			$this->labelsManager->editLabelColor($labelId, $colorId);
			$this->redrawControl('labels');
		}
	}

	private function chceckUserLoggedIn() {
	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}

	/**
	 * Form for changing user name
	 * @return $form
	 */
	protected function createComponentNameForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');
		
		$form->addText('name')
			 	->setAttribute('id', 'name_input')
			 	->setAttribute('class', 'user_settings_input');

		$form->addButton('name_but', 'Změnit jméno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_name_but');

		$form->addSubmit('name_but_save', 'Uložit')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_name_but_save');

		$form->addButton('name_storno', 'Storno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'storno_name_but');

		$form->onSuccess[] = array($this, 'nameFormSucceeded');

		return $form;
	}

	public function nameFormSucceeded($form, $values) {
		if($this->isAjax()) {
			if(preg_match('!^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$!', $values['name'])) {
        		$this->userManager->updateUserData('name', $values['name']);
        		$this->redrawFormSnippets();
        	} else {
        		$form->addError('Jméno smí obsahovat pouze písmena!');
        		$this->redrawControl('userName');
        	}
        }
    }
            
	protected function createComponentSurnameForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		//Adding user surname + option for changing the surname
		$form->addText('surname')
			 	->setAttribute('id', 'surname_input')
			 	->setAttribute('class', 'user_settings_input');

		$form->addButton('surname_but', 'Změnit příjmení')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_surname_but');

		$form->addSubmit('surname_but_save', 'Uložit')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_surname_but_save');

		$form->addButton('surname_storno', 'Storno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'storno_surname_but');

		$form->onSuccess[] = array($this, 'surnameFormSucceeded');

		return $form;
	}

	public function surnameFormSucceeded($form, $values) {
		if($this->isAjax()) {
        	if(preg_match('!^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$!', $values['surname'])) 
        		$this->userManager->updateUserData('surname', $values['surname']);
        	else
        		$form->addError('Příjmení smí obsahovat pouze písmena!');
        	
        	$this->redrawFormSnippets();
        }
    }


	protected function createComponentEmailForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		$form->addText('email')
			 	->setAttribute('id', 'email_input')
			 	->setAttribute('class', 'user_settings_input');

		$form->addButton('email_but', 'Změnit e-mail')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_email_but');

		$form->addSubmit('email_but_save', 'Uložit')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_email_but_save');

		$form->addButton('email_storno', 'Storno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'storno_email_but');

		$form->onSuccess[] = array($this, 'emailFormSucceeded');

		return $form;
	}

	public function emailFormSucceeded($form, $values){
		if($this->isAjax()) {
        	if(preg_match('!^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$!', $values['email'])) 
        		$this->userManager->updateUserData('email', $values['email']);
        	else
        		$form->addError('Email je ve špatném tvaru!');
        	
        	$this->redrawFormSnippets();
        }		
	}

	protected function createComponentPasswdForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		$form->addButton('passwd_but', 'Změnit heslo')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_passwd_but');

		$form->addPassword('current_passwd')
			    ->setAttribute('placeholder', 'Aktuální heslo')
			    ->setAttribute('id', 'current_passwd_input')
			    ->setAttribute('class', 'user_settings_input');

		$form->addPassword('new_passwd')
				->setAttribute('placeholder', 'Nové heslo')
				->setAttribute('id', 'new_passwd_input')
				->setAttribute('class', 'user_settings_input');

		$form->addPassword('new_passwd_again')
				->setAttribute('placeholder', 'Potvrzení nového hesla')
				->setAttribute('id', 'new_passwd_again_input')
				->setAttribute('class', 'user_settings_input');

		$form->addSubmit('change_passwd_but_save', 'Uložit')
				->setAttribute('class', 'button last_passwd');

		$form->addButton('passwd_storno', 'Storno')
				->setAttribute('class', 'button last_passwd')
				->setAttribute('id', 'storno_passwd_but');

		$form->onSuccess[] = array($this, 'passwdFormSucceeded');

		return $form;
	}

	public function passwdFormSucceeded($form, $values) {
		if($this->isAjax()) {
			if($values['new_passwd_again'] == $values['new_passwd']) {
				if(preg_match('!^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}$!', $values['new_passwd'])) {
					if(!$this->userManager->updateUserPasswd($values['current_passwd'], $values['new_passwd']))
						$form->addError('Pro pokračování musí být zadáno správné uživatelské heslo');
				} else
					$form->addError('Heslo musí obsahovat nejméně 8 znaků, jedno velké písmeno a jedno číslo!');
			} else
				$form->addError('Hesla se musí shodovat!');

			$this->redrawControl('userPasswd');
		}
	}

	protected function createComponentSendingForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		$form->addCheckbox('send_events', ' Zasílat upozornění událostí na E-mail');

		$form->addCheckbox('send_info', ' Zasílat informace z blogu na E-mail');

		return $form;
	}

	public function handleColor($idColor, $hashColor) {
		if($this->isAjax()) {
        	$this->userManager->updateUserColor($idColor, $hashColor);
			$this->redrawControl('colorStyle');
			$this->redrawControl('userPrefColor');
			$this->redrawControl('settingsHeader');
		}
	}

	private function redrawFormSnippets() {
		$this->redrawControl('userName');
        $this->redrawControl('userSurame');
        $this->redrawControl('userEmail');
        $this->redrawControl('userPasswd');
        $this->redrawControl('userPref');
	}

	protected function createComponentAddLabelForm() {
		$form = new Form();
		$form->getElementPrototype()->class('ajax');
		
		$form->addText('add_label_name')
					->setAttribute('id', 'add_label_name_input')
					->setAttribute('placeholder', 'Zadejte název štítku');

		$form->addHidden('add_label_color_id')
					->setAttribute('id', 'add_label_color_id_hidden');

		$form->addSubmit('add_label_submit', '')
					->setAttribute('id', 'add_label_submit');

		$form->onSuccess[] = array($this, 'addLabelFormSucceeded');

		return $form;
	}

	public function addLabelFormSucceeded($form, $values) {
		if($this->isAjax()) {
			$this->labelsManager->addNewLabel($values['add_label_name'], $values['add_label_color_id']);
			$this->redrawControl('labels');
		}
	}

	protected function createComponentEditLabelForm() {
		$form = new Form();
		$form->getElementPrototype()->class('ajax');

		$form->addText('edit_label_name')
				->setAttribute('class', 'edit_label_name');

		$form->addSubmit('edit_label_submit', '')
				->setAttribute('class', 'edit_label_submit_class');

		$form->addHidden('edit_label_id');

		$form->onSuccess[] = array($this, 'editLabelFormSucceeded');

		return $form;
	}

	public function editLabelFormSucceeded($form, $values) {
		if($this->isAjax()) {
			$this->labelsManager->editLabelName($values['edit_label_id'], $values['edit_label_name']);
			$this->redrawControl('labels');
		}
	}

	public function handleGroupStorno(){
		if($this->isAjax()) {
			$this->groupsView = 'show';
			$this->group = $this->groupsManager->getGroups();
			$this->redrawControl('groupsView');
		}
	}

	public function createComponentGroupForm() {
		$form = new Form();
		//$form->getElementPrototype()->class('ajax');

		$form->addText('group_name')
				->setAttribute('class', '');

		$form->addHidden('group_color_hidden')
				->setAttribute('id', 'group_color_hidden');

		$form->addHidden('group_users_hidden')
				->setAttribute('name', 'group_users_hidden[]');

		$user = $form->addText('group_user')
				->setAttribute('placeholder', 'přidat e-mail uživatele')
				->setAttribute('class', '');

		$form->addSubmit('add_group_user_submit', 'bla')
				->setAttribute('class', '');

		$form->addSubmit('group_form_submit', 'Uložit')
				->setAttribute('class', '');

		$form->onSuccess[] = array($this, 'groupFormSucceeded');

		return $form;
	}

	public function groupFormSucceeded($form, $values){

		if(!$this->isAjax()) {

			if($form['group_form_submit']->isSubmittedBy()){
				if($values['group_name'] != '') {
				
				
			$this->groupsManager->addGroup($values['group_name'], 
											$values['group_color_hidden'], 
											array($values['group_users_hidden']));
			$this->redirect('Settings:groups');
			}else{
				$form->addError('Skupina musí mít název');
			}
		} elseif($form['add_group_user_submit']->isSubmittedBy()){

			if(isset($values['group_users_hidden']))
				$this->usersInGroup = array($values['group_users_hidden']);

			if(preg_match('!^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$!', $values['group_user'])) {
				if($this->userManager->userExists($values['group_user'])){
					if($values['group_user'] != $this->user->identity->email) {
						$this->usersInGroup[] = $values['group_user'];
						
					}
					else {$form->addError('Váš účet je již definován jako leader skupiny');}
			} else{$form->addError('Žádný uživatel pod tímto e-mailem zde není registrován');}}
			else{$form->addError('Zadaný e-mail není ve správném formátu');}

			$this->groupColor = $values['group_color_hidden'];

			$this->redrawControl('addGroup');


		}
		}
	}

	public function createComponentEditGroup() {
		$form = new Form();
		//$form->getElementPrototype()->class('ajax');

		$form->addText('group_name')
				->setAttribute('class', '');

		$form->addText('group_user')
				->setAttribute('class', '');

		$form->addHidden('group_id_hidden');

		$form->addSubmit('add_user_submit', 'Přidat')
				->setAttribute('class', '');

		$form->onSuccess[] = array($this, 'editGroupSucceeded');

		return $form;
	}

	public function handleDeleteGroup($groupId) {		
		if($this->isAjax()){	
			$this->groupsManager->deleteGroup($groupId);
			$this->redirect('Settings:groups');
		}
	}

	public function editGroupSucceeded($form, $values){
		if(!preg_match('!^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$!', $values['group_user'])){
			$form->addError('Zadaný e-mail není ve správném formátu');
			return false;
		} elseif(!$this->userManager->userExists($values['group_user'])) {
			$form->addError('Žádný uživatel pod tímto e-mailem zde není registrován');
			return false;
		} if($this->groupsManager->userIsAlreadyInGroup($values['group_id_hidden'], $values['group_user'])) {
			$form->addError('Zadaný uživatel již ve skupině je');
			return false;
		}

		$this->groupsManager->addGroupUser($values['group_id_hidden'], $values['group_user']);
		$this->group = $this->groupsManager->getGroups($values['group_id_hidden']);
		$this->redrawControl('editGroup');
	}		
}

