<?php

namespace Db\Entities;

use Db\Security\IPasswordHasher;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Object;


/**
 * @ORM\Entity()
 * @ORM\Table(name="user_account", uniqueConstraints={
 *   @ORM\UniqueConstraint(columns={"email"})
 * })
 */
class User extends Object
{

	use MagicAccessors;

	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $password;


	/**
	 * @param string $email
	 * @param string $password
	 * @param IPasswordHasher $hasher
	 */
	public function __construct($email, $password, IPasswordHasher $hasher)
	{
		$this->email = $email;
		$this->password = $hasher->hash($password);
	}


	/**
	 * @return int
	 */
	final public function getId()
	{
		return $this->id;
	}


	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}


	/**
	 * @param string $password
	 * @param IPasswordHasher $hasher
	 * @return bool
	 */
	public function verifyPassword($password, IPasswordHasher $hasher)
	{
		return $hasher->verify($password, $this->password);
	}

}
