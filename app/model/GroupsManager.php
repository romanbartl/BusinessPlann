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

	public function getGroups($id = null) {
		$query = 'SELECT g.id AS id,
					g.name AS name,
					c.color AS color,
					g.leader_user_id AS leader_id
					FROM `group` AS g, `user_is_in_group` AS uig, `color` AS c
					WHERE g.id = uig.group_id AND c.id = g.color_id AND uig.user_id = ' . $this->user->identity->id;

		if($id) {
			if(!preg_match('!^[0-9]{1,}$!', $id)) 
				return false;

			$query .= ' AND g.id = ' . $id;
			$group = $this->database->query($query)->fetch();

			if($group['leader_id'] != $this->user->identity->id)
				return false;

			$group['users'] = $this->getUsersInGroup($group['id']);
			return $group;
		}

		$result = $this->database->query($query);
   
        $groups = array();
		foreach ($result as $key => $group) {
			$groups[$key]['id'] = $group['id'];
			$groups[$key]['name'] = $group['name'];
			$groups[$key]['color'] = $group['color'];
			$groups[$key]['leader_id'] = $group['leader_id'];
			$groups[$key]['users'] = $this->getUsersInGroup($group['id']);
		}
		
		return $groups;
	}

	private function getUsersInGroup($groupId){
		$query = 'SELECT u.id AS id, CONCAT(u.name, " " , u.surname)AS name, u.email AS email
					FROM `user_is_in_group` AS uig, `user` AS u
					WHERE uig.user_id = u.id AND uig.group_id = ' . $groupId;
		$result = $this->database->query($query);

		$users = array();
		foreach ($result as $key => $user) {
			$users[$key]['id'] = $user['id'];
			$users[$key]['name'] = $user['name'];
			$users[$key]['email'] = $user['email'];
		}

		return $users;
	}

	public function userIsAlreadyInGroup($groupId, $email) {
		$userId = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $email)
														            ->select(self::USER_COLUMN_ID)->fetch();
		
		$result = $this->database->query('SELECT `group_id` FROM `user_is_in_group` WHERE `user_id` = ' . $userId . ' AND `group_id` = ' . $groupId)->fetchAll();

		foreach ($result as $user) {
			if($user['group_id'])
				return true;
		}

		return false;
	}

	public function addGroupUser($groupId, $email){
		$userId = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $email)
														            ->select(self::USER_COLUMN_ID)->fetch();

		$this->database->table(self::USER_GROUP_TABLE_NAME)->insert(array(
				self::USER_GROUP_COLUMN_USER_ID => $userId,
				self::USER_GROUP_COLUMN_GROUP_ID => $groupId
		));
	}

	public function deleteGroup($groupId){
		$this->database->table(self::USER_GROUP_TABLE_NAME)->where(self::USER_GROUP_COLUMN_GROUP_ID, $groupId)
		                                                   ->delete();
		$this->database->table(self::GROUP_TABLE_NAME)->where(self::GROUP_COLUMN_ID, $groupId)->delete();
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
				self::USER_GROUP_COLUMN_GROUP_ID => $group_id
		));

		foreach ($users as $user) {
			if($user_id = $this->database->table(self::USER_TABLE_NAME)->where(self::USER_COLUMN_EMAIL, $user)
														            ->select(self::USER_COLUMN_ID)->fetch()){

			$this->database->table(self::USER_GROUP_TABLE_NAME)->insert(array(
				self::USER_GROUP_COLUMN_USER_ID => $user_id,
				self::USER_GROUP_COLUMN_GROUP_ID => $group_id
			));
	}
		}
	}

	public function changeGroupName($newName, $groupId) {
		$this->database->table(self::GROUP_TABLE_NAME)->where(self::GROUP_COLUMN_ID, $groupId)
    													 ->update(array(self::GROUP_COLUMN_NAME => $newName));
	}

	public function checkViewCorrect($view) {
		if($view == 'add' || $view == 'edit' || $view = 'show')
			return true;
		return false;
	}
}