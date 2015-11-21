<?php

namespace Db\Security;


class NativeHasher implements IPasswordHasher
{

	/**
	 * @param string $password
	 * @return string
	 */
	public function hash($password)
	{
		return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
	}


	/**
	 * @param string $password
	 * @param string $hash
	 * @return bool
	 */
	public function verify($password, $hash)
	{
		return password_verify($password, $hash);
	}

}
