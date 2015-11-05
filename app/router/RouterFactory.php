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
		// TODO - když nebude uživatel přihlášen, bude jako výchozí stránka HOME; jinak to bude aplikace
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}

}
