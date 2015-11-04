<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    App\Model\UserManager;

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

		$form->addText('passwd')
		     ->setAttribute('class', 'input')
		     ->setAttribute('placeholder', 'Heslo');

		$form->addSubmit('signInBut', 'Přihlásit')
			 ->setAttribute('class', 'submit_button');

		$form->addCheckBox('stay_logged', 'Neodhlašovat');
                  
		$form->onSuccess[] = array($this, 'signInFormSucceeded');

		return $form;
	}

	public function signInFormSucceeded($form, $values) {
		
	}
}