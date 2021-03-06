<?php

/**
 * @testCase
 * @multiple 50
 */

namespace DbTests;

use Db\Entities\User;
use Db\Security\IPasswordHasher;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Kdyby\Doctrine\EntityManager;
use Tester\Assert;
use Tester\TestCase;


require_once __DIR__ . '/../../bootstrap.php';


class UserTest extends TestCase
{

	use DatabaseSetup;


	protected function setUp()
	{
		parent::setUp();
		$this->getEm()->getConnection()->connect();
	}


	public function testUser()
	{
		$user = $this->getEm()->getRepository(User::class)->findOneBy(['email' => 'me@jiripudil.cz']);
		$hasher = $this->getContainer()->getByType(IPasswordHasher::class);

		Assert::type(User::class, $user);
		Assert::same('me@jiripudil.cz', $user->getEmail());
		Assert::true($user->verifyPassword('mySuperSecretPassword', $hasher));
		Assert::false($user->verifyPassword('myWrongPassword', $hasher));
	}


	public function testDatabase()
	{
		Assert::type(
			getenv('DB') === 'mysql' ? MySqlPlatform::class : PostgreSqlPlatform::class,
			$this->getEm()->getConnection()->getDatabasePlatform()
		);
		Assert::match('db_tests_%d%', $this->getEm()->getConnection()->getDatabase());
	}


	public function testIsolation1()
	{
		$em = $this->getEm();
		$hasher = $this->getContainer()->getByType(IPasswordHasher::class);

		$user = new User('john.doe@example.com', 'password', $hasher);
		$em->persist($user)->flush();

		Assert::type(User::class, $em->getRepository(User::class)->findOneBy(['email' => 'john.doe@example.com']));
	}


	public function testIsolation2()
	{
		Assert::null($this->getEm()->getRepository(User::class)->findOneBy(['email' => 'john.doe@example.com']));
	}


	/**
	 * @return EntityManager
	 */
	private function getEm()
	{
		return $this->getContainer()->getByType(EntityManager::class);
	}

}


(new UserTest())->run();
