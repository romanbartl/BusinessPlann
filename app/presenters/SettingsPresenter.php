<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    Nette\Security\User,
    App\Model\UserManager;

/**
 * 
 */
class SettingsPresenter extends BasePresenter
{
	private $userManager;

	public function __construct(UserManager $userManager) {
        $this->userManager = $userManager;
    }

    public function renderUser() {
    	$this->template->colors = $this->userManager->getColors();
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

	public function actionGroups() {
		$this->chceckUserLoggedIn();
	}

	public function chceckUserLoggedIn() {
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
			 	->addRule(Form::PATTERN, 'Jméno smí obsahovat pouze písmena.', 
                    '^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$');

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
        	$this->userManager->updateUserData('name', $values['name']);
        	$this->redrawFormSnippets();
        }
    }
            
	protected function createComponentSurnameForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		//Adding user surname + option for changing the surname
		$form->addText('surname')
			 	->setAttribute('id', 'surname_input')
			 	->addRule(Form::PATTERN, 'Příjmení smí obsahovat pouze písmena.', 
                    '^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$');

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
        	$this->userManager->updateUserData('surname', $values['surname']);
        	$this->redrawFormSnippets();
        }
    }


	protected function createComponentEmailForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		$form->addText('email')
			 	->setAttribute('id', 'email_input')
			 	->addRule(Form::PATTERN, 'Email není ve správném tvaru!', 
                    '^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$');

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
        	$this->userManager->updateUserData('email', $values['email']);
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
			    ->setAttribute('id', 'current_passwd_input');

		$form->addPassword('new_passwd')
				->setAttribute('placeholder', 'Nové heslo')
				->setAttribute('id', 'new_passwd_input');

		$form->addPassword('new_passwd_again')
				->setAttribute('placeholder', 'Potvrzení nového hesla')
				->setAttribute('id', 'new_passwd_again_input')
				->addRule(Form::EQUAL, 'Hesla se musí shodovat!', $form['new_passwd']);;

		$form->addSubmit('change_passwd_but_save', 'Uložit')
				->setAttribute('class', 'button last_passwd');

		$form->addButton('passwd_storno', 'Storno')
				->setAttribute('class', 'button last_passwd')
				->setAttribute('id', 'storno_passwd_but');

		$form->onSuccess[] = array($this, 'passwdFormSucceeded');

		return $form;
	}

	public function passwdFormSucceeded() {

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
		}
	}

	private function redrawFormSnippets() {
			$this->redrawControl('userName');
        	$this->redrawControl('userSurame');
        	$this->redrawControl('userEmail');
        	$this->redrawControl('userPasswd');
        	$this->redrawControl('userPref');
	}
}

