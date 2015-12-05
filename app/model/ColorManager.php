<?php

namespace App\Model;

use Nette;


class ColorManager extends BaseManager
{
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	public function getColors() {
		return $this->database->table(self::COLOR_TABLE_NAME);		
	}
}