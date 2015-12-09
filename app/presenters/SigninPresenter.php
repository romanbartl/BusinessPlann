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
	public function actionDefault() {
		if ($this->getUser()->isLoggedIn())
	        $this->redirect('Businessplann:default');
	}

	protected function createComponentSignInForm() {
		$form = New Form();
		$form->getElementPrototype()->class('ajax');

		$form->addText('email')
			 	->setAttribute('class', 'input')
			 	->setAttribute('placeholder', 'E-mail');

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
		if($this->isAjax()) {
			if(preg_match('!^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$!', $values['email'])) {
				try {
		        	$this->getUser()->login($values['email'], $values['passwd']);
					$this->redirect('Businessplann:default');
		        } catch (Nette\Security\AuthenticationException $e) {
		       		$form->addError($e->getMessage());
	    		}
	    	} else
				$form->addError('Email není ve správném tvaru!');

			$this->redrawControl('signInFormSnippet');
	    }
	}	
}