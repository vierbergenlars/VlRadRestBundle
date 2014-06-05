<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 *
 */
class User
{
	/**
	 * @ORM\Id
	 *
	 */
	private $id;
	/**
	 * @ORM\Column(type="text")
	 */
	private $username;
	
	private $email;

	public function getId()
	{
		return $this->id;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	public function getEmail()
	{
	    return $this->email;
	}
	
	public function setEmail($email)
	{
	    $this->email = $email;
	}

	public static function create($username, $id = null)
	{
		$user = new self();
		$user->setUsername($username);
		$user->setEmail($username.'@example.com');

		$refl = new \ReflectionClass($user);
		$prop = $refl->getProperty('id');
		$prop->setAccessible(true);
		$prop->setValue($user, $id !== null?$id:mt_rand());
		$prop->setAccessible(false);
		return $user;
	}
}
