<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;

class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
	protected $em;
	protected $classmetadata;
	protected $repository;

	public function setUp()
	{
		$this->em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
		$this->classmetadata = new ClassMetadata('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User');
		$this->repository = new EntityRepository($this->em, $this->classmetadata);
	}

	public function testCreateObject()
	{
		$object = $this->repository->create();
		$this->assertInstanceOf('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', $object);
	}

	public function testUpdateObject()
	{
		$object = new User();
		$this->em->expects($this->once())
		->method('persist')
		->with($object);
		$this->em->expects($this->once())
		->method('flush')
		->with($object);
		$this->repository->update($object);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testUpdateObjectWrongType()
	{
		$object = new \stdClass();
		$this->em->expects($this->never())
		->method('persist');
		$this->em->expects($this->never())
		->method('flush');

		$this->repository->update($object);
	}

	/**
	 * @expectedException Exception
	 */
	public function testUpdateObjectFailed()
	{
		$object = new User();
		$this->em->expects($this->once())
		->method('persist')
		->with($object);
		$this->em->expects($this->once())
		->method('flush')
		->with($object)
		->willThrowException(new \Exception());

		$this->repository->update($object);
	}

	public function testDeleteObject()
	{
		$object = new User();
		$this->em->expects($this->once())
		->method('remove')
		->with($object);
		$this->em->expects($this->once())
		->method('flush')
		->with($object);
		$this->repository->delete($object);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testDeleteObjectWrongType()
	{
		$object = new \stdClass();
		$this->em->expects($this->never())
		->method('delete');
		$this->em->expects($this->never())
		->method('flush');

		$this->repository->delete($object);
	}

	/**
	 * @expectedException Exception
	 */
	public function testDeleteObjectFailed()
	{
		$object = new User();
		$this->em->expects($this->once())
		->method('remove')
		->with($object);
		$this->em->expects($this->once())
		->method('flush')
		->with($object)
		->willThrowException(new \Exception());

		$this->repository->delete($object);
	}
}

