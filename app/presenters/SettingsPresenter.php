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
	private $database;

	public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

	public function renderUser() {
		$this->template->bg_colors = $this->database->table('bg_color');
	}

	public function actionDefault() {
		$this->redirect('Settings:user');
	}

	protected function createComponentDataForm() {
		$form = New Form();
		$user = $this->getUser()->identity->data;

		$form->addText('name')
			 	->setAttribute('id', 'name_input')
			 	->setAttribute('value', ($user['name'] . " " . $user['surname']))
				->setDisabled();

		$form->addButton('name_but', 'Změnit jméno')
				->setAttribute('class', 'button')
				->setAttribute('id', 'change_name_but');

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

	// TODO make tihs redirect same for all templates action
	public function actionUser() {
		if(isset($_GET['logout']))
	    	$this->getUser()->logout();

	    if (!$this->getUser()->isLoggedIn()) {
	        $this->redirect('Home:default');
	    }
	}

}