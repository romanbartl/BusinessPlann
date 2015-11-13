<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\User;


/**
 * Users management.
 */
class UserManager extends BaseManager
{
	/** @var Nette\Database\Context */
	private $database;
	private $options;

	private $name;
	private $surname;
	private $email;
	private $password;
	private $profile_photo = 1;
	private $bg_color = 1;
	private $role;


	/** @var Nette\Database\Context $database
	  * contructor inicializace local var $database
	  */
	public function __construct() {
		$this->database = $database;
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
		$this->name = trim(strip_tags($name));
		$this->surname = trim(strip_tags($surname));
		$this->email = trim(strip_tags($email));
		$this->password = Passwords::hash($password);
		$this->role = $role;

		try {
			$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $this->name,
				self::COLUMN_SURNAME => $this->surname,
				self::COLUMN_EMAIL => $this->email,
				self::COLUMN_PASSWORD => $this->password,
				self::COLUMN_PROFILE_PHOTO => $this->profile_photo,
				self::COLUMN_BG_COLOR => $this->bg_color,
				self::COLUMN_ROLE => $this->role
			));
			//TODO look at this class
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	/*public function userExists($email) {
		if($this->database->table(self::TABLE_NAME)->where(array('email' => $email))->limit(1)->fetch())
			return FALSE;
		return TRUE;
	}*/
}
