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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository
 */
class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $classmetadata;
    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
        ->disableOriginalConstructor()
        ->getMock();
        $this->classmetadata = new ClassMetadata('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User');
        $this->classmetadata->reflClass = new \ReflectionClass($this->classmetadata->name);
        $this->repository = new EntityRepository($this->em, $this->classmetadata);
    }

    public function testGetPage()
    {
        $paginator = $this->getMock('Knp\Component\Pager\Paginator');
        $this->repository->setPaginator($paginator);

        $this->em->expects($this->any())->method('createQueryBuilder')->willReturn(new QueryBuilder($this->em));

        $paginator->expects($this->once())->method('paginate')
        ->with($this->anything(), 8, 20)
        ->willReturn($pagination = $this->getMock('Knp\Component\Pager\Pagination\PaginationInterface'));

        $this->assertEquals($pagination, $this->repository->getPage(8, 20));
    }

    /**
     * @expectedException LogicException
     */
    public function testGetPageNoPaginator()
    {
        $this->repository->getPage(1, 10);
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
     * @expectedException LogicException
     */
    public function testUpdateObjectNoObject()
    {
        $this->em->expects($this->never())
        ->method('persist');
        $this->em->expects($this->never())
        ->method('flush');

        $this->repository->update('abcde');
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

    /**
     * @expectedException LogicException
     */
    public function testDeleteObjectNoObjectPassed()
    {
        $this->em->expects($this->never())
        ->method('delete');
        $this->em->expects($this->never())
        ->method('flush');

        $this->repository->delete(123);
    }
}

