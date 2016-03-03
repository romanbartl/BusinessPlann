<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;		
		$router[] = new Route('app/event[/<id>]', array('presenter' => 'App', 'action' => 'event'));
		$router[] = new Route('app/<view>[/<date>]', array('presenter' => 'App', 'action' => 'default'));
		$router[] = new Route('settings/groups/<view>[/<id>]', array('presenter' => 'Settings', 'action' => 'groups'));
		$router[] = new Route('<presenter>/<action>', array('presenter' => 'Home', 'action' => 'default'));
	
		return $router;
	}

}
