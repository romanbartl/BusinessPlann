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
   
		foreach ($result as $key => $event) {
			if($event['startDay'] != $event['endDay']) {
				$end = new \DateTime($event['endDay']);
				$end = $end->modify('+1 day');
				$daterange = new \DatePeriod(new \DateTime($event['startDay']), new \DateInterval('P1D'), $end);
				
				foreach ($daterange as $date) 
					$events[$date->format('d.m.Y')][$event['id']] = $event;				
			} else
				$events[$event['startDay']][$event['id']] = $event;	
		}
		return $events;
	}

	public function getEventById($eventId) {
		$event = $this->database->query('SELECT e.id AS id, e.name AS name, 
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
											HAVING e.id = ' . $eventId . '
											ORDER BY e.start')->fetch();
		return $event;
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

	public function getViewByURL() {
		$getArgs = array_keys($_GET);
		foreach ($getArgs as $arg) {
			if($arg == 'day' || $arg == 'week' || $arg == 'month' || $arg == 'agenda'|| $arg == 'edit_event')
				return $arg;	
		}
		return 'day';
	}

	public function getDateByURL() {
		if(isset($_GET['date'])) {
			$date = date_create($_GET['date']);
			if($date)
				return new \DateTime($_GET['date']);
		}
		return new \DateTime();
	}

	public function getFormatedDate($defaultDate, $format) {
		switch($format) {
			case 'day':
			case 'agenda':
			default:
				$date = $this->czechDay($defaultDate->format('w')) . 
						' - ' . $defaultDate->format('j') . 
						'. ' . $this->czechMonth($defaultDate->format('n')) . 
						' ' . $defaultDate->format('Y');
				break;
			case 'week':
				$date = '';
				break;
			case 'month':
				$date = $this->czechMonth($defaultDate->format('n')) . 
						' ' . $defaultDate->format('Y');
				break;
		}
		return $date;
	}

	public function getModifiedDate($date, $format, $modify) {
		if($format == 'agenda')
			$format = 'day';
		$date->modify( $modify . $format );
		return $date->format('Y-m-d');
	}

    private function czechDay($dayNumber) {
    	$days = array('Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota');	
    	return $days[$dayNumber];
	}
	
	private function czechMonth($monthNumber) {
        $months = array(1 => 'leden', 'únor', 'březen', 'duben', 
                                    'květen', 'červen', 'červenec', 'srpen', 
                                    'září', 'říjen', 'listopad', 'prosinec');   
        return $months[$monthNumber];
    }
}