<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    Nette\Security\User;

/**
 * Signin Presenter
 */

class SigninPresenter extends BasePresenter
{
	protected function createComponentSignInForm() {
		$form = New Form();

		$form->addText('email')
			 	->setAttribute('class', 'input')
			 	->setAttribute('placeholder', 'E-mail')
			 	->addRule(Form::FILLED, 'Vyplňte svůj email.')
                ->addRule(Form::PATTERN, 'Email není ve správném tvaru!', 
                    '^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$');;

		$form->addPassword('passwd')
		     ->setAttribute('class', 'input')
		     ->setAttribute('placeholder', 'Heslo');

		$form->addSubmit('signInBut', 'Přihlásit')
			 ->setAttribute('class', 'submit_button');

		$form->addCheckBox('stay_logged', 'Neodhlašovat');
                  
		$form->onSuccess[] = array($this, 'signInFormSucceeded');

		return $form;
	}

	public function signInFormSucceeded($form, $values) {
		try {
	        $this->getUser()->login($values['email'], $values['passwd']);
			$this->redirect('Businessplann:default');

    	} catch (Nette\Security\AuthenticationException $e) {
       		$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
    	}
	}	

	public function actionDefault() {
		if ($this->getUser()->isLoggedIn())
	        $this->redirect('Businessplann:default');
	}
}