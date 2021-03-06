<?php

namespace App\Model;

use Nette,
	Nette\Security\User;



class LabelsManager extends BaseManager
{
	private $database;
	private $user;

	public function __construct(Nette\Database\Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}

	public function getLabels() {
		$result = $this->database->table(self::LABEL_TABLE_NAME)->where(self::LABEL_COLUMN_USER_ID, $this->user->identity->id)
															    ->fetchAll();
   
        $labels = array();
		foreach ($result as $key => $label) {
			$labels[$key]['id'] = $label['id'];
			$labels[$key]['name'] = $label['name'];
			$labels[$key]['color'] = $result[$key]['color_id'][self::COLOR_COLUMN_COLOR];
		}
		
		return $labels;
	}

	public function addNewLabel($labelName, $colorId) {
		$this->database->table(self::LABEL_TABLE_NAME)->insert(array(
			self::LABEL_COLUMN_NAME => $labelName,
			self::LABEL_COLUMN_USER_ID => $this->user->identity->id,
			self::LABEL_COLUMN_COLOR => $colorId
		));
	}

	public function removeLabel($labelId) {
		$this->database->query('DELETE FROM ' . self::LABEL_TABLE_NAME . ' WHERE ' . self::LABEL_COLUMN_ID . ' = ' . $labelId);
		
		$this->database->table(self::EVENT_TABLE_NAME)->where(self::EVENT_COLUMN_LABEL_ID, $labelId)
													 ->update(array(self::EVENT_COLUMN_LABEL_ID => NULL));
	}

	public function editLabelName($labelId, $labelName) {
		$this->database->table(self::LABEL_TABLE_NAME)->where(self::LABEL_COLUMN_ID, $labelId)
													 ->update(array(self::LABEL_COLUMN_NAME => $labelName));
	}

	public function editLabelColor($labelId, $colorId) {
		$this->database->table(self::LABEL_TABLE_NAME)->where(self::LABEL_COLUMN_ID, $labelId)
													  ->update(array(self::LABEL_COLUMN_COLOR => $colorId));
	}
}