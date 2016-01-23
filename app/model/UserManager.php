<?php

namespace App\Model;

use Nette,
    Nette\Security\Passwords,
    Nette\Security\User,
    App\Model\ColorManager;


/**
 * Users management.
 */
class UserManager extends BaseManager
{
	/** @var Nette\Database\Context */
	private $database;
	private $colorManager;

	private $name;
	private $surname;
	private $email;
	private $password;
	private $profile_photo = 1;
	private $bg_color = 1;
	private $role;

	private $user;


	/** @var Nette\Database\Context $database
	  * contructor inicializace local var $database
	  */
	public function __construct(Nette\Database\Context $database, Nette\Security\User $user, ColorManager $colorManager) {
		$this->database = $database;
		$this->user = $user;
		$this->colorManager = $colorManager;
	}

	/**
	 * Adds new user.
	 * @param  string $name - user's real name
	 * @param  string $surname - user's real surname
	 * @param  string $email - user's email
	 * @param  string $password - user's password
	 * @param  int $role - default is set on 2 (standard user)
	 *
	 * @return void or failure message 
	 */
	public function register($name, $surname, $email, $password, $role = 2) {
		$this->name = ucfirst(strtolower(trim(strip_tags($name))));
		$this->surname = ucfirst(strtolower(trim(strip_tags($surname))));
		$this->email = trim(strip_tags($email));
		$this->password = Passwords::hash($password);
		$this->role = $role;

		try {
			$this->database->table(self::USER_TABLE_NAME)->insert(array(
				self::USER_COLUMN_NAME => $this->name,
				self::USER_COLUMN_SURNAME => $this->surname,
				self::USER_COLUMN_EMAIL => $this->email,
				self::USER_COLUMN_PASSWORD => $this->password,
				self::USER_COLUMN_PROFILE_PHOTO => $this->profile_photo,
				self::USER_COLUMN_COLOR => $this->bg_color,
				self::USER_COLUMN_ROLE => $this->role,
				self::USER_COLUMN_LANGUAGE => 2
			));

			$this->user->login($this->email, $password);
			//TODO look at this class how it works
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException('Uživatel pod tímto emailem je již registrován!');
		}
	}

	public function updateUserData($itemToChange, $data) {
		switch($itemToChange) {
			case 'name':
				$rowToChange = self::USER_COLUMN_NAME;
				break;
			case 'surname':
				$rowToChange = self::USER_COLUMN_SURNAME;
				break;
			case 'email':
				$rowToChange = self::USER_COLUMN_EMAIL;
				break;
		}

		$this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_ID, $this->user->identity->id)
													 ->update(array($rowToChange => $data));

		$this->user->identity->$itemToChange = $data;
	}

	public function getColors() {
		return $this->colorManager->getColors();
	}

	public function updateUserColor($idColor, $hashColor) {
		$this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_ID, $this->user->identity->id)
													 ->update(array(self::USER_COLUMN_COLOR => $idColor));

		$this->user->identity->color = $hashColor;
	}

	public function updateUserPasswd($curentPasswd, $newPasswd) {
		$row = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_ID, $this->user->identity->id)->fetch();

		if(Passwords::verify($curentPasswd, $row[self::USER_COLUMN_PASSWORD])) {
			$this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_ID, $this->user->identity->id)
													 	 ->update(array(self::USER_COLUMN_PASSWORD => Passwords::hash($newPasswd)));
			return True;
		} else {
			return False;
		}
	}

	public function userExists($email) {
		if($this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $email)->fetch())
			return true;
		return false;
	}
}
