<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'user',
		COLUMN_NAME = 'name',
		COLUMN_SURNAME = 'surname',
		COLUMN_EMAIL = 'email',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_PROFILE_PHOTO = 'profile_photo',
		COLUMN_ID_SETTINGS = 'id_settings';
		// TODO COLUMN role


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string $name - name of user
	 * @param  string $surname - surname of user
	 * @param  string $email - email of user
	 * @param  string $password - 
	 * @param  
	 * @return void
	 */
	public function register($name, $surname, $email, $password, $profile_photo, $id_settings)
	{
		
		try {
			// TODO Passwords::hash($password)
			$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $name,
				self::COLUMN_SURNAME => $surname, 
				self::COLUMN_EMAIL => $email,
				self::COLUMN_PASSWORD_HASH => $password,
				self::COLUMN_PROFILE_PHOTO => $profile_photo,
				self::COLUMN_ID_SETTINGS => $id_settings
			));
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

}
