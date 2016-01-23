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
											c.color AS color,
											g.leader_user_id AS leader_id
											FROM `group` AS g, `user_is_in_group` AS uig, `color` AS c
											WHERE g.id = uig.group_id AND c.id = g.color_id AND uig.user_id = ' . $this->user->identity->id);
   
        $groups = array();
		foreach ($result as $key => $group) {
			$groups[$key]['id'] = $group['id'];
			$groups[$key]['name'] = $group['name'];
			$groups[$key]['color'] = $group['color'];
			$groups[$key]['leader_id'] = $group['leader_id'];
		}
		
		return $groups;
	}

	public function addGroup($name, $color_id, $users) {
		$this->database->table(self::GROUP_TABLE_NAME)->insert(array(
				self::GROUP_COLUMN_NAME => $name,
				self::GROUP_COLUMN_COLOR_ID => $color_id,
				self::GROUP_COLUMN_LEADER_ID => $this->user->identity->id
			));

		$group_id = $this->database->table(self::GROUP_TABLE_NAME)->max(self::GROUP_COLUMN_ID);

		$this->database->table(self::USER_GROUP_TABLE_NAME)->insert(array(
				self::USER_GROUP_COLUMN_USER_ID => $this->user->identity->id,
				self::USER_GROUP_COLUMN_GROUP_ID => $group_id,
				self::USER_GROUP_COLUMN_CONFIRM => true
		));

		foreach ($users as $user) {
			if($user_id = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $user)
														            ->select(self::USER_COLUMN_ID)->fetch()){

			$this->database->table(self::USER_GROUP_TABLE_NAME)->insert(array(
				self::USER_GROUP_COLUMN_USER_ID => $user_id,
				self::USER_GROUP_COLUMN_GROUP_ID => $group_id,
				self::USER_GROUP_COLUMN_CONFIRM => false
			));
	}
		}
	}

	public function checkViewCorrect($view) {
		if($view == 'add' || $view == 'edit' || $view = 'show')
			return true;
		return false;
	}
}