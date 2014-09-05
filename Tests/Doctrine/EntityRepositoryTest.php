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
use Doctrine\ORM\QueryBuilder;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository
 * @covers vierbergenlars\Bundle\RadRestBundle\Doctrine\QueryBuilderPageDescription
 */
class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $classmetadata;
    protected $repository;

    public function setUp()
    {
        if(!class_exists('Doctrine\ORM\Mapping\ClassMetadata'))
            return $this->markTestSkipped('Doctrine ORM is not installed');
        $this->em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $this->classmetadata = new ClassMetadata('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User');
        $this->classmetadata->reflClass = new \ReflectionClass($this->classmetadata->name);
        $this->repository = new EntityRepository($this->em, $this->classmetadata);
    }

    public function testGetPageDescription()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
        ->setConstructorArgs(array($this->em))
        ->enableProxyingToOriginalMethods()
        ->getMock();
        $this->em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->atLeastOnce())->method('select');

        $this->assertInstanceOf('vierbergenlars\Bundle\RadRestBundle\Doctrine\QueryBuilderPageDescription', $this->repository->getPageDescription());
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
