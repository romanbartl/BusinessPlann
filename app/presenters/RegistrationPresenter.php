<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\UserManager;
use Nette\Security\User;

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

    public function actionDefault() {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Businessplann:default');
        }
    }

    /**
     * Creates form for registration template
     * @return $form
     *
     * TODO regulární výrazy
     */
	protected function createComponentRegForm() {
        $form = new Form;
        $form->getElementPrototype()->class('ajax');

        $form->addText('name')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Jméno');
 
        $form->addText('surname')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Příjmení');

        $form->addText('email')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'E-mail');

        /* TODO podmínka pro vstup hesla (reg. výrazy) */
        $form->addPassword('passwd')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Heslo') ; 

        $form->addPassword('passwdControl')
                 ->setAttribute('class', 'input')
                 ->setAttribute('placeholder', 'Heslo znovu');

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
        if($this->isAjax()) {
            if(preg_match('!^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$!', $values['name'])) {
                if(preg_match('!^([A-ZĚŠČŘŽÝÁÍÉŤŇĎÓ]|[a-zěščřžýáíéťňďó]){1,}$!', $values['surname'])){
                    if(preg_match('!^[A-Za-z0-9._-]+@[A-Za-z0-9]+\.[a-z]{1,4}$!', $values['email'])) {
                        if($values['passwd'] == $values['passwdControl']) {
                            if(preg_match('!^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(.){8,}$!', $values['passwd']))
                            {
                                try {
                                    /* TODO duplicate Error*/
                                    $this->userManager->register($values['name'], 
                                                                 $values['surname'],
                                                                 $values['email'],
                                                                 $values['passwd']);
                                    $this->redirect('Settings:user');
                                } catch(DuplicateNameException $e) {
                                    $form->addError($e);
                                }
                            } else {
                                $form->addError('Heslo musí obsahovat nejméně 8 znaků, jedno velké písmeno a jedno číslo!');
                            }
                        } else {
                            $form->addError('Hesla se musí shodovat!');
                        }
                    } else {
                        $form->addError('Email není ve správném tvaru!');
                    }
                } else
                    $form->addError('Příjmení smí obsahovat pouze písmena!');
            } else {
                $form->addError('Jméno smí obsahovat pouze písmena!');
            }

            $this->redrawControl('regSnippet');
        }
    }
}