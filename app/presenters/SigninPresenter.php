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
	protected function createComponentSignInForm()
	{
		$form = New Form();

		$form->addText('email')
			 ->setAttribute('class', 'input')
			 ->setAttribute('placeholder', 'E-mail');

		$form->addText('passwd')
		     ->setAttribute('class', 'input')
		     ->setAttribute('placeholder', 'Heslo');

		$form->addSubmit('signInBut', 'Přihlásit')
			 ->setAttribute('class', 'submit_button');
			 
		return $form;
	}
}