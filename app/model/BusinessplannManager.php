<?php

namespace App\Model;

use Nette,
	Nette\Security\User;

class BusinessplannManager extends Nette\Object
{
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
}