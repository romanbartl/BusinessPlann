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
		$result = $this->database->query('SELECT e.id AS id, e.name AS name, 
											DATE_FORMAT(e.start, "%d.%c.%Y") AS startDay,
											DATE_FORMAT(e.start, "%H:%i") AS startTime,
											DATE_FORMAT(e.end, "%d.%c.%Y") AS endDay,
											DATE_FORMAT(e.end, "%H:%i") AS endTime,
											l.name AS label,
											c.color AS label_color
											FROM `event` AS e
											LEFT JOIN `label` AS l ON l.id = e.label_id
											LEFT JOIN `color` AS c ON c.id = l.user_color_id
											WHERE  e.user_id = ' . $this->user->identity->id . '
											ORDER BY e.start');
   
        $events = array();
		foreach ($result as $key => $event) {
			$events[$key]['id'] = $event['id'];
			$events[$key]['name'] = $event['name'];
			$events[$key]['startDay'] = $event['startDay'];
			$events[$key]['startTime'] = $event['startTime'];
			$events[$key]['endDay'] = $event['endDay'];
			$events[$key]['endTime'] = $event['endTime'];
			$events[$key]['label'] = $event['label'];
			$events[$key]['label_color'] = $event['label_color'];
		}
		return $events;
	}

	public function addNewEvent($eventName, $startDay, $endDay, $startTime, $endTime, $labelId) {
		$start = substr($startDay, 6) . '-' . substr($startDay, 3, 2) . '-' . substr($startDay, 0, 2) . ' ' . $startTime;
		$end = substr($endDay, 6) . '-' . substr($endDay, 3, 2) . '-' . substr($endDay, 0, 2) . ' ' . $endTime;
		
		$this->database->table(self::EVENT_TABLE_NAME)->insert(array(
			self::EVENT_COLUMN_NAME => $eventName,
			self::EVENT_COLUMN_START => $start,
			self::EVENT_COLUMN_END => $end,
			self::EVENT_COLUMN_USER_ID => $this->user->identity->id,
			self::EVENT_COLUMN_LABEL_ID => $labelId
		));
	}
}