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
	public function __construct(Nette\Database\Context $database) {
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
			$this->database->table(self::USER_TABLE_NAME)->insert(array(
				self::USER_COLUMN_NAME => $this->name,
				self::USER_COLUMN_SURNAME => $this->surname,
				self::USER_COLUMN_EMAIL => $this->email,
				self::USER_COLUMN_PASSWORD => $this->password,
				self::USER_COLUMN_PROFILE_PHOTO => $this->profile_photo,
				self::USER_COLUMN_BG_COLOR => $this->bg_color,
				self::USER_COLUMN_ROLE => $this->role
			));
			//TODO look at this class
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException('Uživatel pod tímto emailem je již registrován!');
		}
	}

	/*public function userExists($email) {
		if($this->database->table(self::TABLE_NAME)->where(array('email' => $email))->limit(1)->fetch())
			return FALSE;
		return TRUE;
	}*/
}
