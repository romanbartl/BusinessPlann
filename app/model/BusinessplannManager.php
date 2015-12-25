<?php

namespace App\Model;

use Nette,
	Nette\Security\User;

class BusinessplannManager extends BaseManager
{
	private $database;
	private $user;

	public function __construct(Nette\Database\Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}

	public function getEvents() {
		$result = $this->database->query("SELECT id, name, 
											CONCAT(DAY(start), '.', MONTH(start), '. ', YEAR(start)) AS startDay,
											CONCAT(HOUR(start), ':', MINUTE(start)) AS startTime,
											CONCAT(DAY(end), '.', MONTH(end), '. ', YEAR(end)) AS endDay,
											CONCAT(HOUR(end), ':', MINUTE(end)) AS endTime
											FROM `event`
											WHERE user_id = " . $this->user->identity->id);
   
        $events = array();
		foreach ($result as $key => $event) {
			$events[$key]['id'] = $event['id'];
			$events[$key]['name'] = $event['name'];
			$events[$key]['startDay'] = $event['startDay'];
			$events[$key]['startTime'] = $event['startTime'];
			$events[$key]['endDay'] = $event['startDay'];
			$events[$key]['endTime'] = $event['endTime'];
		}
		
		return $events;
	}
}