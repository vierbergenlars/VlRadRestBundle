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

use vierbergenlars\Bundle\RadRestBundle\Doctrine\QueryBuilderPageDescription;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;

class QueryBuilderPageDescriptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var QueryBuilder
     */
    private $queryBuilder;
    private $pageDescription;

    protected function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
        ->disableOriginalConstructor()
        ->getMock();

        $this->queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
        ->setConstructorArgs(array($this->em))
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $this->queryBuilder->select('user')->from('vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User', 'user');
        $this->pageDescription = new QueryBuilderPageDescription($this->queryBuilder);
    }

    public function testGetTotalItemCount()
    {
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
        ->setMethods(array('setFirstResult', 'setMaxResults', 'getSingleScalarResult'))
        ->disableOriginalConstructor()
        ->getMockForAbstractClass();
        $query->expects($this->once())->method('setFirstResult')->willReturnSelf();
        $query->expects($this->once())->method('setMaxResults')->willReturnSelf();
        $query->expects($this->once())->method('getSingleScalarResult')->willReturn(25);

        $this->em->expects($this->any())->method('createQuery')->willReturn($query);

        $this->assertEquals(25, $this->pageDescription->getTotalItemCount());
    }

    public function testGetSlice()
    {
        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
        ->setMethods(array('setFirstResult', 'setMaxResults', 'getResult'))
        ->disableOriginalConstructor()
        ->getMockForAbstractClass();
        $query->expects($this->once())->method('setFirstResult')->with(5)->willReturnSelf();
        $query->expects($this->once())->method('setMaxResults')->with(10)->willReturnSelf();
        $results = array(User::create('a'), User::create('b'), User::create('c'));
        $query->expects($this->once())->method('getResult')->willReturn($results);

        $this->em->expects($this->any())->method('createQuery')->willReturn($query);

        $this->assertSame($results, $this->pageDescription->getSlice(5, 10));
    }
}
