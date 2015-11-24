<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    Nette\Security\User;

/**
 * 
 */

class SettingsPresenter extends BasePresenter
{
	public function renderUser() {
		//$this->template->bg_colors = $this->database->table('bg_color');
	}

	public function actionDefault() {
		$this->redirect('Settings:user');
	}

	protected function createComponentDataForm() {
		$form = New Form();
		$user = $this->getUser()->identity->data;

		$form->addText('name')
			 	->setAttribute('id', 'name_input')
			 	->setAttribute('value', $user['name'] . " " . $user['surname'])
				->setDisabled();

		$form->addButton('name_but', 'Změnit jméno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_name_but');

		$form->addButton('name_but_save', 'Uložit')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_name_but_save');

		$form->addText('email')
			 	->setAttribute('id', 'email_input')
			 	->setAttribute('value', $user['email'])
				->setDisabled();

		$form->addButton('email_but', 'Změnit e-mail')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_email_but');

		$form->addButton('passwd_but', 'Změnit heslo')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_passwd_but');

		return $form;
	}

	public function actionLabels() {
		$this->action();
	}

	public function actionGroups() {
		$this->action();
	}

	private function action() {
	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}

}