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

	/**
	 * Non-public property w/o getter
	 * @see vierbergenlars\Bundle\RadRestBundle\Tests\Twig\ObjectExtension
	 */
	protected $password;

	/**
	 * Public property w/o getter
	 * @see vierbergenlars\Bundle\RadRestBundle\Tests\Twig\ObjectExtension
	 */
	public $nose;

	/**
	 * Non-public property w/ getter
	 * @see vierbergenlars\Bundle\RadRestBundle\Tests\Twig\ObjectExtension
	 */
	private $dead = false;

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

	/**
	 * Non-public property w/ getter
	 * @see vierbergenlars\Bundle\RadRestBundle\Tests\Twig\ObjectExtension
	 */
	public function isDead()
	{
	    return $this->dead;
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

	public static function createArray($num = 10) {
	    $a = array();
	    for($i = 0; $i < $num; $i++) {
	        $a[] = self::create(chr(mt_rand(97, 122)).chr(mt_rand(97, 122)).chr(mt_rand(97, 122)), $i);
	    }
	    return $a;
	}

	/**
	 * @see vierbergenlars\Bundle\RadRestBundle\Tests\Twig\ObjectExtension
	 */
	public function __toString()
	{
	    return $this->username;
	}
}
