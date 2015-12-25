<?php

namespace App\Model;

use Nette;
use App\Model;


/**
 * Base maneger for all application managers.
 */
abstract class BaseManager extends Nette\Object
{
	const
		USER_TABLE_NAME = 'user',
		USER_COLUMN_ID = 'id',
		USER_COLUMN_NAME = 'name',
		USER_COLUMN_SURNAME = 'surname',
		USER_COLUMN_EMAIL = 'email',
		USER_COLUMN_PASSWORD = 'password',
		USER_COLUMN_SEND_EVENTS = 'send_events',
		USER_COLUMN_SEND_INFO = 'send_info',
		USER_COLUMN_PROFILE_PHOTO = 'profile_photo',
		USER_COLUMN_COLOR = 'color_id',
		USER_COLUMN_ROLE = 'role_id',
		USER_COLUMN_LANGUAGE = 'language_id',

		ROLE_COLUMN_ROLE = 'role',

		COLOR_TABLE_NAME = 'color',
		COLOR_COLUMN_ID = 'id',
		COLOR_COLUMN_COLOR = 'color',

		LABEL_TABLE_NAME = 'label',
		LABEL_COLUMN_ID = 'id',
		LABEL_COLUMN_NAME = 'name',
		LABEL_COLUMN_USER_ID = 'user_id',
		LABEL_COLUMN_COLOR = 'user_color_id',

		EVENT_TABLE_NAME = 'event',
		EVENT_COLUMN_NAME = 'name',
		EVENT_COLUMN_START = 'start',
		EVENT_COLUMN_END = 'end',
		EVENT_COLUMN_USER_ID = 'user_id';
}