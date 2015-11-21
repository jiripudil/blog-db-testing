<?php

namespace DbTests;

use Doctrine\Common\DataFixtures as Fixtures;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Migration;
use Kdyby\Doctrine\EntityManager;
use Tester;
use Tracy;
use Zenify\DoctrineMigrations\Configuration\Configuration as MigrationsConfiguration;


trait DatabaseSetup
{

	use CompiledContainer {
		createContainer as parentCreateContainer;
	}


	/**
	 * @var string|NULL
	 */
	protected $databaseName;


	protected function createContainer()
	{
		$container = $this->parentCreateContainer();

		/** @var ConnectionMock $db */
		$db = $container->getByType(Connection::class);
		if ( ! $db instanceof ConnectionMock) {
			throw new \LogicException("Connection service should be instance of ConnectionMock");
		}

		$db->onConnect[] = function (Connection $db) use ($container) {
			if ($this->databaseName !== NULL) {
				return;
			}

			try {
				$this->setupDatabase($db);

			} catch (\Exception $e) {
				Tester\Assert::fail($e->getMessage());
			}
		};

		return $container;
	}


	private function setupDatabase(Connection $db)
	{
		$this->databaseName = 'db_tests_' . getmypid();

		$this->dropDatabase($db);
		$this->createDatabase($db);

		$db->transactional(function (Connection $db) {
			$this->runMigrations($db);
			$this->runFixtures($db);
		});

		register_shutdown_function(function () use ($db) {
			$this->dropDatabase($db);
		});
	}


	private function runMigrations(Connection $db)
	{
		$container = $this->getContainer();

		/** @var MigrationsConfiguration $migrationsConfig */
		$migrationsConfig = $container->getByType(MigrationsConfiguration::class);
		$migrationsConfig->__construct($container, $db); // necessary to disable output
		$migrationsConfig->registerMigrationsFromDirectory($migrationsConfig->getMigrationsDirectory());
		$migration = new Migration($migrationsConfig);

		$migration->migrate($migrationsConfig->getLatestVersion());
	}


	private function runFixtures(Connection $db)
	{
		$container = $this->getContainer();

		$fixtures = [];
		$rawFixtures = (new Fixtures\Loader())->loadFromDirectory(__DIR__ . '/../fixtures');
		foreach ($rawFixtures as $fixture) {
			$fixtureClassName = get_class($fixture);
			$fixtures[] = $container->createInstance($fixtureClassName);
		}

		$executor = new Fixtures\Executor\ORMExecutor($container->getByType(EntityManager::class));
		$executor->execute($fixtures, TRUE);
	}


	private function createDatabase(Connection $db)
	{
		$db->exec("CREATE DATABASE {$this->databaseName}");
		$this->connectToDatabase($db, $this->databaseName);
	}


	private function dropDatabase(Connection $db)
	{
		if (getenv('DB') === 'pgsql') {
			$this->connectToDatabase($db, 'postgres');
		}

		$db->exec("DROP DATABASE IF EXISTS {$this->databaseName}");
	}


	private function connectToDatabase(Connection $db, $databaseName)
	{
		$db->close();
		$db->__construct(
			['dbname' => $databaseName] + $db->getParams(),
			$db->getDriver(),
			$db->getConfiguration(),
			$db->getEventManager()
		);
		$db->connect();
	}

}
