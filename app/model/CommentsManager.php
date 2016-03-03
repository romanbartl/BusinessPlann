<?php
namespace App\Model;

use Nette,
	Nette\Security\User;

class CommentsManager extends BaseManager
{
	private $database;
	private $user;

	public function __construct(Nette\Database\Context $database, User $user) {
		$this->database = $database;
		$this->user = $user;
	}	

	public function getComents($eventId) {
		return $this->database->query('SELECT u.id AS user_id, CONCAT(u.name, " ", u.surname) AS name, 
						col.color AS color, u.profile_photo AS photo, c.content AS content
						FROM `comment` AS c, `user` AS u, `color` AS col
						WHERE u.id = c.user_id AND col.id = u.color_id AND c.event_id = ' . $eventId . ' 
						ORDER BY c.time')->fetchAll();
	}

	public function addComment($content, $eventId) {
		$this->database->table(self::COMMENT_TABLE_NAME)->insert(array(
				self::COMMENT_COLUMN_CONTENT => $content,
				self::COMMENT_COLUMN_TIME => date('Y-m-d H:i:s'),
				self::COMMENT_COLUMN_EVENT_ID => $eventId,
				self::COMMENT_COLUMN_USER_ID => $this->user->identity->id
			));
	}
}
