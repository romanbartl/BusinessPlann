<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UserManager;

/**
 * Registration Presenter
 */

class RegistrationPresenter extends BasePresenter
{
    // @var UserManager $userManager - instance of class Model for work with users
    private $userManager;

    public $factory;

    /**
     * Contruct
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager) {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * Creates form for registration template
     * @return $form
     *
     * TODO regulární výrazy
     */
	protected function createComponentRegForm() {
        $form = new Form;

        $form->addText('name')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Jméno')
                 ->addRule(Form::FILLED, 'Vyplňte své jméno.')
                 ->addRule(Form::PATTERN, 'Jméno není ve správném tvaru!', 
                    '^[a-zěščřžýáíéťňďó]{1,}$');

        $form->addText('surname')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Příjmení')
                 ->addRule(Form::FILLED, 'Vyplňte své příjmení.')
                 ->addRule(Form::PATTERN, 'Příjmení není ve správném tvaru!', 
                    '^[a-zěščřžýáíéťňďó]{1,}$');

        $form->addText('email')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'E-mail')
                 ->addRule(Form::FILLED, 'Vyplňte svůj email.')
                 // TODO ->addRule($this->userManager->userExists($form['email']), 'Uživatel s tímto emailem již existuje.')
                 ->addRule(Form::PATTERN, 'Email není ve správném tvaru!', 
                    '^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$');

        /* TODO podmínka pro vstup hesla (reg. výrazy) */
        $form->addPassword('passwd')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Heslo')             
                 ->addRule(Form::FILLED, 'Vyplňte své heslo.');

        $form->addPassword('passwdControl')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Heslo znovu')
                 ->addRule(Form::FILLED, 'Vyplňte kontrolu hesla.')
                 ->addRule(Form::EQUAL, 'Hesla se musí shodovat!', $form['passwd']);

        $form->addSubmit('regBut', 'Vytvořit bezplatný účet')
                 ->setAttribute('class', 'submit_button');

        $form->onSuccess[] = array($this, 'regFormSucceeded');

        return $form;
    }

    /**
     * Calls this function after succesfull form send 
     * @param Form $form
     * @param Array $values - sent values from form
     */
    public function regFormSucceeded($form, $values) {
        $this->userManager->register($values['name'], 
                                     $values['surname'],
                                     $values['email'],
                                     $values['passwd']);
    }
}