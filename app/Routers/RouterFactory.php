<?php

namespace Db\Routers;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Object;


class RouterFactory extends Object
{

	/**
	 * @return IRouter
	 */
	public function create()
	{
		return new RouteList();
	}

}
