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
		$router[] = new Route('<presenter>/<action>[/view]', 'Businessplann:default');

		/*$router[] = new Route('settings', 'Settings:default');
		$router[] = new Route('lagels', 'Settings:labels');
		$router[] = new Route('groups', 'Settings:groups');

		
		/*$router[] = new Route('<agenda | day | month | week>', array(
						    'presenter' => 'Businessplann',
						    'action' => 'default',
						    'view' => NULL,
						));*/
		
		/*$router[] = new Route('<presenter>/<action>/?view=<view>', array(
		    'presenter' => 'Businessplann',
		    'action' => 'default'
		));*/
		
		return $router;
	}

}
