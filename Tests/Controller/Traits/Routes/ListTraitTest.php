<?php
/**
 * This file is part of the RadRest package.
 *
 * (c) Lars Vierbergen <vierbergenlars@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vierbergenlars\Bundle\RadRestBundle\Tests\Controller\Traits\Routes;

use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use vierbergenlars\Bundle\RadRestBundle\View\View;
use vierbergenlars\Bundle\RadRestBundle\Tests\Fixtures\Pagination\ArrayPageDescription;

/**
 * @covers vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ListTrait
 */
class ListTraitTest extends \PHPUnit_Framework_TestCase
{
    private $frontendManager;

    private $listTrait;

    protected function setUp()
    {
        if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4)
            $this->markTestSkipped('PHP 5.4 required to use traits');

        $this->frontendManager = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Manager\FrontendManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->listTrait = $this->getMockBuilder('vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\ListTrait')
            ->enableProxyingToOriginalMethods()
            ->getMockForTrait();

        $this->listTrait->expects($this->once())
            ->method('getFrontendManager')
            ->with()
            ->willReturn($this->frontendManager);

        $this->listTrait->expects($this->once())
            ->method('handleView')
            ->with($this->anything())
            ->willReturnArgument(0);

        $this->listTrait->expects($this->once())
            ->method('getSerializationGroups')
            ->with('cget')
            ->willReturn(array('Default', 'list'));
    }

    public function testListArray()
    {
        $this->frontendManager->expects($this->once())
            ->method('getList')
            ->with(true)
            ->willReturn($users = User::createArray(16));

        $request = new Request();
        $request->query->set('page', 2);

        $view = $this->listTrait->cgetAction($request);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($users, $view->getData());
        $this->assertEquals(array('Default', 'list'), $view->getSerializationContext()->attributes->get('groups')->get());
    }

    public function testListPageDescription()
    {
        $pageDescription = new ArrayPageDescription($users = User::createArray(16));
        $this->frontendManager->expects($this->once())
            ->method('getList')
            ->with(true)
            ->willReturn($pageDescription);

        $this->listTrait->expects($this->once())
            ->method('getPagination')
            ->with($pageDescription, 2)
            ->willReturn($thisPage = $pageDescription->getSlice(10, 10));

        $request = new Request();
        $request->query->set('page', 2);

        $view = $this->listTrait->cgetAction($request);

        $this->assertTrue($view instanceof View);
        $this->assertEquals($thisPage, $view->getData());
        $this->assertEquals(array('Default', 'list'), $view->getSerializationContext()->attributes->get('groups')->get());
    }
}
