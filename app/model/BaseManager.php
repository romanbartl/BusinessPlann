<?php

namespace App\Model;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BaseManager extends Nette\Object
{
	const
		TABLE_NAME = 'user',
		COLUMN_ID = 'id_user',
		COLUMN_NAME = 'name',
		COLUMN_SURNAME = 'surname',
		COLUMN_EMAIL = 'email',
		COLUMN_PASSWORD = 'password',
		COLUMN_PROFILE_PHOTO = 'profile_photo',
		COLUMN_BG_COLOR = 'bg_color_id_bg_color',
		COLUMN_ROLE = 'role';	
}