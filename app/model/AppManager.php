<?php
namespace App\Model;

use Nette,
	Nette\Security\User;

class AppManager extends BaseManager
{
	private $database;
	private $user;
	private $groupsManager;

	public function __construct(Nette\Database\Context $database, User $user, GroupsManager $groupsManager) {
		$this->database = $database;
		$this->user = $user;
		$this->groupsManager = $groupsManager;
	}

	public function getEvents() {
		$eventsIds = array();

		$query = 'SELECT `id` AS id 
				  FROM `event` AS e
				  WHERE e.user_id = ' . $this->user->identity->id;

		$result = $this->database->query($query)->fetchPairs('id', 'id');

		foreach ($result as $id) {
			$eventsIds[] = $id;
		}

		$query = 'SELECT ghe.event_id AS id
				  FROM `user_is_in_group` AS uig
				  LEFT JOIN `group_has_event` AS ghe ON ghe.group_id = uig.group_id
				  WHERE uig.user_id = ' . $this->user->identity->id;
		
		$result = $this->database->query($query)->fetchPairs('id', 'id');

		foreach ($result as $id) {
			if(isset($id))
				$eventsIds[] = $id;
		}

		$events = array();

		$query = 'SELECT e.id AS id,
				  e.name AS text, 
				  DATE_FORMAT(e.start, "%Y-%m-%d %H-%i") AS start_date,
				  DATE_FORMAT(e.end, "%Y-%m-%d %H-%i") AS end_date

				  FROM `event` AS e ';

		if(count($eventsIds) > 0){
			$query .= 'WHERE (';

			foreach ($eventsIds as $id) {
				$query .= 'e.id = ' . $id . ' OR ';
			}

			$query = substr($query, 0, -4);
		} else 
			return $events;

		$query .= ') AND e.trash != 1 ORDER BY e.start';

		$result = $this->database->query($query)->fetchAll();

		foreach ($result as $key => $event) {
			$label = $this->database->query('SELECT c.color AS label_color
							  FROM `color` AS c, `label` AS l
							  LEFT JOIN `event_has_label` AS ehl ON l.id = ehl.label_id
							  WHERE (c.id = l.user_color_id) AND l.user_id = ' . $this->user->identity->id . ' AND ehl.event_id = ' . $event['id'])->fetch();
			if($label['label_color'])
				$result[$key]['color'] = '#' . $label['label_color'];
			else
				$result[$key]['color'] = '#B3B3B3';

			$result[$key]['textColor'] = 'white';
		}

		$events = array();

		foreach ($result as $key => $event) {
			$events[] = $event;
		}

		return json_encode($events);
	}

	public function getEventById($eventId) {
		$event = $this->database->query('SELECT e.id AS id, 
							e.user_id AS owner,
							e.name AS name, 
							DATE_FORMAT(e.start, "%d.%m.%Y") AS startDay,
							DATE_FORMAT(e.start, "%H:%i") AS startTime,
							DATE_FORMAT(e.end, "%d.%m.%Y") AS endDay,
							DATE_FORMAT(e.end, "%H:%i") AS endTime,				  
							e.place AS place,
				  			e.description AS description
							
							FROM `event` AS e 
							
							WHERE e.id = ' . $eventId)->fetch();

		$label = $this->database->query('SELECT l.id AS id, c.color AS label_color
						  FROM `color` AS c, `label` AS l
						  LEFT JOIN `event_has_label` AS ehl ON l.id = ehl.label_id
						  WHERE (c.id = l.user_color_id) AND l.user_id = ' . $this->user->identity->id . ' AND ehl.event_id = ' . $eventId)->fetch();
		
		$event['labelId'] = $label['id'];
		$event['label_color'] = $label['label_color'];

		return $event;
	}

	public function eventInGroups($eventId) {
		return $this->database->query('SELECT g.id AS id, g.name AS name, c.color AS color
										FROM `group` AS g, `group_has_event` AS ghe, `color` AS c
										WHERE (c.id = g.color_id) AND (g.id = ghe.group_id AND ghe.event_id = ' . $eventId . ')')->fetchAll();
	}

	public function addNewEvent($eventName, $startDay, $endDay, $startTime, $endTime, $labelId) {
		$start = substr($startDay, 6) . '-' . substr($startDay, 3, 2) . '-' . substr($startDay, 0, 2) . ' ' . $startTime;
		$end = substr($endDay, 6) . '-' . substr($endDay, 3, 2) . '-' . substr($endDay, 0, 2) . ' ' . $endTime;
		
		$row = $this->database->table(self::EVENT_TABLE_NAME)->insert(array(
			self::EVENT_COLUMN_NAME => $eventName,
			self::EVENT_COLUMN_START => $start,
			self::EVENT_COLUMN_END => $end,
			self::EVENT_COLUMN_USER_ID => $this->user->identity->id,
			self::EVENT_COLUMN_TRASH => 0
		));
		
		$eventId = $row->id;

		if($labelId != NULL) {
			$this->database->table(self::EVENT_HAS_LABEL_TABLE_NAME)->insert(array(
				self::EVENT_HAS_LABEL_COLUMN_EVENT_ID => $eventId,
				self::EVENT_HAS_LABEL_COLUMN_LABEL_ID => $labelId
			));
		} 

		return $eventId;
	}

	public function editEvent($eventId, $eventName, $startDay, $endDay, $startTime, $endTime, $place, $description) {
		$start = substr($startDay, 6) . '-' . substr($startDay, 3, 2) . '-' . substr($startDay, 0, 2) . ' ' . $startTime;
		$end = substr($endDay, 6) . '-' . substr($endDay, 3, 2) . '-' . substr($endDay, 0, 2) . ' ' . $endTime;
		
		$this->database->table(self::EVENT_TABLE_NAME)->where(self::EVENT_COLUMN_ID, $eventId)
														->update(array(
															self::EVENT_COLUMN_NAME => $eventName,
															self::EVENT_COLUMN_START => $start,
															self::EVENT_COLUMN_END => $end,
															self::EVENT_COLUMN_PLACE => $place,
															self::EVENT_COLUMN_DESCRIPTION => $description,
														));
	}

	public function changeLabel($eventId, $labelId) {
		$query = 'SELECT l.id AS id
				  FROM `label` AS l
				  LEFT JOIN `event_has_label` AS ehl ON l.id = ehl.label_id
				  WHERE l.user_id = ' . $this->user->identity->id . ' AND ehl.event_id = ' . $eventId;
		
		$lastLabelId = $this->database->query($query)->fetch();			

		if($labelId == NULL && isset($lastLabelId->id)) {
			$this->database->table(self::EVENT_HAS_LABEL_TABLE_NAME)
				->where(self::EVENT_HAS_LABEL_COLUMN_LABEL_ID, $lastLabelId->id)
				->where(self::EVENT_HAS_LABEL_COLUMN_EVENT_ID, $eventId)
				->delete();
			
			return TRUE;	
		}

		if(isset($lastLabelId->id)) {
			$this->database->table(self::EVENT_HAS_LABEL_TABLE_NAME)
				->where(self::EVENT_HAS_LABEL_COLUMN_LABEL_ID, $lastLabelId->id)
				->where(self::EVENT_HAS_LABEL_COLUMN_EVENT_ID, $eventId)
				->update(array(self::EVENT_HAS_LABEL_COLUMN_LABEL_ID => $labelId));			
		} else {
			$this->database->table(self::EVENT_HAS_LABEL_TABLE_NAME)->insert(array(
				self::EVENT_HAS_LABEL_COLUMN_EVENT_ID => $eventId,
				self::EVENT_HAS_LABEL_COLUMN_LABEL_ID => $labelId
			));			
		}
	}

	public function deleteEventFromGroup($eventId, $groupId) {
		$this->database->table(self::GROUP_HAS_EVENT_TABLE_NAME)
													  ->where(self::GROUP_HAS_EVENT_COLUMN_EVENT_ID, $eventId)
													  ->where(self::GROUP_HAS_EVENT_COLUMN_GROUP_ID, $groupId)
													  ->delete();
	}

	public function deleteEvent($eventId) {
		$this->database->table(self::EVENT_TABLE_NAME)
								->where(self::EVENT_COLUMN_ID, $eventId)
								->update(array(self::EVENT_COLUMN_TRASH => 1));
	}

	public function checkViewCorrect($view) {	
		if($view == 'day' || $view == 'week' || $view == 'month' || $view == 'agenda' || $view == 'edit')
			return TRUE;
		return FALSE;
	}

	public function checkDateCorrect($date){
		if(\DateTime::createFromFormat('Y-m-d', $date))
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
				$date['date'] = $defaultDate->format('j. n.');
				$date['name'] = $this->czechDay($defaultDate->format('w'));
				break;
		}
		return $date;
	}

	public function shareInGroups($eventId, $groupsIds) {
		foreach($groupsIds as $key => $id) {
			$this->database->table(self::GROUP_HAS_EVENT_TABLE_NAME)
							->insert(array(
								self::GROUP_HAS_EVENT_COLUMN_EVENT_ID => $eventId,
								self::GROUP_HAS_EVENT_COLUMN_GROUP_ID => $id
							));
		}
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

    public function getGroupsDifference($eventId) {  
    	$eventInGroups = $this->eventInGroups($eventId);
    	$groups = $this->groupsManager->getGroups();

        $groupsTmp = array();
        $eventInGroupsTmp = array();
        
        foreach ($groups as $group) {
            $groupsTmp[] = $group['id'];   
        }

        foreach ($eventInGroups as $eventInGroup) {
            $eventInGroupsTmp[] = $eventInGroup['id'];   
        }

        $arrayDif = array_diff($groupsTmp, $eventInGroupsTmp);
        $differentGroups = array();
        
        foreach ($groups as $group) {
            foreach ($arrayDif as $key) {
                if($key == $group['id']) {
                    $differentGroups[$key]['id'] = $group['id'];
                    $differentGroups[$key]['name'] = $group['name'];
                }
            }     
        }

        return $differentGroups;
    }

    public function userHasPermition($eventId) {
    	$share = $this->database->query('SELECT ghe.event_id AS id
					FROM `user_is_in_group` AS uig
					LEFT JOIN `group_has_event` AS ghe ON ghe.group_id = uig.group_id
					WHERE uig.user_id = ' . $this->user->identity->id . ' AND ghe.event_id = ' . $eventId)->fetch();
    	
    	if($share != NULL)
    		return TRUE;
    	
    	$owner = $this->database->table(self::EVENT_TABLE_NAME)->where(self::EVENT_COLUMN_ID, $eventId)
    					->where(self::EVENT_COLUMN_USER_ID, $this->user->identity->id)
    					->select(self::EVENT_COLUMN_USER_ID)->fetch();

    	if(!$owner)
    		return FALSE;


    	return TRUE;
    }
}