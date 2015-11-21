<?php

namespace DbTests\Fixtures;

use Db\Entities\User;
use Db\Security\IPasswordHasher;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;


class UserFixture extends AbstractFixture
{

	/** @var IPasswordHasher */
	private $passwordHasher;


	public function __construct(IPasswordHasher $passwordHasher = NULL)
	{
		$this->passwordHasher = $passwordHasher;
	}


	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
		$user = new User('me@jiripudil.cz', 'mySuperSecretPassword', $this->passwordHasher);

		$manager->persist($user);
		$manager->flush();
	}

}
