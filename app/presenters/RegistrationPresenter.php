<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Form,
    App\Model\UserManager;

/**
 * RegistrationPresenter
 * 
 */

class RegistrationPresenter extends BasePresenter
{
    /**
     * 
     * @return $form
     *
     * TODO set form to required!
     */
	protected function createComponentRegForm()
    {
        $form = new Form;

        $form->addText('name')
             ->setAttribute('class', 'input')
             ->setAttribute('placeholder', 'Jméno');

        $form->addText('surname')
             ->setAttribute('class', 'input')
             ->setAttribute('placeholder', 'Příjmení');

        $form->addText('email')
             ->setAttribute('class', 'input')
             ->setAttribute('placeholder', 'E-mail')
             ->addRule(Form::EMAIL);

        $form->addPassword('passwd')
             ->setAttribute('class', 'input')
             ->setAttribute('placeholder', 'Heslo');

        $form->addPassword('passwdA')
             ->setAttribute('class', 'input')
             ->setAttribute('placeholder', 'Heslo znovu');

        $form->addSubmit('regBut', 'Vytvořit bezplaný účet')
             ->setAttribute('class', 'submit_button');

        $form->onSuccess[] = array($this, 'regFormSucceeded');

        return $form;
    }

    public function regFormSucceeded($form, $values)
    {
        
    }
}