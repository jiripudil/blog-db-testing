<?php

namespace DbTests;

use Nette;


trait CompiledContainer
{

	/** @var Nette\DI\Container */
	private $container;


	protected function getContainer()
	{
		if ($this->container === NULL) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}


	protected function createContainer()
	{
		$configurator = new Nette\Configurator();

		$configurator->setTempDirectory(dirname(TEMP_DIR)); // shared container for performance purposes
		$configurator->setDebugMode(FALSE);

		$configurator->addParameters([
			'appDir' => __DIR__ . '/../../app',
		]);

		$configurator->addConfig(__DIR__ . '/../../app/config/config.neon');
		$configurator->addConfig(__DIR__ . '/../../app/config/' . getenv('DB') . '.neon');
		$configurator->addConfig(__DIR__ . '/../config/tests.neon');

		return $configurator->createContainer();
	}

}
