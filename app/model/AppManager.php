<?php
namespace App\Model;

use Nette,
	Nette\Security\User;

class AppManager extends BaseManager
{
	private $database;
	private $user;

	public function __construct(Nette\Database\Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}

	public function getEvents($format, $viewDate) {
		$events = array();

		$query = 'SELECT e.id AS id, e.name AS name, 
				  DATE_FORMAT(e.start, "%Y-%c-%d") AS startDay,
				  DATE_FORMAT(e.start, "%H:%i") AS startTime,
				  DATE_FORMAT(e.end, "%Y-%c-%d") AS endDay,
				  DATE_FORMAT(e.end, "%H:%i") AS endTime,
				  l.name AS label,
				  c.color AS label_color
				  FROM `event` AS e
				  LEFT JOIN `label` AS l ON l.id = e.label_id
				  LEFT JOIN `color` AS c ON c.id = l.user_color_id
				  WHERE  e.user_id = ' . $this->user->identity->id . ' 
				  ORDER BY e.start';

		$result = $this->database->query($query);

		switch ($format) {
			case 'day':
				foreach ($result as $key => $event) {
					$end = new \DateTime($event['endDay']);
					$end = $end->modify('+1 day');
					$daterange = new \DatePeriod(new \DateTime($event['startDay']), new \DateInterval('P1D'), $end);
					
					foreach ($daterange as $date) {
						if($date->format('Y-m-d') == $viewDate->format('Y-m-d'))
							$events[$event['id']] = $event;
					}
				}
				return $events;

			default:
				foreach ($result as $key => $event) {
					$end = new \DateTime($event['endDay']);
					$end = $end->modify('+1 day');
					$daterange = new \DatePeriod(new \DateTime($event['startDay']), new \DateInterval('P1D'), $end);
				
					foreach ($daterange as $date) {
						switch ($format) {
							case 'week':
								$condition = $date->format('Y-W') == $viewDate->format('Y-W');
								break;
							case 'month':
								$condition = $date->format('Y-m') == $viewDate->format('Y-m');
								break;
							case 'agenda':
								$condition = $date->format('Y-m-d') >= $viewDate->format('Y-m-d');
								break;
						}
						if($condition)
							$events[$date->format('Y-m-d')][$event['id']] = $event;	
					}			 
				}
				return $events;
		}
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

	public function checkViewCorrect($view) {	
		if($view == 'day' || $view == 'week' || $view == 'month' || $view == 'agenda' || $view == 'edit')
			return TRUE;
		return FALSE;
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

				//$monday = date('w', $defaultDate);
				//$sunday = date('d.m.Y', strtotime($monday . '+1 week -1 day'));
				$date = '';
				break;
			case 'month':
				$date = $this->czechMonth($defaultDate->format('n')) . 
						' ' . $defaultDate->format('Y');
				break;
			case 'agendaLink':
				$date = $defaultDate->format('d.m.Y');
				break;
		}
		return $date;
	}

	public function getNewDate($date) {
		return new \DateTime($date);
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