<?php

namespace DbTests\Fixtures;

use Db\Entities\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;


class UserFixture extends AbstractFixture
{

	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
		$user = new User('me@jiripudil.cz', 'mySuperSecretPassword');

		$manager->persist($user);
		$manager->flush();
	}

}
