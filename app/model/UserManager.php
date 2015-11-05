<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\User;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'user',
		COLUMN_ID = 'id_user',
		COLUMN_NAME = 'name',
		COLUMN_SURNAME = 'surname',
		COLUMN_EMAIL = 'email',
		COLUMN_PASSWORD = 'password',
		COLUMN_PROFILE_PHOTO = 'profile_photo',
		COLUMN_BG_COLOR = 'bg_color_id_bg_color',
		COLUMN_ROLE = 'role_id_role';		


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
		$this->options['cost'] = 23;
		$this->options['salt'] = 'Ry\X234121566m97w5*de78a5d2vx3';
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($email, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->fetch();

		/*if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.' . Passwords::hash($password), self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD])) {
			$row->update(array(
				self::COLUMN_PASSWORD => Passwords::hash($password),
			));
		}*/

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
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
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	public function userExists($email) {
		if($this->database->table(self::TABLE_NAME)->where(array('email' => $email))->limit(1)->fetch())
			return FALSE;

		return TRUE;
	}
}
