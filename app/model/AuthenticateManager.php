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

		$row = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $email)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Uživatel pod tímto emailem není zaregistrován!', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::USER_COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('Nesprávné heslo!', self::INVALID_CREDENTIAL);

		}

		$arr = $row->toArray();
		$arr['color'] = $row->color[self::COLOR_COLUMN_COLOR];

		unset($arr[self::USER_COLUMN_ID], $arr[self::USER_COLUMN_PASSWORD], $arr[self::USER_COLUMN_COLOR]);
		return new Nette\Security\Identity($row[self::USER_COLUMN_ID], 'Uživatel', $arr);
	}
} 