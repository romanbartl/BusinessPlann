<?php

namespace App\Presenters;

use Nette,
    Nette\Application\UI\Presenter,
    Nette\Application\UI\Form,
    App\Model\UserManager;

/**
 * Registration Presenter
 */

class RegistrationPresenter extends BasePresenter
{
    // @var UserManager $userManager - instance of class Model for work with users
    private $userManager;

    /**
     * Contruct
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * Creates form for registration template
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

    /**
     * Calls this function after succesfull form send 
     * @param Form $form
     * @param Array $values - sent values from form
     */
    public function regFormSucceeded($form, $values)
    {
        
    }
}