<?php
namespace App\Model;

use Nette,
	Nette\Security\User;

class GroupsManager extends BaseManager
{
	private $database;
	private $user;

	public function __construct(Nette\Database\Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}

	public function getGroups() {
		$result = $this->database->query('SELECT g.id AS id,
											g.name AS name,
											c.color AS color
											FROM `group` AS g, `user_is_in_group` AS uig, `color` AS c
											WHERE g.id = uig.group_id AND c.id = g.color_id AND uig.user_id = ' . $this->user->identity->id);
   
        $groups = array();
		foreach ($result as $key => $group) {
			$groups[$key]['id'] = $group['id'];
			$groups[$key]['name'] = $group['name'];
			$groups[$key]['color'] = $group['color'];
		}
		
		return $groups;
	}

}