<?php

namespace Db\Security;


interface IPasswordHasher
{

	/**
	 * @param string $password
	 * @return string
	 */
	public function hash($password);


	/**
	 * @param string $password
	 * @param string $hash
	 * @return bool
	 */
	public function verify($password, $hash);

}
