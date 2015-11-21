<?php

namespace DbTests;

use Kdyby\Doctrine\Connection;


/**
 * @method onConnect(ConnectionMock $self)
 */
class ConnectionMock extends Connection
{

	public $onConnect = [];


	public function connect()
	{
		if (parent::connect()) {
			$this->onConnect($this);
		}
	}

}
