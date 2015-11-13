<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\User;



class AuthenticateManager extends BaseManager implements Nette\Security\IAuthenticator
{
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($email, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_EMAIL, $email)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Nesprávné přihlašovací jméno!', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('Nesprávné heslo!', self::INVALID_CREDENTIAL);

		}/* TODO elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD])) {
			$row->update(array(
				self::COLUMN_PASSWORD => Passwords::hash($password),
			));
		}*/

		$arr = $row->toArray();
		unset($arr[self::COLUMN_ID], $arr[self::COLUMN_PASSWORD], $arr[self::COLUMN_ROLE]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row->role[self::COLUMN_ROLE], $arr);
	}
} 